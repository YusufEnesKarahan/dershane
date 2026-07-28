<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Platform\Services\HQApiService;
use Illuminate\Http\Request;

class HQApiController extends Controller
{
    public function __construct(protected HQApiService $hqApiService) {}

    public function index()
    {
        $token = $this->hqApiService->getActiveToken();

        return view('admin.platform.api.index', compact('token'));
    }

    public function regenerate()
    {
        $this->hqApiService->generateToken('HQ Central Panel Integration Token');

        return redirect()->route('admin.platform.api.index')->with('success', 'HQ API Token başarıyla yeniden oluşturuldu.');
    }

    public function revoke()
    {
        $token = $this->hqApiService->getActiveToken();
        if ($token) {
            $this->hqApiService->revokeToken($token->token);
        }

        return redirect()->route('admin.platform.api.index')->with('success', 'HQ API Token başarıyla iptal edildi.');
    }
}
