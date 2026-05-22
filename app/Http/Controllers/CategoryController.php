<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Helpers\InteractionHelper;
class CategoryController extends Controller
{
    // Hiển thị danh sách
    public function index()
    {
        // Lấy danh mục của chính User ĐANG ĐĂNG NHẬP HOẶC danh mục hệ thống (mặc định)
        $categories = Category::where(function($query) {
            $query->where('user_id', Auth::id())
                  ->orWhere('is_default', 1);
        })
        ->orderBy('is_default', 'desc') // Hiện hàng mặc định trước
        ->orderBy('created_at', 'desc') // Danh mục mới tạo sẽ đẩy lên đầu trang 1
        ->get();

        return view('categories.index', compact('categories'));
    }

    // Thêm
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'type' => 'required|in:income,expense',
        ]);

        try {
            Category::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'type' => $request->type,
                'is_default' => 0
            ]);
$typeText = $request->type == 'income' ? 'Thu nhập' : 'Chi tiêu';
            InteractionHelper::log(
    Auth::id(),
    'categories',
    'created',
    $request->name,
    "💰 Thêm danh mục: {$request->name} | 💡 Loại: $typeText"
);
            return back()->with('success', 'Thêm danh mục thành công');
        } catch (\Exception $e) {
            return back()->with('error', 'Thêm thất bại: ' . $e->getMessage());
        }
    }

    // Cập nhật
    public function update(Request $request, $id)
    {
        $category = Category::where('user_id', Auth::id())->findOrFail($id);
 $oldName = $category->name;
    $oldType = $category->type;
        // Tránh sửa danh mục mặc định của hệ thống
        if ($category->is_default) {
            return back()->with('error', 'Không thể sửa danh mục mặc định');
        }

        $request->validate([
            'name' => 'required|max:255',
        ]);

        if ($category->name == $request->name && $category->type == $request->type) {
            return back()->with('info', 'Không có thay đổi nào');
        }

        try {
            $category->update([
                'name' => $request->name,
                'type' => $request->type,
            ]);
$note = '';

if ($oldName != $request->name) {
    $note = "📝 Sửa danh mục: \"$oldName\" → \"{$request->name}\"";
} 

if ($oldType != $request->type) {
    $oldTypeText = $oldType == 'income' ? 'Thu nhập' : 'Chi tiêu';
    $newTypeText = $request->type == 'income' ? 'Thu nhập' : 'Chi tiêu';

    // nếu đã có note rồi thì nối thêm
    if ($note != '') {
        $note .= ' | ';
    }

    $note .= "📝  Sửa loại: \"$oldTypeText\" → \"$newTypeText\"";
}

// fallback nếu không có gì đặc biệt
if ($note == '') {
    $note = "🔄 Cập nhật danh mục";
}

InteractionHelper::log(
    Auth::id(),
    'categories',
    'updated',
    $request->name,
    $note
);
            return back()->with('success', 'Cập nhật thành công');
        } catch (\Exception $e) {
            return back()->with('error', 'Cập nhật thất bại');
        }
    }

    // Xóa
    public function destroy($id)
    {
        try {
            // Chỉ cho phép xóa danh mục của chính mình và không phải mặc định
            $category = Category::where('user_id', Auth::id())
                                ->where('is_default', 0)
                                ->findOrFail($id);
$categoryName = $category->name;
$type = $category->type;

InteractionHelper::log(
    Auth::id(),
    'categories',
    'deleted',
    $categoryName,
    "🗑️ Xóa danh mục: \"$categoryName\" | 💡 Loại: " . ($type == 'income' ? 'Thu nhập' : 'Chi tiêu')
);

$category->delete();
            $category->delete();

            return back()->with('success', 'Xóa thành công');
        } catch (\Exception $e) {
            return back()->with('error', 'Xóa thất bại hoặc bạn không có quyền');
        }
    }
}