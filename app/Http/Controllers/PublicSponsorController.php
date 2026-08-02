<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use Illuminate\Http\JsonResponse;

class PublicSponsorController extends Controller
{
    private const TIER_ORDER = ['platinum' => 1, 'gold' => 2, 'silver' => 3, 'bronze' => 4];

    public function __invoke(): JsonResponse
    {
        $sponsors = Sponsor::query()
            ->orderByRaw("FIELD(tier, 'platinum', 'gold', 'silver', 'bronze')")
            ->orderBy('name')
            ->get()
            ->map(fn (Sponsor $sponsor) => [
                'id'        => $sponsor->id,
                'name'      => $sponsor->name,
                'tier'      => $sponsor->tier,
                'image'     => $sponsor->image,
                'createdAt' => $sponsor->created_at->toIso8601String(),
            ]);

        return $this->success('success get sponsor data', [
            'sponsors' => $sponsors,
        ]);
    }
}
