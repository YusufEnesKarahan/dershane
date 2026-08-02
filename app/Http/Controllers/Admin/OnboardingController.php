<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemIdentity;
use App\Models\AcademicTerm;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    public function index()
    {
        $step = 1;
        $hasIdentity = SystemIdentity::exists();
        $hasTerm = AcademicTerm::where('is_active', true)->exists();

        if (!$hasIdentity) {
            $step = 1;
        } elseif (!$hasTerm) {
            $step = 2;
        } else {
            return redirect()->route('admin.reporting.dashboard');
        }

        return view('admin.onboarding.index', compact('step'));
    }

    public function storeIdentity(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'brand_name' => 'required|string|max:255',
        ]);

        $identity = SystemIdentity::first();
        
        if ($identity) {
            $identity->update([
                'company_name' => $request->company_name,
                'brand_name' => $request->brand_name,
            ]);
        } else {
            SystemIdentity::create([
                'company_name' => $request->company_name,
                'brand_name' => $request->brand_name,
            ]);
        }

        return redirect()->route('admin.onboarding.index')->with('success', 'Identity saved successfully.');
    }

    public function storeTerm(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Set all other terms to inactive
        AcademicTerm::query()->update(['is_active' => false]);

        AcademicTerm::create([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => true,
        ]);

        return redirect()->route('admin.reporting.dashboard')->with('success', 'Onboarding completed successfully! Welcome to your dashboard.');
    }
}
