<?php

namespace Shopen\Http\Controllers\Admin\Settings;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Shopen\Http\Resources\Admin\TextSlide\TextSlideResource;
use Shopen\Models\TextSlide\TextSlide;
use Shopen\Services\TextSlidesService;

class TopBarSettingsController
{
    public function __construct(
        private TextSlidesService $textSlideService,
    )
    {

    }

    public function index(): Response
    {
        return Inertia::render('Admin/Settings/TopBar', [
            'slides' => TextSlideResource::collection(TextSlide::all()),
        ]);
    }

    public function update(): RedirectResponse
    {
        $ids = array_column(request()->slides, 'id');

        TextSlide::query()->whereNotIn('id', $ids)->delete();

        foreach (request()->get('slides', []) as $slideData) {
            if ($slideData['id'] ?? false) {
                $slide = TextSlide::query()->find($slideData['id']);
            } else {
                $slide = new TextSlide();
            }
            $slide->fill($slideData);
            $slide->save();
        }
        $this->textSlideService->clearCache();

        return back()->with('success', 'Zmiany zostały zapisane');
    }
}