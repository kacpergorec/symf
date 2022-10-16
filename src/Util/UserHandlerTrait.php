<?php
declare (strict_types=1);

namespace App\Util;


use App\Entity\User;

trait UserHandlerTrait
{

    private User $user;

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

}