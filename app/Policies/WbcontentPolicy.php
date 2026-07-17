<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wbcontent;

class WbcontentPolicy
{
    public function create(User $user): bool
    {
        return $this->isAdministrator($user);
    }

    public function update(User $user, Wbcontent $wbcontent): bool
    {
        return $this->isAdministrator($user);
    }

    public function delete(User $user, Wbcontent $wbcontent): bool
    {
        return $this->isAdministrator($user);
    }

    public function distribute(User $user, Wbcontent $wbcontent): bool
    {
        return $this->isAdministrator($user);
    }

    protected function isAdministrator(User $user): bool
    {
        return $user->status === 'Administrator';
    }
}
