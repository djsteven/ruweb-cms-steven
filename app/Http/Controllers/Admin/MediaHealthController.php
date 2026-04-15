<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Media\MediaHealthService;
use Illuminate\View\View;

class MediaHealthController extends Controller
{
    public function __construct(
        protected MediaHealthService $health
    ) {
    }

    public function index(): View
    {
        return view('admin.media.health', [
            'summary' => $this->health->summary(),
            'detail' => $this->health->detailed(),
        ]);
    }
}

