<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Handle product image synchronization.
     *
     * - Deletes images that were removed from the UI
     * - Keeps images that are still selected
     * - Uploads and stores newly added images
     *
     * @param \App\Models\Product $product
     * @param array $existingImageIds
     * @param array $newImages
     * @return void
     */
    public function handleImage(Product $product, array $existingImageIds = [], array $newImages = []): void
    {
        $product->images()->whereNotIn('id', $existingImageIds)->get()->each(function ($image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        });
        foreach ($newImages as $image) {
            $path = $image->store('products', 'public');
            $product->images()->create([
                'image_path' => $path,
            ]);
        }
    }
}
