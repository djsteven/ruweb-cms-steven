<?php

namespace App\Http\Controllers;

use App\Models\Retreat;
use Illuminate\View\View;

class RetreatController extends Controller
{
    public function show(Retreat $retreat): View
    {
        abort_unless($retreat->status === 'published' && (! $retreat->published_at || $retreat->published_at->isPast()), 404);
        return view('retreats.show', compact('retreat'));
    }
}
