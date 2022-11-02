<?php
declare (strict_types=1);

namespace App\Factory\User;


use App\Entity\User;

class VerifiedUserFactory extends UserFactory
{

    public function createNew(string $username, string $email, string $plainPassword): User
    {
        $user = parent::createNew($username, $email, $plainPassword);

        $user->setVerified(true);

        return $user;
    }

    public function createNewFromEntity(User $user): User
    {
        $user = parent::createNewFromEntity($user);

        $user->setVerified(true);

        return $user;
    }
}