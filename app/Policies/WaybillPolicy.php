<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Waybill;

class WaybillPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isAdministrator($user);
    }

    public function view(User $user, Waybill $waybill): bool
    {
        return $this->isAdministrator($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdministrator($user);
    }

    public function update(User $user, Waybill $waybill): bool
    {
        return $this->isAdministrator($user);
    }

    public function delete(User $user, Waybill $waybill): bool
    {
        return $this->isAdministrator($user);
    }

    public function restore(User $user, Waybill $waybill): bool
    {
        return $this->isAdministrator($user);
    }

    public function distribute(User $user, Waybill $waybill): bool
    {
        return $this->isAdministrator($user);
    }

    protected function isAdministrator(User $user): bool
    {
        return $user->status === 'Administrator';
    }
}
