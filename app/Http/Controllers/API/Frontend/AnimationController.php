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
            $animations = Animation::where('is_active', 'active')->get()->map(fn ($item) => $this->format($item));

            return response()->json([
                'animations' => $animations,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Get Animations Data failed', ['error' => $th->getMessage()]);
            return response()->json([
                'message' => 'Something went wrong!'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAnimation(string $id)
    {
        $animation = Animation::where('is_active', 'active')->find($id);

        if (!$animation) {
            return response()->json([
                'message' => 'Animation not found!'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'animation' => $this->format($animation),
        ], Response::HTTP_OK);
    }

    // Stored paths become full URLs; missing files stay null.
    private function format(Animation $item): Animation
    {
        $item->thumbnail = $item->thumbnail ? url($item->thumbnail) : null;
        $item->video = $item->video ? url($item->video) : null;
        return $item;
    }
}
