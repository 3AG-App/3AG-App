<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\UpdatePasswordRequest;
use App\Http\Requests\Dashboard\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(Request $request): Response
    {
        return Inertia::render('dashboard/profile', [
            'user' => UserResource::make($request->user())->resolve(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Profile updated',
            'description' => 'Your profile information has been saved.',
        ]);

        return back();
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Password updated',
            'description' => 'Your password has been changed successfully.',
        ]);

        return back();
    }
}
