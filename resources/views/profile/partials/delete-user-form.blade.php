<section class="bg-white rounded-2xl shadow-sm border border-red-100 p-8">

    <h2 class="text-xl font-bold text-slate-800">
        Hapus Akun
    </h2>

    <p class="text-sm text-slate-500 mt-1">
        Setelah akun dihapus, semua data tidak bisa dikembalikan
    </p>

    <div class="mt-6">
        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >{{ __('Delete Account') }}</x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-slate-800 mb-2">
                Yakin ingin menghapus akun?
            </h2>

            <p class="text-sm text-slate-500 mb-6">
                Data akan dihapus permanen. Masukkan password untuk konfirmasi.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Password" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Password"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <button type="submit" x-bind:disabled="submitting"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>
