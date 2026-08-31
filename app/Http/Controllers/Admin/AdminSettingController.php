<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminSettingController extends Controller
{
    public function updateNonWinnerLabel(Request $request): JsonResponse
    {
        if (! auth()->user()->hasRole('Super Admin')) {
            throw new AccessDeniedHttpException('unauthorize');
        }

        $request->validate(['label' => 'required|string|max:255']);

        Setting::set('non_winner_label', $request->label);

        return $this->success('Non-winner label updated successfully.', [
            'label' => Setting::get('non_winner_label'),
        ]);
    }
}
