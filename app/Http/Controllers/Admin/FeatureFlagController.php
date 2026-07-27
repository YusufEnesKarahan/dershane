<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;
use App\Domain\Platform\Services\FeatureFlagService;
use Illuminate\Http\Request;

class FeatureFlagController extends Controller
{
    public function __construct(protected FeatureFlagService $featureFlagService) {}

    public function index()
    {
        $flags = $this->featureFlagService->getAllFlags();
        return view('admin.platform.features.index', compact('flags'));
    }

    public function toggle(FeatureFlag $feature)
    {
        $feature->update([
            'enabled' => !$feature->enabled
        ]);

        $this->featureFlagService->clearCache($feature->name);

        return redirect()->route('admin.platform.features.index')
            ->with('success', "Özellik: {$feature->name} durumu güncellendi.");
    }
}
