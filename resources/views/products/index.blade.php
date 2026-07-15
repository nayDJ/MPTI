@section('title', 'Manajemen Inventaris')
<x-app-layout>

<div class="min-h-screen bg-[#F3F6F8] p-8" x-data="{
    submitting: false,
    openEdit(id, name, category, stock, price) {
        document.getElementById('edit-product-form').action = '/products/' + id;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-category').value = category;
        document.getElementById('edit-stock').value = stock;
        document.getElementById('edit-price').value = price;
        document.getElementById('edit-product-id').value = id;
        this.$dispatch('open-modal', 'edit-product');
    },
    confirmDelete(url) {
        document.getElementById('delete-product-form').action = url;
        this.$dispatch('open-modal', 'confirm-delete-product');
    },
    init() {
        @if(old('_form_type') === 'edit' && old('_edit_id'))
            document.getElementById('edit-product-form').action = '/products/' + {{ json_encode(old('_edit_id')) }};
            document.getElementById('edit-name').value = {{ json_encode(old('name')) }};
            document.getElementById('edit-category').value = {{ json_encode(old('category')) }};
            document.getElementById('edit-stock').value = {{ json_encode(old('stock')) }};
            document.getElementById('edit-price').value = {{ json_encode(old('price')) }};
            document.getElementById('edit-product-id').value = {{ json_encode(old('_edit_id')) }};
        @endif
    }
}">

    @if(session('success'))
        <div class="mb-6 bg-green-100 text-green-700 p-4 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 text-red-600 p-4 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex justify-between items-start mb-8">

        <div>
            <nav class="text-sm text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-[#0F6E8C] transition">Dashboard</a>
                <span class="mx-1">›</span>
                <span class="text-slate-600">Manajemen Inventaris</span>
            </nav>

            <h1 class="text-3xl font-bold text-[#0F6E8C]">
                Manajemen Inventaris
            </h1>

            <p class="text-gray-500 mt-1">
                Pantau stok aset dan perlengkapan NNQUA secara real-time.
            </p>
        </div>

        <button
            @click="$dispatch('open-modal', 'add-product')"
            class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-5 py-3 rounded-xl shadow-sm transition whitespace-nowrap">
            + Tambah Produk Baru
        </button>

    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-white rounded-2xl shadow-sm border p-6">
            <p class="text-sm text-gray-500">Total Jenis Item</p>
            <h2 class="text-3xl font-bold mt-2">{{ $totalProducts }}</h2>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-6">
            <p class="text-sm text-gray-500">Stok Rendah</p>
            <h2 class="text-3xl font-bold text-yellow-500 mt-2">{{ $lowStockCount }}</h2>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-6">
            <p class="text-sm text-gray-500">Habis (Kosong)</p>
            <h2 class="text-3xl font-bold text-red-500 mt-2">{{ $outOfStockCount }}</h2>
        </div>

    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="p-6 border-b flex items-center justify-between gap-4 flex-wrap">
            <h2 class="text-xl font-bold text-slate-800">Daftar Stok Inventaris</h2>

            <div class="flex gap-2">
                <a href="{{ route('products.index') }}"
                   class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 {{ request()->has('category') || request()->has('search') ? '' : 'hidden' }}">
                    Reset
                </a>

                <form method="GET" action="{{ route('products.index') }}" class="flex gap-2">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Cari inventaris..."
                        class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-64">
                    <button type="submit"
                        class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-4 py-2 rounded-lg text-sm transition">
                        Cari
                    </button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">

                <thead>
                    <tr class="bg-slate-50 border-b">
                        <th class="p-4 text-left">Nama Produk</th>
                        <th class="p-4 text-left">Kategori</th>
                        <th class="p-4 text-left">Jumlah Stok</th>
                        <th class="p-4 text-left">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($products as $product)
                        <tr class="border-b hover:bg-slate-50">
                            <td class="p-4 font-medium">{{ $product->name }}</td>
                            <td class="p-4 text-slate-500">{{ $product->category ?? '-' }}</td>
                            <td class="p-4">{{ number_format($product->stock) }} Unit</td>
                            <td class="p-4">
                                @if($product->stock <= 0)
                                    <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-red-600"></span>
                                        Habis
                                    </span>
                                @elseif($product->stock <= 30)
                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                        Stok Menipis
                                    </span>
                                @else
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-green-600"></span>
                                        Tersedia
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        @click="openEdit({{ json_encode($product->id) }}, {{ json_encode($product->name) }}, {{ json_encode($product->category) }}, {{ json_encode($product->stock) }}, {{ json_encode($product->price) }})"
                                        title="Edit"
                                        class="bg-gray-400 hover:bg-gray-500 text-white p-2 rounded-lg transition relative group">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    <button
                                        @click="confirmDelete('/products/' + {{ $product->id }})"
                                        title="Hapus"
                                        class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition relative group">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-16 text-gray-500">
                                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-400 mb-2">Belum ada data produk</p>
                                <p class="text-sm text-gray-400 mb-6">Tambah produk pertama Anda untuk memulai</p>
                                <button
                                    @click="$dispatch('open-modal', 'add-product')"
                                    class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Tambah Produk
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <div class="p-4 border-t flex items-center justify-between">
            <p class="text-sm text-slate-500">
                Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} item
            </p>
            {{ $products->links() }}
        </div>

    </div>

</div>

{{-- Modal Tambah Produk --}}
<x-modal name="add-product" :show="$errors->any() && old('_form_type') === 'add'" focusable>
    <form action="{{ route('products.store') }}" method="POST" class="p-6" @submit="submitting = true">
        @csrf
        <input type="hidden" name="_form_type" value="add">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Tambah Produk Baru</h2>

        <div class="space-y-4">
            <div>
                <x-input-label for="name" value="Nama Produk" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('name')" />
            </div>
            <div>
                <x-input-label for="category" value="Kategori" />
                <x-text-input id="category" name="category" type="text" class="mt-1 block w-full" placeholder="Contoh: Aksesoris, Perlengkapan, Pengemasan" />
                <x-input-error :messages="$errors->get('category')" />
            </div>
            <div>
                <x-input-label for="stock" value="Jumlah Stok" />
                <x-text-input id="stock" name="stock" type="number" min="0" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('stock')" />
            </div>
            <div>
                <x-input-label for="price" value="Harga" />
                <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('price')" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" @click="$dispatch('close-modal', 'add-product')"
                class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
                Batal
            </button>
            <button type="submit" :disabled="submitting"
                class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-5 py-2 rounded-lg text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                Simpan
            </button>
        </div>
    </form>
</x-modal>

{{-- Modal Edit Produk --}}
<x-modal name="edit-product" :show="$errors->any() && old('_form_type') === 'edit'" focusable>
    <form id="edit-product-form" method="POST" class="p-6" @submit="submitting = true">
        @csrf
        @method('PUT')
        <input type="hidden" name="_form_type" value="edit">
        <input type="hidden" name="_edit_id" id="edit-product-id">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Edit Produk</h2>

        <div class="space-y-4">
            <div>
                <x-input-label for="edit-name" value="Nama Produk" />
                <x-text-input id="edit-name" name="name" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('name')" />
            </div>
            <div>
                <x-input-label for="edit-category" value="Kategori" />
                <x-text-input id="edit-category" name="category" type="text" class="mt-1 block w-full" />
                <x-input-error :messages="$errors->get('category')" />
            </div>
            <div>
                <x-input-label for="edit-stock" value="Jumlah Stok" />
                <x-text-input id="edit-stock" name="stock" type="number" min="0" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('stock')" />
            </div>
            <div>
                <x-input-label for="edit-price" value="Harga" />
                <x-text-input id="edit-price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('price')" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" @click="$dispatch('close-modal', 'edit-product')"
                class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
                Batal
            </button>
            <button type="submit" :disabled="submitting"
                class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-5 py-2 rounded-lg text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                Simpan
            </button>
        </div>
    </form>
</x-modal>

{{-- Modal Hapus --}}
<x-modal name="confirm-delete-product" focusable>
    <form id="delete-product-form" method="POST" class="p-6" @submit="submitting = true">
        @csrf
        @method('DELETE')
        <div class="text-center">
            <svg class="mx-auto h-14 w-14 text-red-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <h2 class="text-lg font-bold text-slate-800 mb-2">Hapus Produk</h2>
            <p class="text-sm text-gray-500 mb-6">Yakin ingin menghapus produk ini? Tindakan ini tidak bisa dibatalkan.</p>
        </div>
        <div class="flex justify-end gap-3">
            <button type="button" @click="$dispatch('close-modal', 'confirm-delete-product')"
                class="px-5 py-2.5 text-sm text-slate-600 hover:text-slate-800 font-medium">
                Batal
            </button>
            <button type="submit" :disabled="submitting"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                Ya, Hapus
            </button>
        </div>
    </form>
</x-modal>

</x-app-layout>
