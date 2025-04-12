<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    /**
     * Sync product images:
     * - Delete removed images
     * - Keep selected images
     * - Upload new ones
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
