<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Animation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AnimationController extends Controller
{
    // Videos are heavy on mobile; PHP upload_max_filesize/post_max_size must also allow this.
    private const MAX_VIDEO_KB = 256000;

    private const VIDEO_PATH = 'uploads/animations/videos';
    private const THUMBNAIL_PATH = 'uploads/animations/thumbnails';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view animation');
        try {
            $animations = Animation::all();
            return view('dashboard.animations.index', compact('animations'));
        } catch (\Throwable $th) {
            Log::error('Animations Index Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create animation');
        return view('dashboard.animations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create animation');
        $validator = Validator::make($request->all(), $this->rules(true));

        if ($validator->fails()) {
            Log::error('Animation Validation Failed', ['errors' => $validator->errors()->toArray()]);
            return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'Validation Error!');
        }

        try {
            $animation = new Animation();
            $animation->name = $request->name;
            $this->saveFiles($request, $animation);
            $animation->save();
            $this->saveTranslations($request, $animation);

            return redirect()->route('dashboard.animations.index')->with('success', 'Animation Created Successfully');
        } catch (\Throwable $th) {
            Log::error('Animation Store Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('dashboard.animations.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('update animation');
        try {
            $animation = Animation::with('translations')->findOrFail($id);
            return view('dashboard.animations.edit', compact('animation'));
        } catch (\Throwable $th) {
            Log::error('Animation Edit Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('update animation');
        $validator = Validator::make($request->all(), $this->rules(false));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all())->with('error', 'Validation Error!');
        }

        try {
            $animation = Animation::findOrFail($id);
            $animation->name = $request->name;
            $this->saveFiles($request, $animation);
            $animation->save();
            $this->saveTranslations($request, $animation);
            // Translation-only edits must still bump updated_at so the app refreshes its cache.
            $animation->touch();

            return redirect()->route('dashboard.animations.index')->with('success', 'Animation Updated Successfully');
        } catch (\Throwable $th) {
            Log::error('Animation Update Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete animation');
        try {
            $animation = Animation::findOrFail($id);
            $this->deleteFile($animation->thumbnail);
            $this->deleteFile($animation->video);
            $animation->delete();
            return redirect()->back()->with('success', 'Animation Deleted Successfully');
        } catch (\Throwable $th) {
            Log::error('Animation Delete Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
        }
    }

    public function updateStatus(string $id)
    {
        $this->authorize('update animation');
        try {
            $animation = Animation::findOrFail($id);
            $message = $animation->is_active == 'active' ? 'Animation Deactivated Successfully' : 'Animation Activated Successfully';
            $animation->is_active = $animation->is_active == 'active' ? 'inactive' : 'active';
            $animation->save();
            return redirect()->back()->with('success', $message);
        } catch (\Throwable $th) {
            Log::error('Animation Status Updation Failed', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', "Something went wrong! Please try again later");
        }
    }

    private function rules(bool $creating): array
    {
        return [
            'name' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max_size',
            'video' => ($creating ? 'required' : 'nullable')
                . '|file|mimetypes:video/mp4,video/quicktime,video/webm,video/x-m4v|max:' . self::MAX_VIDEO_KB,
            'translations' => 'nullable|array',
            'translations.*.name' => 'nullable|string|max:255',
        ];
    }

    // Saves the non-English names; languages left empty fall back to English.
    private function saveTranslations(Request $request, Animation $animation): void
    {
        foreach (array_diff(config('app.api_locales'), ['en']) as $locale) {
            $name = $request->input("translations.$locale.name");

            if ($name) {
                $animation->translations()->updateOrCreate(['locale' => $locale], ['name' => $name]);
            } else {
                $animation->translations()->where('locale', $locale)->delete();
            }
        }
    }

    private function saveFiles(Request $request, Animation $animation): void
    {
        if ($request->hasFile('thumbnail')) {
            $this->deleteFile($animation->thumbnail);
            $animation->thumbnail = $this->moveUpload($request->file('thumbnail'), self::THUMBNAIL_PATH, 'thumbnail');
        }

        if ($request->hasFile('video')) {
            $this->deleteFile($animation->video);
            $video = $request->file('video');
            $animation->video_size = $video->getSize();
            $animation->video = $this->moveUpload($video, self::VIDEO_PATH, 'video');
        }
    }

    private function moveUpload($file, string $path, string $suffix): string
    {
        $name = time() . '_' . $suffix . '.' . strtolower($file->getClientOriginalExtension());
        $file->move(public_path($path), $name);
        return $path . '/' . $name;
    }

    private function deleteFile(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
