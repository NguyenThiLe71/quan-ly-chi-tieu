@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white dark:bg-gray-700'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    // Căn theo lề phải (end-0) nhưng dịch sang trái 40% để tâm dropdown gần với icon hơn
    'center' => 'end-0 translate-x-[40%] origin-top-right', 
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

// Xác định độ rộng cụ thể cho từng trường hợp
$widthValue = match ($width) {
    '32' => '128px',    // Cho User (Gọn gàng)
    '80' => '450px',    // Cho Thông báo (Rộng rãi)
    'full' => '420px',
    default => null,    // Nếu không khớp thì để CSS tự xử lý
};
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-2 rounded-md shadow-lg {{ $alignmentClasses }}"
        @style([
            'display: none',
            "width: $widthValue" => $widthValue, // Chỉ thêm width nếu $widthValue có giá trị
        ])
        @click="open = false">

        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            {{ $content }}
        </div>

    </div>
</div>