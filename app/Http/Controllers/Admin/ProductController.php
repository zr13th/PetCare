<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'tags']);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('slug', 'like', "%$search%");
        }

        if ($cat = $request->input('category_id')) {
            $query->where('category_id', $cat);
        }

        if ($brand = $request->input('brand_id')) {
            $query->where('brand_id', $brand);
        }

        $products = $query->latest()->paginate(10);
        $categories = Category::all();
        $brands = Brand::all();
        $tags = Tag::all();

        return view('admin.products.index', compact('products', 'categories', 'brands', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:products',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id'    => 'nullable|exists:brands,id',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tags'        => 'array',
            'tags.*'      => 'exists:tags,id',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $slug = Str::slug($request->name);
            Storage::exists('products') or Storage::makeDirectory('products', 0775, true);
            $path = Storage::putFileAs('products', $request->file('image'), $slug . '.' . $request->file('image')->extension());
        }

        $product = Product::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'category_id' => $request->category_id,
            'brand_id'    => $request->brand_id,
            'description' => $request->description,
            'price'       => $request->price,
            'stock'       => $request->stock,
            'image'       => $path,
            'status'      => $request->boolean('status', true),
        ]);

        $product->tags()->sync($request->tags ?? []);

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:products,name,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'brand_id'    => 'nullable|exists:brands,id',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tags'        => 'array',
            'tags.*'      => 'exists:tags,id',
        ]);

        $data = $request->only(['name', 'category_id', 'brand_id', 'description', 'price', 'stock']);
        $data['slug'] = Str::slug($request->name);
        $data['status'] = $request->boolean('status', true);

        if ($request->hasFile('image')) {
            if ($product->image && Storage::exists($product->image)) {
                Storage::delete($product->image);
            }
            $slug = Str::slug($request->name);
            $data['image'] = Storage::putFileAs('products', $request->file('image'), $slug . '.' . $request->file('image')->extension());
        }

        $product->update($data);
        $product->tags()->sync($request->tags ?? []);

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function toggleStatus($id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['message' => 'Không tìm thấy sản phẩm'], 404);
        $product->status = !$product->status;
        $product->save();
        return response()->json(['status' => $product->status]);
    }

    public function destroy(Product $product)
    {
        if ($product->image && Storage::exists($product->image)) Storage::delete($product->image);
        $product->tags()->detach();
        $product->delete();
        return redirect()->back()->with('success', 'Đã xóa sản phẩm!');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        Excel::import(new ProductsImport, $request->file('file'));
        return back()->with('success', 'Nhập dữ liệu sản phẩm thành công!');
    }

    public function export()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }
}
