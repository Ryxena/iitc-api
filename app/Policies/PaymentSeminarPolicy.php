<?php

namespace App\Policies;

use App\Models\PaymentSeminar;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentSeminarPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentSeminar  $paymentSeminar
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, PaymentSeminar $paymentSeminar)
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user, PaymentSeminar $paymentSeminar, User $targetUser): bool
    {
        // Hanya boleh unggah bukti untuk dirinya sendiri
        return $user->id === $targetUser->id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentSeminar  $paymentSeminar
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, PaymentSeminar $paymentSeminar)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentSeminar  $paymentSeminar
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, PaymentSeminar $paymentSeminar)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentSeminar  $paymentSeminar
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, PaymentSeminar $paymentSeminar)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PaymentSeminar  $paymentSeminar
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, PaymentSeminar $paymentSeminar)
    {
        return false;
    }
}
