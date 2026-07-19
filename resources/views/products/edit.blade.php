@section('title', 'Edit Produk')
<x-app-layout x-data="{ submitting: false }">

<div class="min-h-screen bg-[#F3F6F8] p-8">

    <div class="max-w-3xl mx-auto">

        <nav class="text-sm text-slate-400 mb-4">
            <a href="{{ route('dashboard') }}" class="hover:text-[#0F6E8C] transition">Dashboard</a>
            <span class="mx-1">›</span>
            <a href="{{ route('products.index') }}" class="hover:text-[#0F6E8C] transition">Manajemen Inventaris</a>
            <span class="mx-1">›</span>
            <span class="text-slate-600">Edit Produk</span>
        </nav>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

            <h1 class="text-2xl font-bold text-[#0F6E8C] mb-2">
                Edit Produk
            </h1>

            <p class="text-gray-500 mb-8">
                Perbarui data produk
            </p>

            <form
                action="{{ route('products.update', $product->id) }}"
                method="POST"
                class="space-y-5"
                @submit="submitting = true"
            >
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="name" value="Nama Produk" />
                    <x-text-input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="category" value="Kategori" />
                    <x-text-input id="category" name="category" type="text" value="{{ old('category', $product->category) }}" class="mt-1 block w-full" placeholder="Contoh: Aksesoris, Perlengkapan, Pengemasan" />
                    <x-input-error :messages="$errors->get('category')" />
                </div>

                <div>
                    <x-input-label for="stock" value="Jumlah Stok" />
                    <x-text-input id="stock" name="stock" type="number" value="{{ old('stock', $product->stock) }}" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('stock')" />
                </div>

                <div>
                    <x-input-label for="price" value="Harga" />
                    <x-text-input id="price" name="price" type="number" step="0.01" value="{{ old('price', $product->price) }}" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('price')" />
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('products.index') }}"
                        class="px-5 py-3 text-sm text-slate-600 hover:text-slate-800 font-medium">
                        Batal
                    </a>
                    <button type="submit" :disabled="submitting"
                        class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-6 py-3 rounded-xl text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Update
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

</x-app-layout>
