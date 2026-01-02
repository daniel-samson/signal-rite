<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * Find a user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail($email)
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * Find a user by password reset token
     *
     * @param string $token
     * @return User|null
     */
    public function findByPasswordResetToken($token)
    {
        return $this->findOneBy(['passwordResetToken' => $token]);
    }
}
