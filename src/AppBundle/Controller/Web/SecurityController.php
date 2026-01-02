<?php

namespace AppBundle\Controller\Web;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastEmail = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_email' => $lastEmail,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        throw new \LogicException('This should never be reached!');
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            $email = trim($request->request->get('email', ''));
            $password = $request->request->get('password', '');
            $passwordConfirm = $request->request->get('password_confirm', '');

            // Create user and set values for validation
            $user = new User();
            $user->setEmail($email);
            $user->setPlainPassword($password);

            // Validate using Symfony validator
            $violations = $validator->validate($user, null, ['Default', 'registration']);

            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            // Check password confirmation
            if ($password !== $passwordConfirm) {
                $errors['password_confirm'] = 'Passwords do not match.';
            }

            // Check if user already exists
            if (empty($errors)) {
                $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
                if ($existingUser) {
                    $errors['email'] = 'An account with this email already exists.';
                }
            }

            if (empty($errors)) {
                $user->setPassword($passwordEncoder->encodePassword($user, $password));
                $user->setRoles(['ROLE_USER']);
                $user->eraseCredentials();

                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Account created successfully. Please log in.');
                return $this->redirectToRoute('login');
            }
        }

        return $this->render('security/register.html.twig', [
            'errors' => $errors,
            'email' => $request->request->get('email', ''),
        ]);
    }

    /**
     * @Route("/forgot-password", name="forgot_password")
     */
    public function forgotPasswordAction(
        Request $request,
        EntityManagerInterface $em,
        \Swift_Mailer $mailer
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        $errors = [];
        $success = false;

        if ($request->isMethod('POST')) {
            $email = trim($request->request->get('email', ''));

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Please enter a valid email address.';
            } else {
                /** @var User|null $user */
                $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

                if ($user) {
                    $token = $user->generatePasswordResetToken();
                    $em->flush();

                    $resetUrl = $this->generateUrl('reset_password', ['token' => $token], 0);

                    $message = (new \Swift_Message('Password Reset - Signal Rite'))
                        ->setFrom('noreply@signalrite.local')
                        ->setTo($user->getEmail())
                        ->setBody(
                            $this->renderView('emails/password_reset.html.twig', [
                                'resetUrl' => $resetUrl,
                            ]),
                            'text/html'
                        );

                    $mailer->send($message);
                }

                $success = true;
            }
        }

        return $this->render('security/forgot_password.html.twig', [
            'errors' => $errors,
            'success' => $success,
        ]);
    }

    /**
     * @Route("/reset-password/{token}", name="reset_password")
     */
    public function resetPasswordAction(
        Request $request,
        string $token,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {
        /** @var User|null $user */
        $user = $em->getRepository(User::class)->findOneBy(['passwordResetToken' => $token]);

        if (!$user || !$user->isPasswordResetTokenValid()) {
            $this->addFlash('error', 'This password reset link is invalid or has expired.');
            return $this->redirectToRoute('forgot_password');
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password', '');
            $passwordConfirm = $request->request->get('password_confirm', '');

            if (strlen($password) < 8) {
                $errors['password'] = 'Password must be at least 8 characters.';
            }
            if ($password !== $passwordConfirm) {
                $errors['password_confirm'] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                $user->setPassword($passwordEncoder->encodePassword($user, $password));
                $user->clearPasswordResetToken();
                $em->flush();

                $this->addFlash('success', 'Your password has been reset. Please log in.');
                return $this->redirectToRoute('login');
            }
        }

        return $this->render('security/reset_password.html.twig', [
            'errors' => $errors,
            'token' => $token,
        ]);
    }

    /**
     * @Route("/me", name="profile")
     */
    public function profileAction(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
        $errors = [];
        $success = false;

        if ($request->isMethod('POST')) {
            $currentPassword = $request->request->get('current_password', '');
            $newPassword = $request->request->get('new_password', '');
            $newPasswordConfirm = $request->request->get('new_password_confirm', '');

            if (!$passwordEncoder->isPasswordValid($user, $currentPassword)) {
                $errors['current_password'] = 'Current password is incorrect.';
            }
            if (strlen($newPassword) < 8) {
                $errors['new_password'] = 'New password must be at least 8 characters.';
            }
            if ($newPassword !== $newPasswordConfirm) {
                $errors['new_password_confirm'] = 'New passwords do not match.';
            }

            if (empty($errors)) {
                $user->setPassword($passwordEncoder->encodePassword($user, $newPassword));
                $em->flush();
                $success = true;
            }
        }

        return $this->render('security/profile.html.twig', [
            'user' => $user,
            'errors' => $errors,
            'success' => $success,
        ]);
    }
}
