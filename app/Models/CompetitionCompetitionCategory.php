<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionCompetitionCategory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competition__id');
    }

    public function competition_category(): BelongsTo
    {
        return $this->belongsTo(CompetitionCategory::class, 'competition_category__id');
    }

}
