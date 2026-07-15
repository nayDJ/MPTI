<x-guest-layout :hideLogo="true" backgroundType="video">
    @if(session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <div class="text-center mb-6">
        <div class="w-20 h-20 mx-auto bg-[#0F6E8C] rounded-2xl flex items-center justify-center animate-float">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-9 h-9 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
        </div>
        <h1 class="mt-2 text-2xl font-bold text-[#0F6E8C]">NNQUA</h1>
        <p class="text-base text-white/70">Sistem Manajemen Air Minum</p>
    </div>

    <form method="POST" action="{{ route('login') }}" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <div class="space-y-4">
            <div class="floating-group">
                <input id="email"
                    class="floating-input"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder=" "
                    required
                    autofocus />
                <label for="email" class="floating-label">Username</label>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="floating-group" x-data="{ showPassword: false }">
                <input id="password"
                    class="floating-input pr-12"
                    x-bind:type="showPassword ? 'text' : 'password'"
                    type="password"
                    name="password"
                    placeholder=" "
                    required
                    autocomplete="current-password" />
                <label for="password" class="floating-label">Kata Sandi</label>
                <button type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-white/60 hover:text-white">
                    <template x-if="!showPassword">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </template>
                    <template x-if="showPassword">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                        </svg>
                    </template>
                </button>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
        </div>

        <div class="mt-6">
            <button type="submit"
                :disabled="submitting"
                class="btn-animated w-full justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                x-bind:disabled="submitting">
                Masuk
                <div class="icon">
                    <svg height="20" width="20" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0z" fill="none"/>
                        <path d="M16.172 11l-5.364-5.364 1.414-1.414L20 12l-7.778 7.778-1.414-1.414L16.172 13H4v-2z" fill="currentColor"/>
                    </svg>
                </div>
            </button>
        </div>

        @if (Route::has('password.request'))
            <div class="mt-4 text-center">
                    <a class="text-base text-white/70 hover:text-white underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0F6E8C]" href="{{ route('password.request') }}">
                    Lupa Username / Password?
                </a>
            </div>
        @endif

        <div class="mt-4 text-center">
            <span class="text-base text-white/60">Belum punya akun?</span>
            <a class="text-base text-white/80 hover:text-white hover:underline font-semibold ml-1" href="{{ route('register') }}">
                Daftar
            </a>
        </div>
    </form>
</x-guest-layout>
