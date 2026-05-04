<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClaudeMcpController extends Controller
{
    public function index(Request $request): View
    {
        $clientId = array_key_first(config('oauth.clients', []));
        $client = config('oauth.clients.'.$clientId, []);

        return view('admin.claude-mcp.index', [
            'mcpUrl' => rtrim(config('app.url'), '/').'/mcp/rpc',
            'clientId' => $clientId ?? '',
            'secret' => $client['secret'] ?? '',
            'user' => $request->user(),
            'newApiKey' => session('new_mcp_api_key'),
        ]);
    }

    public function generateMcpApiKey(Request $request): RedirectResponse
    {
        $plainApiKey = $request->user()->generateMcpApiKey();

        return redirect()
            ->route('admin.claude-mcp.index')
            ->with('success', __('admin.mcp_api_key_generated'))
            ->with('new_mcp_api_key', $plainApiKey);
    }

    public function revokeMcpApiKey(Request $request): RedirectResponse
    {
        $request->user()->revokeMcpApiKey();

        return redirect()
            ->route('admin.claude-mcp.index')
            ->with('success', __('admin.mcp_api_key_revoked'));
    }
}
