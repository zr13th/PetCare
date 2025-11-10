<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BrandsExport;
use App\Imports\BrandsImport;

class BrandController extends Controller
{
    /** Hiển thị danh sách thương hiệu */
    public function index(Request $request)
    {
        $query = Brand::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $brands = $query->orderBy('id', 'desc')->paginate(10);
        $brands->appends($request->only(['search']));

        return view('admin.brands.index', compact('brands'));
    }

    /** Form thêm mới */
    public function create()
    {
        return view('admin.brands.create');
    }

    /** Xử lý thêm mới */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $path = null;

        if ($request->hasFile('logo')) {
            $slug = Str::slug($request->name, '-');

            // Tạo thư mục brands nếu chưa có
            Storage::exists('brands') or Storage::makeDirectory('brands', 0775, true);

            // Đặt tên file dạng brands/<slug>.<ext>
            $extension = $request->file('logo')->extension();
            $filename = $slug . '.' . $extension;

            $path = Storage::putFileAs('brands', $request->file('logo'), $filename);
        }

        Brand::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'logo' => $path,
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Thêm thương hiệu thành công!');
    }

    /** Form sửa */
    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /** Cập nhật thương hiệu */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $slug = Str::slug($request->name, '-');
        $data = [
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
        ];

        // Upload logo mới
        if ($request->hasFile('logo')) {
            // Xóa logo cũ
            if ($brand->logo && Storage::exists($brand->logo)) {
                Storage::delete($brand->logo);
            }

            // Tạo thư mục nếu chưa có
            Storage::exists('brands') or Storage::makeDirectory('brands', 0775, true);

            // Đặt tên file dạng brands/<slug>.<ext>
            $extension = $request->file('logo')->extension();
            $filename = $slug . '.' . $extension;

            $data['logo'] = Storage::putFileAs('brands', $request->file('logo'), $filename);
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Cập nhật thương hiệu thành công!');
    }

    /** Xóa thương hiệu */
    public function destroy(Brand $brand)
    {
        if ($brand->logo && Storage::exists($brand->logo)) {
            Storage::delete($brand->logo);
        }

        $brand->delete();
        return redirect()->back()->with('success', 'Đã xóa thương hiệu thành công!');
    }

    /** Hiển thị logo (vì lưu private trong storage/app) */
    public function showLogo(Brand $brand)
    {
        if (!$brand->logo || !Storage::exists($brand->logo)) {
            abort(404);
        }

        return response()->file(storage_path('app/' . $brand->logo));
    }

    /** Nhập Excel */
    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        Excel::import(new BrandsImport, $request->file('file'));
        return redirect()->route('admin.brands.index')->with('success', 'Nhập Excel thành công!');
    }

    /** Xuất Excel */
    public function export()
    {
        return Excel::download(new BrandsExport, 'brands.xlsx');
    }
}
