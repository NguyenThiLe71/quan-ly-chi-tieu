<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        // Chỉ lấy danh mục hệ thống (is_default = 1)
        $categories = DB::table('categories')
            ->where('is_default', 1)
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|max:100', 'type' => 'required']);
        
        DB::table('categories')->insert([
            'name' => $request->name,
            'type' => $request->type,
            'is_default' => 1, // Luôn là 1 vì đây là trang quản lý hệ thống
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Đã thêm danh mục hệ thống mới!');
    }

    public function update(Request $request, $id)
    {
        DB::table('categories')->where('id', $id)->update([
            'name' => $request->name,
            'type' => $request->type,
            'updated_at' => now()
        ]);

        return back()->with('success', 'Đã cập nhật danh mục!');
    }

    public function destroy($id)
    {
        DB::table('categories')->where('id', $id)->delete();
        return back()->with('success', 'Đã xóa danh mục hệ thống!');
    }
}