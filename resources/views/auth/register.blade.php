<x-guest-layout :hideLogo="true">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-primary mb-1">NNQUA</h1>
        <p class="text-xl text-on-surface-variant">Manajemen Air Masa Depan</p>
        <div class="h-1 w-12 bg-primary mx-auto mt-4 rounded-full"></div>
    </div>

    <h2 class="text-2xl font-semibold text-on-surface mb-6">Buat Akun Baru</h2>

    <form method="POST" action="{{ route('register') }}" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
            <div class="form-control">
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder=" " required autofocus autocomplete="name" />
                <label for="name">
                    <span style="transition-delay:0ms">N</span><span style="transition-delay:50ms">a</span><span style="transition-delay:100ms">m</span><span style="transition-delay:150ms">a</span><span style="transition-delay:200ms">&nbsp;</span><span style="transition-delay:250ms">L</span><span style="transition-delay:300ms">e</span><span style="transition-delay:350ms">n</span><span style="transition-delay:400ms">g</span><span style="transition-delay:450ms">k</span><span style="transition-delay:500ms">a</span><span style="transition-delay:550ms">p</span>
                </label>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="form-control">
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder=" " required autocomplete="username" />
                <label for="email">
                    <span style="transition-delay:0ms">E</span><span style="transition-delay:50ms">m</span><span style="transition-delay:100ms">a</span><span style="transition-delay:150ms">i</span><span style="transition-delay:200ms">l</span>
                </label>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="form-control" x-data="{ showPassword: false }">
                <input id="password" style="padding-right: 3rem" x-bind:type="showPassword ? 'text' : 'password'" type="password" name="password" placeholder=" " required autocomplete="new-password" />
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

            <div class="form-control" x-data="{ showConfirm: false }">
                <input id="password_confirmation" style="padding-right: 3rem" x-bind:type="showConfirm ? 'text' : 'password'" type="password" name="password_confirmation" placeholder=" " required autocomplete="new-password" />
                <label for="password_confirmation">
                    <span style="transition-delay:0ms">K</span><span style="transition-delay:50ms">o</span><span style="transition-delay:100ms">n</span><span style="transition-delay:150ms">f</span><span style="transition-delay:200ms">i</span><span style="transition-delay:250ms">r</span><span style="transition-delay:300ms">m</span><span style="transition-delay:350ms">a</span><span style="transition-delay:400ms">s</span><span style="transition-delay:450ms">i</span><span style="transition-delay:500ms">&nbsp;</span><span style="transition-delay:550ms">K</span><span style="transition-delay:600ms">a</span><span style="transition-delay:650ms">t</span><span style="transition-delay:700ms">a</span><span style="transition-delay:750ms">&nbsp;</span><span style="transition-delay:800ms">S</span><span style="transition-delay:850ms">a</span><span style="transition-delay:900ms">n</span><span style="transition-delay:950ms">d</span><span style="transition-delay:1000ms">i</span>
                </label>
                <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary">
                    <template x-if="!showConfirm">
                        <span class="material-symbols-outlined">visibility</span>
                    </template>
                    <template x-if="showConfirm">
                        <span class="material-symbols-outlined">visibility_off</span>
                    </template>
                </button>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-start gap-3 mt-6">
            <input type="checkbox" id="terms" class="mt-0.5 rounded border-outline-variant text-primary focus:ring-primary" required />
            <label for="terms" class="text-sm text-on-surface-variant">
                Saya menyetujui <a href="#" class="text-primary font-semibold hover:underline">Syarat &amp; Ketentuan</a> serta <a href="#" class="text-primary font-semibold hover:underline">Kebijakan Privasi</a> NNQUA.
            </label>
        </div>

        <div class="mt-6">
            <button type="submit" :disabled="submitting" class="btn-slide-hover w-full justify-center group disabled:opacity-50 disabled:cursor-not-allowed" x-bind:disabled="submitting">
                <span>Buat Akun</span>
                <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </button>
        </div>

        <div class="mt-6 text-center">
            <p class="text-lg text-on-surface-variant">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="text-primary font-bold hover:underline ml-1">Masuk di sini</a>
            </p>
        </div>
    </form>
</x-guest-layout>
