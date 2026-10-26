<?php

namespace App\Http\Controllers\API\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Animation;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AnimationController extends Controller
{
    public function getAnimationsData()
    {
        try {
            $animations = Animation::with('translations')->where('is_active', 'active')->get()
                ->map(fn ($item) => $this->format($item));

            return response()->json([
                'animations' => $animations,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Get Animations Data failed', ['error' => $th->getMessage()]);
            return response()->json([
                'message' => __('Something went wrong!')
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAnimation(string $id)
    {
        $animation = Animation::with('translations')->where('is_active', 'active')->find($id);

        if (!$animation) {
            return response()->json([
                'message' => __('Animation not found!')
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'animation' => $this->format($animation),
        ], Response::HTTP_OK);
    }

    // Name in the requested language (English fallback); stored paths become full URLs, missing files stay null.
    private function format(Animation $item): Animation
    {
        $locale = app()->getLocale();
        $item->name = ($locale === 'en' ? null : $item->translation($locale)?->name) ?: $item->name;
        $item->language = $locale;
        $item->unsetRelation('translations');
        $item->thumbnail = $item->thumbnail ? url($item->thumbnail) : null;
        $item->video = $item->video ? url($item->video) : null;
        return $item;
    }
}
