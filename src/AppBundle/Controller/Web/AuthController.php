<?php

declare(strict_types=1);

namespace AppBundle\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{

    /**
     * @Route("/register", name="register")
     */
    public function register(): Response
    {
        return $this->render('default/auth/register.html.twig');
    }

    /**
     * @Route("/signin", name="login")
     */
    public function signin(): Response
    {
        return $this->render('default/auth/login.html.twig');
    }

    /**
     * @Route("/signout", name="logout")
     */
    public function signout(): Response
    {
        return $this->render('default/auth/logout.html.twig');
    }
}
