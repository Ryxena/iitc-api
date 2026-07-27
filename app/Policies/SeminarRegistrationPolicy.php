<?php

namespace App\Policies;

use App\Models\SeminarRegistration;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeminarRegistrationPolicy
{
    use HandlesAuthorization;

    /**
     * Admins with the correct permission can verify attendance (update).
     */
    public function update(User $user, SeminarRegistration $seminarRegistration): bool
    {
        return $user->hasPermissionTo('Update Payment Status');
    }
}
