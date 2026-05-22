<!DOCTYPE html>
<html lang="vi">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Quản lý chi tiêu</title>

@vite(['resources/css/app.css','resources/js/app.js'])

</head>


<body class="bg-gray-100">

<div class="flex min-h-screen">

<!-- SIDEBAR -->
<div class="w-64 bg-white shadow-lg">

<div class="p-6 border-b">

<h1 class="text-2xl font-bold text-orange-500">
💰 FinanceAI
</h1>

</div>

<nav class="mt-6 px-4 space-y-2">

<a href="/dashboard" class="flex items-center p-3 rounded-lg hover:bg-orange-100">
<span class="mr-3">🏠</span>
Dashboard
</a>

<a href="#" class="flex items-center p-3 rounded-lg hover:bg-orange-100">
<span class="mr-3">💳</span>
Giao dịch
</a>

<a href="#" class="flex items-center p-3 rounded-lg hover:bg-orange-100">
<span class="mr-3">📂</span>
Danh mục
</a>

<a href="#" class="flex items-center p-3 rounded-lg hover:bg-orange-100">
<span class="mr-3">📊</span>
Ngân sách
</a>

<a href="#" class="flex items-center p-3 rounded-lg hover:bg-orange-100">
<span class="mr-3">🎯</span>
Tiết kiệm
</a>

<a href="#" class="flex items-center p-3 rounded-lg hover:bg-orange-100">
<span class="mr-3">📈</span>
Thống kê
</a>

<a href="#" class="flex items-center p-3 rounded-lg hover:bg-orange-100">
<span class="mr-3">🤖</span>
AI phân tích
</a>

<a href="#" class="flex items-center p-3 rounded-lg hover:bg-orange-100">
<span class="mr-3">🔔</span>
Thông báo
</a>

</nav>

</div>



<!-- CONTENT -->
<div class="flex-1 flex flex-col">

<!-- TOPBAR -->
<div class="bg-white shadow p-4 flex justify-between items-center">

<h2 class="text-lg font-semibold">
@yield('title')
</h2>

<div class="text-gray-600">
👤 {{ Auth::user()->name }}
</div>

</div>


<!-- PAGE -->
<div class="p-6">

@yield('content')

</div>

</div>

</div>

</body>
</html>