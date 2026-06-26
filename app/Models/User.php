<?php

namespace App\Models;

use App\Traits\Mutator\HashingPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, HasRoles, HashingPassword;

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'leader_id');
    }

    public function asMembers(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'members');
    }

    public function participant(): HasOne
    {
        return $this->hasOne(Participant::class, 'user_id', 'id');
    }

    public function isMemberOf(int $teamId): bool
    {
        $isMember = Member::query()->where('user_id', $this->id)->where('team_id', $teamId)->first();

        return $isMember != null;
    }

        public function payment(): HasOne
    {
        return $this->hasOne(PaymentSeminar::class);
    }

    public function paymentStatus(): HasOne
    {
        return $this->hasOne(PaymentSeminarStatus::class);
    }
    

}
