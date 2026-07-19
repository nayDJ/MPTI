<x-guest-layout :hideLogo="true">
    @if(session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-primary mb-1">NNQUA</h1>
        <p class="text-xl text-on-surface-variant">Manajemen Air Masa Depan</p>
        <div class="h-1 w-12 bg-primary mx-auto mt-4 rounded-full"></div>
    </div>

    <h2 class="text-xl font-semibold text-on-surface mb-6">Masuk</h2>

    <form method="POST" action="{{ route('login') }}" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <div class="space-y-5">
            <div class="form-control">
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder=" " required autofocus />
                <label for="email">
                    <span style="transition-delay:0ms">U</span><span style="transition-delay:50ms">s</span><span style="transition-delay:100ms">e</span><span style="transition-delay:150ms">r</span><span style="transition-delay:200ms">n</span><span style="transition-delay:250ms">a</span><span style="transition-delay:300ms">m</span><span style="transition-delay:350ms">e</span>
                </label>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="form-control" x-data="{ showPassword: false }">
                <input id="password" style="padding-right: 3rem" x-bind:type="showPassword ? 'text' : 'password'" type="password" name="password" placeholder=" " required autocomplete="current-password" />
                <label for="password">
                    <span style="transition-delay:0ms">K</span><span style="transition-delay:50ms">a</span><span style="transition-delay:100ms">t</span><span style="transition-delay:150ms">a</span><span style="transition-delay:200ms">&nbsp;</span><span style="transition-delay:250ms">S</span><span style="transition-delay:300ms">a</span><span style="transition-delay:350ms">n</span><span style="transition-delay:400ms">d</span><span style="transition-delay:450ms">i</span>
                </label>
                <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary">
                    <template x-if="!showPassword">
                        <span class="material-symbols-outlined">visibility</span>
                    </template>
                    <template x-if="showPassword">
                        <span class="material-symbols-outlined">visibility_off</span>
                    </template>
                </button>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" :disabled="submitting" class="btn-slide-hover w-full justify-center group disabled:opacity-50 disabled:cursor-not-allowed" x-bind:disabled="submitting">
                <span>Masuk</span>
                <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </button>
        </div>

        @if (Route::has('password.request'))
            <div class="mt-4 text-center">
                <a class="text-m text-on-surface-variant hover:text-primary underline" href="{{ route('password.request') }}">
                    Lupa Username / Password?
                </a>
            </div>
        @endif

        <div class="mt-4 text-center">
            <p class="text-lg text-on-surface-variant">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-primary font-bold hover:underline ml-1">Daftar</a>
            </p>
        </div>
    </form>
</x-guest-layout>
