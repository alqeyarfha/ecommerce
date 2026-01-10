<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin'); // atau middleware lain jika ada
        // $this->authorizeResource(Product::class, 'product'); // uncomment jika policy sudah ada
    }

    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['category', 'primaryImage'])
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->when($request->category, fn($q, $catId) => $q->where('category_id', $catId))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::active()->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $product = Product::create($request->validated());

            if ($request->hasFile('images')) {
                $this->uploadImages($request->file('images'), $product);
            }

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Gagal menyimpan produk: ' . $e->getMessage());
        }
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'images']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::active()->orderBy('name')->get();
        $product->load('images');

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $product->update($request->validated());

            if ($request->hasFile('images')) {
                $this->uploadImages($request->file('images'), $product);
            }

            if ($request->filled('delete_images')) {
                $this->deleteImages($request->delete_images, $product);
            }

            if ($request->filled('primary_image')) {
                $this->setPrimaryImage($product, $request->primary_image);
            }

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus produk (support AJAX & redirect)
     */
    public function destroy(Request $request, Product $product): JsonResponse|RedirectResponse
    {
        try {
            // Hapus semua file gambar terkait
            foreach ($product->images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
            }

            $product->delete(); // soft delete (jika pakai trait SoftDeletes)
            // $product->forceDelete(); // uncomment jika ingin hapus permanen dari database

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil dihapus permanen!'
                ]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus produk: ' . $e->getMessage()
                ], 422);
            }

            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }

    // ===================== Helper Methods =====================

    protected function uploadImages(array $files, Product $product): void
    {
        $currentCount = $product->images()->count();
        $isFirst = $currentCount === 0;

        foreach ($files as $index => $file) {
            if (!$file->isValid()) {
                continue;
            }

            $filename = 'product-' . $product->id . '-' . time() . '-' . ($currentCount + $index) . '.' . $file->extension();
            $path = $file->storeAs('products', $filename, 'public');

            $product->images()->create([
                'image_path' => $path,
                'is_primary' => $isFirst && $index === 0,
                'sort_order' => $currentCount + $index,
            ]);
        }
    }

    protected function deleteImages(array $imageIds, Product $product): void
    {
        $images = ProductImage::whereIn('id', $imageIds)
            ->where('product_id', $product->id)
            ->get();

        foreach ($images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }
    }

    protected function setPrimaryImage(Product $product, int $imageId): void
    {
        if (!$product->images()->where('id', $imageId)->exists()) {
            return;
        }

        $product->images()->update(['is_primary' => false]);
        $product->images()->where('id', $imageId)->update(['is_primary' => true]);
    }
}
