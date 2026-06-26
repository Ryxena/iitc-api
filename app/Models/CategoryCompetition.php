<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryCompetition extends Model
{
    use HasFactory;

    protected $table = 'category_competition';

    protected $guarded = [];

    public function competitionCompetitionCategories(): HasMany
    {
        return $this->hasMany(CompetitionCompetitionCategory::class, 'competition_category_id');
    }
}
