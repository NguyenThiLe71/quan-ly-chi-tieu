<button {{ $attributes->merge([
'type' => 'submit',
'class' => 'inline-flex items-center px-4 py-2 bg-gradient-to-r from-pink-500 to-orange-400 border border-transparent rounded-md font-semibold text-xs text-black uppercase tracking-widest hover:from-pink-600 hover:to-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2 transition ease-in-out duration-150'
]) }}>
    {{ $slot }}
</button>