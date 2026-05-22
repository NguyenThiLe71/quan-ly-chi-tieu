<?php

return [

    'required' => ':attribute không được để trống.',
    'email' => ':attribute phải đúng định dạng email.',
    'confirmed' => ':attribute xác nhận không khớp.',
    'unique' => ':attribute đã tồn tại.', 
    'regex' => ':attribute chỉ được chứa chữ cái và khoảng trắng.',
'not_regex' => ':attribute không hợp lệ.',

    'current_password' => 'Mật khẩu hiện tại không đúng.',

    'password' => [
        'letters' => 'Mật khẩu phải chứa ít nhất một chữ cái.',
        'mixed' => 'Mật khẩu phải có chữ hoa và chữ thường.',
        'numbers' => 'Mật khẩu phải chứa ít nhất một số.',
        'symbols' => 'Mật khẩu phải chứa ít nhất một ký tự đặc biệt.',
    ],

    'attributes' => [
        'name' => 'Tên',
        'email' => 'Email',
        'password' => 'Mật khẩu',
        'password_confirmation' => 'Xác nhận mật khẩu',
    ],

];