<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StoreRequest;
use App\Models\Image;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function __invoke(StoreRequest $request) {
        $data = $request->validated();
        $images = $data['images'];
        unset($data['images']);
        $post = Post::create($data);

        foreach ($images as $image) {
            $name = md5(Carbon::now() . '_' . $image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
            $filePath = Storage::disk('public')->putFileAs('/images', $image, $name);
            $previewName = 'prew_' . $name;

            Image::create([
                'path' => $filePath,
                'url' => url('/storage/' . $filePath),
                'preview_url' => url('/storage/images/' . $previewName),
                'post_id' => $post->id
            ]);

            \Intervention\Image\Facades\Image::make($image)->fit(100, 100)
                ->save(storage_path('app/public/images/' . $previewName));
        }
        return response()->json(['message' => 'OK']);
    }
}
