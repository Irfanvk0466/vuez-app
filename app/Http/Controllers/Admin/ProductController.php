<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    /**
     * Display a list of all products with their associated images.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $products = Product::with(relations: 'images')->latest()->paginate(6);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    /**
     * Store a newly created product in the database along with uploaded images.
     *
     * @param \App\Http\Requests\ProductRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductRequest $request)
    {
        try {
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'starting_price' => $request->starting_price,
                'current_price' => $request->starting_price,
                'end_time' => $request->end_time,
            ]);
            $images = $request->file('images', []);
            $this->imageService->handleImage($product, [], $images);
            return redirect()->route('products.index')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Return product details with images for editing via AJAX.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(string $id)
    {
        $product = Product::with('images')->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'product' => $product,
        ]);
    }
    /**
     * Update the specified product and handle image replacements.
     *
     * @param \App\Http\Requests\ProductRequest $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductRequest $request, string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'starting_price' => $request->starting_price,
                'end_time' => $request->end_time,
            ]);
            $existing = $request->existing_image_ids ?? [];
            $new = $request->file('images', []);
            $this->imageService->handleImage($product, $existing, $new);
            return redirect()->route('products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }
    /**
     * Delete the specified product along with its associated images from storage.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            foreach ($product->images as $img) {
                if (Storage::disk('public')->exists($img->image_path)) {
                    Storage::disk('public')->delete($img->image_path);
                }
                $img->delete();
            }
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }
}
