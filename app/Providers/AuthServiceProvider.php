<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Competition;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Models\SeminarRegistration;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\CompetitionPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\PaymentStatusPolicy;
use App\Policies\SeminarRegistrationPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Category::class => CategoryPolicy::class,
        Competition::class => CompetitionPolicy::class,
        Payment::class => PaymentPolicy::class,
        PaymentStatus::class => PaymentStatusPolicy::class,
        SeminarRegistration::class => SeminarRegistrationPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

    }
}
