<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\DTOs\User\UserProfileDTO;
use App\Http\Requests\Admin\User\UpdateProfileRequest;
use App\Domain\Auth\Actions\User\UpdateProfileAction;
use App\Domain\Auth\Actions\User\UpdateAvatarAction;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('admin.profile.edit', compact('user'));
    }

    public function update(UpdateProfileRequest $request, UpdateProfileAction $action)
    {
        $dto = UserProfileDTO::fromRequest($request->validated());
        $action->execute(auth()->user(), $dto);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();
        $user->update(['password' => bcrypt($request->password)]);

        // Invalidate sessions
        auth()->logoutOtherDevices($request->password);

        event(new \App\Events\UserPasswordChanged($user));

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    public function avatar(Request $request, UpdateAvatarAction $action)
    {
        $request->validate([
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $action->execute(auth()->user(), $request->file('avatar'));

        return redirect()->back()->with('success', 'Avatar updated successfully.');
    }
}
