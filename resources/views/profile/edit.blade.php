@section('title', 'Profile')
<x-app-layout>

<div class="min-h-screen bg-[#F3F6F8] p-8">

    <div class="max-w-3xl mx-auto">

        <nav class="text-sm text-slate-400 mb-4">
            <a href="{{ route('dashboard') }}" class="hover:text-[#0F6E8C] transition">Dashboard</a>
            <span class="mx-1">›</span>
            <span class="text-slate-600">Profile</span>
        </nav>

        <div class="space-y-6">
            @include('profile.partials.update-profile-information-form')

            @include('profile.partials.update-password-form')

            @include('profile.partials.delete-user-form')
        </div>

    </div>

</div>

</x-app-layout>
