<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hồ sơ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Card 1 -->
            <div class="p-4 sm:p-8 bg-white/90 shadow-2xl sm:rounded-2xl border border-pink-100
                        transform transition duration-500 hover:-translate-y-1 hover:shadow-pink-200/50
                        animate-fade-in">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Card 2 -->
            <div class="p-4 sm:p-8 bg-white/90 shadow-2xl sm:rounded-2xl border border-pink-100
                        transform transition duration-500 hover:-translate-y-1 hover:shadow-pink-200/50
                        animate-fade-in [animation-delay:0.2s]">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Card 3 -->
            <div class="p-4 sm:p-8 bg-white/90 shadow-2xl sm:rounded-2xl border border-pink-100
                        transform transition duration-500 hover:-translate-y-1 hover:shadow-pink-200/50
                        animate-fade-in [animation-delay:0.4s]">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>