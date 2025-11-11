<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TagExport;
use App\Imports\TagImport;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $query = Tag::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('slug', 'like', "%$search%");
        }

        $tags = $query->latest()->paginate(10);
        return view('admin.tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags',
            'description' => 'nullable|string',
        ]);

        Tag::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return back()->with('success', 'Thêm thẻ thành công!');
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
            'description' => 'nullable|string',
        ]);

        $tag->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return back()->with('success', 'Cập nhật thẻ thành công!');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return back()->with('success', 'Đã xóa thẻ!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new TagImport, $request->file('file'));

        return back()->with('success', 'Nhập dữ liệu thẻ thành công!');
    }

    public function export()
    {
        return Excel::download(new TagExport, 'tags.xlsx');
    }
}
