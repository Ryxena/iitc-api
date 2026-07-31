<?php

namespace App\Models;

use App\Helpers\Grade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participant extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    protected $attributes = [
        'grade' => Grade::STUDENT,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
