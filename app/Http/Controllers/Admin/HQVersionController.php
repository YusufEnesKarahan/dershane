<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HQVersion;
use App\Domain\HQ\Services\HQVersionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HQVersionController extends Controller
{
    public function __construct(
        protected HQVersionService $versionService
    ) {}

    public function index()
    {
        $versions = HQVersion::latest('version')->paginate(20);
        return view('admin.hq.versions.index', compact('versions'));
    }

    public function create()
    {
        return view('admin.hq.versions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'version' => 'required|string|unique:hq_versions,version',
            'channel' => 'required|string|in:stable,beta,alpha',
            'release_notes' => 'nullable|string',
            'minimum_supported_version' => 'nullable|string',
            'is_mandatory' => 'boolean',
            'action' => 'required|in:publish,draft',
        ]);

        $validated['created_by'] = Auth::id();

        if ($validated['action'] === 'publish') {
            $this->versionService->publishVersion($validated);
            return redirect()->route('admin.platform.hq_central.versions.index')->with('success', 'Version published successfully.');
        }

        $this->versionService->draftVersion($validated);
        return redirect()->route('admin.platform.hq_central.versions.index')->with('success', 'Version drafted successfully.');
    }

    public function show(HQVersion $version)
    {
        $version->load(['jobs.systemInstance', 'jobs.tenant']);
        return view('admin.hq.versions.show', compact('version'));
    }

    public function archive(HQVersion $version)
    {
        $this->versionService->archiveVersion($version);
        return back()->with('success', 'Version archived successfully.');
    }
}
