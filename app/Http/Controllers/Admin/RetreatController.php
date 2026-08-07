<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Retreat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RetreatController extends Controller
{
    public function index(): View
    {
        return view('admin.retreats.index', ['retreats' => Retreat::orderByDesc('starts_at')->paginate(15)]);
    }

    public function create(): View
    {
        return view('admin.retreats.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $retreat = Retreat::create($this->validated($request) + [
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);
        $this->syncImage($retreat, $request->integer('featured_image'));

        return redirect()->route('admin.retreats.edit', $retreat)->with('success', 'Retreat created.');
    }

    public function edit(Retreat $retreat): View
    {
        return view('admin.retreats.edit', compact('retreat'));
    }

    public function update(Request $request, Retreat $retreat): RedirectResponse
    {
        $retreat->update($this->validated($request, $retreat) + ['updated_by' => $request->user()->id]);
        $this->syncImage($retreat, $request->integer('featured_image'));

        return back()->with('success', 'Retreat updated.');
    }

    public function destroy(Retreat $retreat): RedirectResponse
    {
        abort_unless(request()->user()->isAdmin(), 403);
        $retreat->media()->detach();
        $retreat->delete();

        return redirect()->route('admin.retreats.index')->with('success', 'Retreat deleted.');
    }

    private function validated(Request $request, ?Retreat $retreat = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('retreats')->ignore($retreat)],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'organizer' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:700'],
            'content' => ['nullable', 'string'],
            'status' => ['required', Rule::in(config('cms.statuses'))],
            'published_at' => ['nullable', 'date'],
            'featured_image' => ['nullable', 'integer', 'exists:media,id'],
        ]);
        unset($data['featured_image']);
        if ($data['status'] === 'published' && empty($data['published_at'])) $data['published_at'] = now();
        return $data;
    }

    private function syncImage(Retreat $retreat, int $imageId): void
    {
        $retreat->media()->wherePivot('collection', 'featured_image')->detach();
        if ($imageId) $retreat->attachMedia($imageId, 'featured_image');
    }
}
