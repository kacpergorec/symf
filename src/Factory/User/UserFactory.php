<?php
declare (strict_types=1);

namespace App\Factory\User;


use App\Entity\User;

class UserFactory extends AbstractUserFactory
{

    public function createNew(string $username, string $email, string $plainPassword): User
    {
        $user = new User();

        if ($username && $email && $plainPassword) {
            $hashedPassword = $this->hashPassword($user, $plainPassword);

            $user
                ->setUsername($username)
                ->setEmail($email)
                ->setPassword($hashedPassword);
        }

        return $user;
    }

    public function createNewFromEntity(User $user): User
    {
        if ($user->hasPassword()) {
            $hashedPassword = $this->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
        }

        return $user;
    }
}