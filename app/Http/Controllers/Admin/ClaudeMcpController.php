<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ClaudeMcpController extends Controller
{
    public function index(): View
    {
        $clientId = array_key_first(config('oauth.clients', []));
        $client   = config('oauth.clients.' . $clientId, []);

        return view('admin.claude-mcp.index', [
            'mcpUrl'   => rtrim(config('app.url'), '/') . '/mcp/rpc',
            'clientId' => $clientId ?? '',
            'secret'   => $client['secret'] ?? '',
        ]);
    }
}
