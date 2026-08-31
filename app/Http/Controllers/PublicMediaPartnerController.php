<?php

namespace App\Http\Controllers;

use App\Models\MediaPartner;
use Illuminate\Http\JsonResponse;

class PublicMediaPartnerController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $partners = MediaPartner::query()
            ->orderBy('name')
            ->get()
            ->map(fn (MediaPartner $partner) => [
                'id' => $partner->id,
                'name' => $partner->name,
                'image' => $partner->image,
                'createdAt' => $partner->created_at->toIso8601String(),
            ]);

        return $this->success('success get media partner data', [
            'mediaPartners' => $partners,
        ]);
    }
}
