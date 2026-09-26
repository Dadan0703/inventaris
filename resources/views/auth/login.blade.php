<x-guest-layout>
    <!-- Judul form -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold">Masuk ke akun</h1>
        <p class="mt-1 text-sm text-stone-500">Gunakan email dan password yang terdaftar.</p>
    </div>

    <!-- Pesan status, contoh: setelah reset password berhasil -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        {{-- Token keamanan wajib untuk setiap form POST di Laravel --}}
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" placeholder="nama@perusahaan.com" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" value="Password" />

                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-brand hover:text-brand-dark" href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                @endif
            </div>
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" placeholder="Minimal 8 karakter" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Ingat saya -->
        <label for="remember_me" class="inline-flex items-center gap-2">
            <input id="remember_me" type="checkbox" class="rounded border-stone-300 text-brand shadow-sm focus:ring-brand" name="remember">
            <span class="text-sm">Ingat saya</span>
        </label>

        <x-primary-button class="w-full">
            Masuk
        </x-primary-button>

        <p class="text-center text-sm text-stone-500">Belum punya akun? Hubungi administrator untuk dibuatkan akses.</p>
    </form>
</x-guest-layout>
