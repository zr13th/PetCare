<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategoriesExport;
use App\Imports\CategoriesImport;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::with('parent');

        // Lọc theo tên / slug
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Lọc theo danh mục cha
        if ($parentId = $request->input('parent_id')) {
            $query->where('parent_id', $parentId);
        }

        $categories = $query->orderBy('id', 'desc')->paginate(3);
        $categories->appends($request->only(['search', 'parent_id']));

        // Lấy tất cả danh mục cha cho dropdown lọc
        $parents = Category::whereNull('parent_id')->get();

        return view('admin.categories.index', compact('categories', 'parents'));
    }


    public function create()
    {
        $parents = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:categories']);
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id
        ]);
        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công');
    }

    public function edit(Category $category)
    {
        $categories = Category::all();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => 'required|string|max:255|unique:categories,name,' . $category->id]);
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'parent_id' => $request->parent_id
        ]);
        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return redirect()->back()->with('error', 'Không thể xóa danh mục có danh mục con.');
        }

        $category->delete();
        return redirect()->back()->with('success', 'Đã xóa danh mục');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        Excel::import(new CategoriesImport, $request->file('file'));
        return redirect()->route('admin.categories.index')->with('success', 'Nhập Excel thành công!');
    }

    public function export()
    {
        return Excel::download(new CategoriesExport, 'categories.xlsx');
    }
}
