<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Auth\Services\UserPreferenceService;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    public function __construct(protected UserPreferenceService $service) {}

    public function update(Request $request)
    {
        $prefs = $request->validate([
            'theme' => ['nullable', 'string', 'in:light,dark,auto'],
            'language' => ['nullable', 'string', 'max:5'],
            'timezone' => ['nullable', 'string', 'max:50'],
            'density' => ['nullable', 'string', 'in:compact,comfortable'],
            'pagination' => ['nullable', 'integer', 'min:5'],
        ]);

        $this->service->updatePreferences(auth()->user(), $prefs);

        return redirect()->back()->with('success', 'Preferences updated successfully.');
    }
}
