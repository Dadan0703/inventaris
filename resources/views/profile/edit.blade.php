<x-app-layout>
    <x-slot name="title">Profil</x-slot>
    <x-slot name="section">Akun</x-slot>

    <div class="max-w-3xl space-y-6">
        <!-- Form ubah nama & email -->
        <x-card class="p-6 sm:p-8">
            @include('profile.partials.update-profile-information-form')
        </x-card>

        <!-- Form ubah password -->
        <x-card class="p-6 sm:p-8">
            @include('profile.partials.update-password-form')
        </x-card>

        <!-- Form hapus akun -->
        <x-card class="p-6 sm:p-8">
            @include('profile.partials.delete-user-form')
        </x-card>
    </div>
</x-app-layout>
