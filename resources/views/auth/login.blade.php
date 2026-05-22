<x-guest-layout>

<div class="bg-white/90 p-10 rounded-2xl shadow-xl w-full max-w-xl mx-auto -mt-5">

<h2 style="
text-align:center;
font-size:38px;
font-family:'Poppins',sans-serif;
font-weight:700;
letter-spacing:4px;
margin-bottom:25px;
">
LOGIN
</h2>

@if(session('success'))
<div style="
background:#22c55e;
color:white;
padding:12px;
border-radius:8px;
margin-bottom:20px;
text-align:center;
font-weight:500;
box-shadow:0 4px 10px rgba(0,0,0,0.15);
">
{{ session('success') }}
</div>
@endif
<!-- Session Status -->
<x-auth-session-status class="mb-4" :status="session('status')" />

<form method="POST" action="{{ route('login') }}">
@csrf

<!-- Email -->
<div>
<x-input-label for="email" :value="__('Email')" />
<x-text-input id="email" class="block mt-1 w-full"
type="email"
name="email"
:value="old('email')"
required autofocus autocomplete="username" />
<x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<!-- Password -->
<div class="mt-4">
    <x-input-label for="password" :value="__('Password')" />

    <div class="relative mt-1" style="position: relative;">
        
        <x-text-input 
            id="password" 
            type="password" 
            name="password" 
            required 
            autocomplete="current-password"
            class="block w-full pr-10" 
        /> 

        <button
            type="button"
            onclick="togglePassword()"
            class="text-gray-500 hover:text-gray-700"
            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 10; border: none; background: none; cursor: pointer; display: flex; align-items: center;"
        >
            <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z"/>
            </svg>

            <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>
        </button>
    </div>

    <x-input-error :messages="$errors->get('password')" class="mt-2" />
</div>
<!-- Remember -->
<div class="block mt-4">
<label class="inline-flex items-center">
<input type="checkbox" name="remember" class="rounded border-gray-300">
<span class="ms-2 text-sm text-gray-600">
Remember me
</span>
</label>
</div>

<div class="mt-6 flex justify-between text-sm">
<a class="text-gray-600 underline"
href="{{ route('register') }}">
Bạn chưa có tài khoản? Đăng ký
</a>

@if (Route::has('password.request'))
<a class="text-gray-600 underline"
href="{{ route('password.request') }}">
Bạn quên mật khẩu?
</a>
@endif
</div>

<div class="mt-6 flex justify-center">
<x-primary-button>
ĐĂNG NHẬP
</x-primary-button>
</div>

</form>

</div>

<script>
function togglePassword() {
    const password = document.getElementById("password");
    password.type = password.type === "password" ? "text" : "password";
}
</script>
</x-guest-layout>