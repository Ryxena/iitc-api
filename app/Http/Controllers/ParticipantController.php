<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateParticipantRequest;
use App\Models\Participant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ParticipantController extends Controller
{
    public function show(): JsonResponse
    {
        $user = User::with('participant')->findOrFail(auth()->id());
        $responseData = [
            'status' => 1,
            'message' => 'Succeed get detail user profile',
            'data' => [
                'user' => $user,
            ],
        ];

        return response()->json($responseData);
    }

    public function update(UpdateParticipantRequest $request): JsonResponse
    {
        $user = auth()->user();

        $userData = [
            'name' => $request->input('fullName'),
            'phone' => $request->input('phone'),
        ];
        $user->update($userData);

        $profileData = [
            'grade' => $request->input('grade'),
            'institution' => $request->input('institution'),
            'student_id_number' => $request->input('studentId'),
            'gender' => $request->input('gender'),
        ];
        if ($request->file('avatar') != null) {
            $ext = $request->file('avatar')->getClientOriginalExtension();
            $fileName = $user->name . '-' . $user->email . '-' . Carbon::now()->timestamp . '.' . $ext;
            $avatar = $request->file('avatar')->storeAs('participant/avatar', $fileName, ['disk' => 'public']);
            $profileData['avatar'] = Storage::disk('public')->url($avatar);
        }
        if ($request->file('photoIdentity') != null) {
            $ext = $request->file('photoIdentity')->getClientOriginalExtension();
            $fileName = $user->name . '-' . $user->email . '-' . Carbon::now()->timestamp . '.' . $ext;
            $photoIdentity = $request->file('photoIdentity')
                ->storeAs('participant/photo-identity', $fileName, ['disk' => 'public']);
            $profileData['photo_identity'] = Storage::disk('public')->url($photoIdentity);
        }
        if ($request->file('twibbon') != null) {
            $ext = $request->file('twibbon')->getClientOriginalExtension();
            $fileName = $user->name . '-' . $user->email . '-' . Carbon::now()->timestamp . '.' . $ext;
            $twibbon = $request->file('twibbon')->storeAs('participant/twibbon', $fileName, ['disk' => 'public']);
            $profileData['twibbon'] = Storage::disk('public')->url($twibbon);
        }
        $participant = Participant::query()->where('user_id', $user->id)->first();
        $detail = [];
        if ($participant == null) {
            $profileData['user_id'] = $user->id;
            $detail = Participant::query()->create($profileData);
        } else {
            Participant::query()->where('user_id', $user->id)->update($profileData);
            $detail = $participant;
        }

        $responseData = [
            'status' => 1,
            'message' => 'Succeed update user profile',
            'data' => [
                'user' => $user,
                'detail' => $detail,
            ],
        ];

        return response()->json($responseData, 200);
    }
}
