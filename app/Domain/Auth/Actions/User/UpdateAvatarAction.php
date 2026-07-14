<?php
namespace App\Domain\Auth\Actions\User;

use App\Events\UserAvatarChanged;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UpdateAvatarAction
{
    public function execute(User $user, ?UploadedFile $file): User
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        if ($file) {
            // Validation/Optimization placeholder
            $path = $file->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        } else {
            $user->update(['avatar' => null]);
        }

        event(new UserAvatarChanged($user));

        return $user;
    }
}
