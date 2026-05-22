<x-guest-layout>

<div class="bg-white/90 p-10 rounded-2xl shadow-xl w-full max-w-xl mx-auto">

<h2 style="
text-align:center;
font-size:38px;
font-family:'Poppins',sans-serif;
font-weight:700;
letter-spacing:4px;
margin-bottom:25px;">
REGISTER
</h2>

<form method="POST" action="{{ route('register') }}">
@csrf

<div>
<x-input-label for="name" :value="__('Tên')" />
<x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
<x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
<x-input-label for="email" :value="__('E-mail')" />
<x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
<x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<div class="mt-4">
<x-input-label for="password" :value="__('Mật khẩu')" />

<div class="relative mt-1" style="position: relative;">

<x-text-input id="password" class="block w-full pr-10"
type="password"
name="password"
required
autocomplete="new-password" />

<button type="button"
onclick="togglePassword('password')"
class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700"
style="position:absolute;top:50%;right:0;transform:translateY(-50%);height:100%;">

<svg xmlns="http://www.w3.org/2000/svg"
fill="none"
viewBox="0 0 24 24"
stroke-width="1.8"
stroke="currentColor"
class="w-5 h-5">

<path stroke-linecap="round"
stroke-linejoin="round"
d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5
12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431
0 .639C20.577 16.49 16.64 19.5 12
19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>

<path stroke-linecap="round"
stroke-linejoin="round"
d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>

</svg>

</button>
</div>

<!-- Độ mạnh mật khẩu -->
<div id="password-strength"
style="margin-top:6px;font-size:14px;"></div>

<x-input-error :messages="$errors->get('password')" class="mt-2" />

</div>

<div class="mt-4">

<x-input-label for="password_confirmation" :value="__('Xác nhận mật khẩu')" />

<div class="relative mt-1" style="position:relative;">

<x-text-input id="password_confirmation"
class="block w-full pr-10"
type="password"
name="password_confirmation"
required
autocomplete="new-password" />

<button type="button"
onclick="togglePassword('password_confirmation')"
class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700"
style="position:absolute;top:50%;right:0;transform:translateY(-50%);height:100%;">

<svg xmlns="http://www.w3.org/2000/svg"
fill="none"
viewBox="0 0 24 24"
stroke-width="1.8"
stroke="currentColor"
class="w-5 h-5">

<path stroke-linecap="round"
stroke-linejoin="round"
d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5
12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431
0 .639C20.577 16.49 16.64 19.5 12
19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>

<path stroke-linecap="round"
stroke-linejoin="round"
d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>

</svg>

</button>

</div>

<x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />

</div>

<div class="flex items-center justify-end mt-6">

<a class="underline text-sm text-gray-600 hover:text-gray-900"
href="{{ route('login') }}">
Đã đăng ký rồi?
</a>

<x-primary-button class="ms-4">
Đăng ký
</x-primary-button>

</div>

</form>
</div>

<script>

function togglePassword(id){
let input=document.getElementById(id);
input.type=input.type==="password"?"text":"password";
}

const password=document.getElementById("password");
const strengthText=document.getElementById("password-strength");

password.addEventListener("input",function(){

let value=password.value;
let strength=0;

if(value.length>=8) strength++;
if(/[A-Z]/.test(value)) strength++;
if(/[a-z]/.test(value)) strength++;
if(/[0-9]/.test(value)) strength++;
if(/[@$!%*#?&]/.test(value)) strength++;

if(strength<=2){
strengthText.innerHTML="Mật khẩu yếu 🔴";
strengthText.style.color="red";
}
else if(strength<=4){
strengthText.innerHTML="Mật khẩu trung bình 🟡";
strengthText.style.color="orange";
}
else{
strengthText.innerHTML="Mật khẩu mạnh 🟢";
strengthText.style.color="green";
}

});

</script>

</x-guest-layout>