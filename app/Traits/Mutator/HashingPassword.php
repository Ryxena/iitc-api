<?php

namespace App\Traits\Mutator;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HashingPassword
{
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => bcrypt($value),
        );
    }
}
