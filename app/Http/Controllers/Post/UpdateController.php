<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Image;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class UpdateController extends Controller
{
    public function __invoke(UpdateRequest $request, Post $post) {
        $data = $request->validated();
        $images = isset($data['images']) ? $data['images'] : null;
        $imagesIdsForDelete = isset($data['images_ids_for_delete']) ? $data['images_ids_for_delete'] : null;
        $imagesUrlsForDelete = isset($data['images_urls_for_delete']) ? $data['images_urls_for_delete'] : null;
        unset($data['images'], $data['images_ids_for_delete'], $data['images_urls_for_delete']);

        $currentPostImages = $post->images;

        if ($imagesIdsForDelete) {
            foreach ($currentPostImages as $currentPostImage) {
                if (in_array($currentPostImage->id, $imagesIdsForDelete)) {
                    Storage::disk('public')->delete($currentPostImage->path);
                    //Storage::disk('public')->delete(str_replace('images/', 'images/prew_', $currentPostImage->path));
                    $currentPostImage->delete();
                }
            }
        }

        if ($imagesUrlsForDelete) {
            foreach ($imagesUrlsForDelete as $imageUrlForDelete) {
                $removeStr = $request->root() . '/storage/';
                $path = str_replace($removeStr, '', $imageUrlForDelete);
                Storage::disk('public')->delete($path);
            }
        }

        $post->update($data);

        if ($images) {
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

                //\Intervention\Image\Facades\Image::make($image)->fit(100, 100)
                //    ->save(storage_path('app/public/images/' . $previewName));
            }
        }

        return response()->json(['message' => 'OK']);
    }
}
