@section('title', 'Daftar Produk')
@section('topbar-title', 'Dashboard Produk')
<x-app-layout>

<div class="min-h-screen bg-[#F3F6F8]" x-data="{
    submitting: false,
    openEdit(id, name, category, stock, price, lowStockAlertEnabled, lowStockThreshold, components, trackStock) {
        let form = document.getElementById('edit-product-form');
        form.action = '/products/' + id;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-stock').value = stock;
        document.getElementById('edit-price').value = price;
        document.getElementById('edit-product-id').value = id;
        let data = Alpine.$data(form);
        data.category = category || 'kemasan';
        data.stockEnabled = trackStock ? 'true' : 'false';
        data.lowStockAlertEnabled = !!lowStockAlertEnabled;
        data.lowStockThreshold = lowStockThreshold || 30;
        data.components = components || [];
        this.$dispatch('open-modal', 'edit-product');
    },
    confirmDelete(url) {
        document.getElementById('delete-product-form').action = url;
        this.$dispatch('open-modal', 'confirm-delete-product');
    },
    init() {
        @if(old('_form_type') === 'edit' && old('_edit_id'))
            let form = document.getElementById('edit-product-form');
            form.action = '/products/' + {{ json_encode(old('_edit_id')) }};
            document.getElementById('edit-name').value = {{ json_encode(old('name')) }};
            document.getElementById('edit-stock').value = {{ json_encode(old('stock')) }};
            document.getElementById('edit-price').value = {{ json_encode(old('price')) }};
            document.getElementById('edit-product-id').value = {{ json_encode(old('_edit_id')) }};
            let data = Alpine.$data(form);
            data.category = {{ json_encode(old('category')) }} || 'kemasan';
            data.stockEnabled = '{{ old('track_stock', true) ? "true" : "false" }}';
            data.lowStockAlertEnabled = {{ old('low_stock_alert_enabled') ? 'true' : 'false' }};
            data.lowStockThreshold = {{ old('low_stock_threshold', 30) }};
            data.components = {{ json_encode(old('components', [])) }};
        @endif
    }
}">

    <div class="p-8">

    {{-- Header --}}
    <div class="flex justify-between items-start mb-8">

        <div>
            <nav class="text-sm text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-[#0F6E8C] transition">Dashboard</a>
                <span class="mx-1">›</span>
                <span class="text-slate-600">Daftar Produk</span>
            </nav>

            <h1 class="text-3xl font-bold text-[#0F6E8C]">
                Daftar Produk
            </h1>

            <p class="text-gray-500 mt-1">
                Kelola ketersediaan stok dan harga produk NNQUA
            </p>
        </div>

        <button
            @click="$dispatch('open-modal', 'add-product')"
            class="bg-primary hover:bg-primary-container text-white px-5 py-3 rounded-xl shadow-sm transition whitespace-nowrap inline-flex items-center gap-2">
            <span class="material-symbols-outlined">add_circle</span>
            Tambah Produk
        </button>

    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-4 gap-6 mb-8">

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 shadow-sm p-5 flex flex-col gap-2 hover:border-primary/50 transition-colors">
            <div class="flex justify-between items-start">
                <span class="material-symbols-outlined text-primary bg-primary/10 p-2 rounded-lg">inventory</span>
            </div>
            <div>
                <p class="text-sm text-on-surface-variant">Total SKU</p>
                <p class="text-3xl font-bold text-on-surface">{{ $totalProducts }}</p>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 shadow-sm p-5 flex flex-col gap-2 hover:border-primary/50 transition-colors">
            <div class="flex justify-between items-start">
                <span class="material-symbols-outlined text-tertiary bg-tertiary/10 p-2 rounded-lg">warning</span>
            </div>
            <div>
                <p class="text-sm text-on-surface-variant">Stok Rendah ≤ 30</p>
                <p class="text-3xl font-bold text-yellow-500">{{ $lowStockCount }}</p>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 shadow-sm p-5 flex flex-col gap-2 hover:border-primary/50 transition-colors">
            <div class="flex justify-between items-start">
                <span class="material-symbols-outlined text-error bg-error-container/10 p-2 rounded-lg">priority_high</span>
            </div>
            <div>
                <p class="text-sm text-on-surface-variant">Stok Kritis < 10</p>
                <p class="text-3xl font-bold text-red-500">{{ $criticalStockCount }}</p>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 shadow-sm p-5 flex flex-col gap-2 hover:border-primary/50 transition-colors">
            <div class="flex justify-between items-start">
                <span class="material-symbols-outlined text-secondary bg-secondary/10 p-2 rounded-lg">block</span>
            </div>
            <div>
                <p class="text-sm text-on-surface-variant">Habis</p>
                <p class="text-3xl font-bold text-red-700">{{ $outOfStockCount }}</p>
            </div>
        </div>

    </div>

    {{-- Table --}}
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">

        <div class="p-5 border-b border-outline-variant/20">

            <form method="GET" action="{{ route('products.index') }}"
                  x-data="{
                      showFilter: false,
                      selectedCategory: '{{ request('category', '') }}',
                      searchQuery: '{{ request('search', '') }}',
                      categories: [
                          { val: '', label: 'Semua Kategori' },
                          { val: 'galon', label: 'Galon' },
                          { val: 'air_tanki', label: 'Air Tanki' },
                          { val: 'kemasan', label: 'Kemasan' },
                          { val: 'lainnya', label: 'Lainnya' }
                      ],
                      selectCategory(val) {
                          this.selectedCategory = val;
                          this.showFilter = false;
                          this.$refs.categoryInput.value = val;
                          this.$refs.filterForm.submit();
                      }
                  }"
                  class="flex flex-col md:flex-row justify-between items-center gap-4"
                  x-ref="filterForm">

                <div class="relative w-full md:w-80">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Cari nama produk atau kategori..."
                        x-model="searchQuery"
                        class="w-full pl-10 pr-4 py-2 border border-outline rounded-lg text-sm focus:ring-primary focus:border-primary bg-surface-container-low">
                </div>

                <div class="flex items-center gap-2">
                    <div class="relative">
                        <button type="button" @click="showFilter = !showFilter"
                            class="flex items-center gap-1 px-3 py-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high transition text-sm">
                            <span class="material-symbols-outlined text-[18px]">filter_list</span>
                            Filter
                            <span x-show="selectedCategory" x-cloak class="w-2 h-2 rounded-full bg-primary"></span>
                        </button>

                        <div x-show="showFilter" @click.outside="showFilter = false" x-cloak
                            class="absolute right-0 mt-2 w-48 bg-surface-container-lowest rounded-xl border border-outline-variant shadow-lg z-20 overflow-hidden py-1">
                            <template x-for="item in categories" :key="item.val">
                                <button type="button"
                                    @click="selectCategory(item.val)"
                                    class="w-full text-left px-4 py-2 text-sm flex items-center gap-2 hover:bg-surface-container-high transition-colors"
                                    :class="selectedCategory === item.val ? 'bg-primary/10 text-primary font-semibold' : 'text-on-surface'">
                                    <span x-show="selectedCategory === item.val" class="material-symbols-outlined text-primary text-base">check</span>
                                    <span x-text="item.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        <a href="{{ route('products.index', array_merge(request()->query(), ['is_active' => ''])) }}"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors border {{ request('is_active', '') === '' ? 'bg-primary/10 text-primary border-primary/30' : 'border-outline-variant text-on-surface-variant hover:bg-surface-container-high' }}">
                            Semua
                        </a>
                        <a href="{{ route('products.index', array_merge(request()->query(), ['is_active' => '1'])) }}"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors border {{ request('is_active') === '1' ? 'bg-[#dcfce7] text-[#166534] border-[#bbf7d0]' : 'border-outline-variant text-on-surface-variant hover:bg-surface-container-high' }}">
                            Aktif
                        </a>
                        <a href="{{ route('products.index', array_merge(request()->query(), ['is_active' => '0'])) }}"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors border {{ request('is_active') === '0' ? 'bg-red-100 text-red-500 border-red-200' : 'border-outline-variant text-on-surface-variant hover:bg-surface-container-high' }}">
                            Nonaktif
                        </a>
                    </div>

                    <a href="{{ route('products.export.pdf', request()->only(['search', 'category', 'is_active'])) }}"
                       class="btn-pdf inline-flex items-center gap-2">
                        <span class="text">PDF</span>
                        <span class="icon material-symbols-outlined">picture_as_pdf</span>
                    </a>

                    <button type="submit" x-show="searchQuery || selectedCategory" x-cloak
                        class="flex items-center justify-center w-9 h-9 rounded-lg bg-primary text-white hover:bg-primary-container transition shadow-sm">
                        <span class="material-symbols-outlined text-lg">search</span>
                    </button>

                    @if(request('search') || request('category'))
                        <a href="{{ route('products.index') }}"
                            class="flex items-center gap-1 px-3 py-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high transition text-sm">
                            <span class="material-symbols-outlined text-[18px]">refresh</span>
                            Reset
                        </a>
                    @endif

                    <input type="hidden" name="category" x-ref="categoryInput" value="{{ request('category', '') }}">
                    <input type="hidden" name="is_active" value="{{ request('is_active', '') }}">
                </div>
            </form>

        </div>

        <div class="overflow-x-auto">
            <table class="w-full">

                <thead>
                    <tr class="bg-surface-container-low/50">
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Nama Produk</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Kategori</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-center">Stok</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Status Stok</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Status</th>
                        <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($products as $product)
                        <tr class="border-b border-outline-variant/20 hover:bg-primary/5 transition-colors group">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 rounded-lg bg-secondary-container/30 flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-outline">inventory_2</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-on-surface group-hover:text-primary transition-colors">{{ $product->name }}</p>
                                        <p class="text-xs text-on-surface-variant">NQ-{{ str_pad($product->id, 3, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    @if(in_array($product->category, ['galon', 'air_tanki', 'kemasan', 'lainnya']))
                                        bg-primary/10 text-primary
                                    @else
                                        bg-secondary-container text-on-secondary-container
                                    @endif">
                                    {{ $product->category ? ucfirst(str_replace('_', ' ', $product->category)) : '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($product->track_stock)
                                    <p class="font-bold text-on-surface">{{ number_format($product->stock) }}</p>
                                    <p class="text-[10px] text-on-surface-variant">Unit</p>
                                @else
                                    <p class="text-on-surface-variant">-</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($product->track_stock && $product->is_active)
                                    @php
                                        $threshold = $product->low_stock_alert_enabled ? $product->low_stock_threshold : 30;
                                    @endphp
                                    @if($product->stock <= 0)
                                        <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1">
                                            <span class="w-2 h-2 rounded-full bg-red-600"></span>
                                            Habis
                                        </span>
                                    @elseif($product->stock < 10)
                                        <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1 animate-pulse">
                                            <span class="w-2 h-2 rounded-full bg-red-600"></span>
                                            Stok Kritis
                                        </span>
                                    @elseif($product->low_stock_alert_enabled && $product->stock <= $threshold)
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
                                @elseif(!$product->track_stock && $product->is_active)
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full bg-green-600"></span>
                                        Tersedia
                                    </span>
                                @elseif(!$product->track_stock)
                                    <span class="text-gray-400">-</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($product->is_active)
                                    <span class="text-green-600 font-semibold">Aktif</span>
                                @else
                                    <span class="text-red-500 font-semibold">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        @click="openEdit({{ json_encode($product->id) }}, {{ json_encode($product->name) }}, {{ json_encode($product->category) }}, {{ json_encode($product->stock) }}, {{ json_encode($product->price) }}, {{ json_encode($product->low_stock_alert_enabled) }}, {{ json_encode($product->low_stock_threshold) }}, {{ json_encode($product->components->map(fn($c) => ['product_id' => (string)$c->component_product_id, 'quantity' => $c->quantity])) }}, {{ json_encode($product->track_stock) }})"
                                        title="Edit"
                                        class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined">edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('products.toggle-status', $product) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                            title="{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined">{{ $product->is_active ? 'toggle_on' : 'toggle_off' }}</span>
                                        </button>
                                    </form>
                                    <button
                                        @click="confirmDelete('/products/' + {{ $product->id }})"
                                        title="Hapus"
                                        class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-16 text-on-surface-variant">
                                <span class="material-symbols-outlined text-6xl text-outline-variant mb-4 inline-block">inventory_2</span>
                                <p class="text-lg font-medium text-on-surface-variant mb-2">Belum ada data produk</p>
                                <p class="text-sm text-on-surface-variant mb-6">Tambah produk pertama Anda untuk memulai</p>
                                <button
                                    @click="$dispatch('open-modal', 'add-product')"
                                    class="bg-primary hover:bg-primary-container text-white px-5 py-2.5 rounded-lg text-sm font-medium transition inline-flex items-center gap-2">
                                    <span class="material-symbols-outlined">add_circle</span>
                                    Tambah Produk
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <div class="p-4 border-t border-outline-variant/20 bg-surface-container-low/30 flex items-center justify-between">
            <p class="text-sm text-on-surface-variant">
                Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} item
            </p>
            {{ $products->links() }}
        </div>

    </div>

</div>

</div>

{{-- Modal Tambah Produk --}}
<x-modal name="add-product" :show="$errors->any() && old('_form_type') === 'add'" maxWidth="2xl" focusable>
    <form action="{{ route('products.store') }}" method="POST" @submit="submitting = true"
        x-data="{
            category: '{{ old('category') ?: 'kemasan' }}',
            otherCategory: '',
            stockEnabled: 'true',
            lowStockAlertEnabled: {{ old('low_stock_alert_enabled') ? 'true' : 'true' }},
            lowStockThreshold: {{ old('low_stock_threshold', 30) }},
            components: [],
            componentSearch: '',
            componentQty: 1,
        }">
        @csrf
        <input type="hidden" name="_form_type" value="add">

        <div class="glass-panel rounded-xl shadow-xl flex flex-col max-h-[640px] overflow-y-auto">

            <div class="px-6 py-4 border-b border-outline-variant/30 flex items-center justify-between bg-surface-container-lowest">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL'1;">inventory_2</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-on-surface">Tambah Produk</h2>
                        <p class="text-sm text-on-surface-variant">Kelola inventaris air minum Anda</p>
                    </div>
                </div>
                <button type="button" @click="$dispatch('close-modal', 'add-product')"
                    class="p-2 hover:bg-surface-container-high rounded-full transition-colors text-on-surface-variant flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            @if($errors->any() && old('_form_type') === 'add')
                <div class="mx-6 mt-4 bg-error-container text-on-error-container p-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Nama Produk</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">inventory</span>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                                placeholder="Masukkan nama produk..." required>
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Kategori Produk</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors bg-white text-sm"
                                :class="category === 'galon' ? 'border-primary bg-primary/5' : 'border-outline hover:border-primary/50'">
                                <input type="radio" name="category_radio" value="galon" x-model="category" class="text-primary focus:ring-primary">
                                Galon
                            </label>
                            <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors bg-white text-sm"
                                :class="category === 'air_tanki' ? 'border-primary bg-primary/5' : 'border-outline hover:border-primary/50'">
                                <input type="radio" name="category_radio" value="air_tanki" x-model="category" class="text-primary focus:ring-primary">
                                Air Tanki
                            </label>
                            <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors bg-white text-sm"
                                :class="category === 'kemasan' ? 'border-primary bg-primary/5' : 'border-outline hover:border-primary/50'">
                                <input type="radio" name="category_radio" value="kemasan" x-model="category" class="text-primary focus:ring-primary">
                                Kemasan
                            </label>
                            <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors bg-white text-sm"
                                :class="category === 'lainnya' ? 'border-primary bg-primary/5' : 'border-outline hover:border-primary/50'">
                                <input type="radio" name="category_radio" value="lainnya" x-model="category" class="text-primary focus:ring-primary">
                                Lainnya
                            </label>
                        </div>
                        <input type="hidden" name="category" x-bind:value="category === 'lainnya' ? otherCategory : category">
                        <div x-show="category === 'lainnya'" x-cloak class="mt-2">
                            <input type="text" x-model="otherCategory" name="other_category"
                                class="w-full px-3 py-2 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                                placeholder="Masukkan nama kategori...">
                        </div>
                        <x-input-error :messages="$errors->get('category')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Stok</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors bg-white text-sm"
                                :class="stockEnabled === 'true' ? 'border-primary bg-primary/5' : 'border-outline hover:border-primary/50'">
                                <input type="radio" name="track_stock_radio" x-model="stockEnabled" value="true" class="text-primary focus:ring-primary">
                                Aktifkan Stok
                            </label>
                            <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors bg-white text-sm"
                                :class="stockEnabled === 'false' ? 'border-error bg-error/5' : 'border-outline hover:border-error/50'">
                                <input type="radio" name="track_stock_radio" x-model="stockEnabled" value="false" class="text-error focus:ring-error">
                                Nonaktifkan Stok
                            </label>
                        </div>
                        <input type="hidden" name="track_stock" x-bind:value="stockEnabled === 'true' ? '1' : '0'">
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Stok Awal</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">inventory_2</span>
                                <input type="number" name="stock" min="0" value="{{ old('stock', 0) }}"
                                    :disabled="stockEnabled === 'false'"
                                    :class="stockEnabled === 'true'
                                        ? 'bg-white border border-outline focus:ring-2 focus:ring-primary/20 focus:border-primary'
                                        : 'bg-gray-100 border border-gray-200 text-gray-400 cursor-not-allowed'"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-lg outline-none transition-all text-sm"
                                    x-bind:required="stockEnabled === 'true'">
                            </div>
                            <x-input-error :messages="$errors->get('stock')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Harga Jual per Unit</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">payments</span>
                            <input type="number" name="price" step="0.01" min="0" value="{{ old('price', 0) }}"
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                                required>
                        </div>
                        <x-input-error :messages="$errors->get('price')" class="mt-1" />
                    </div>

                    <div x-show="stockEnabled === 'true'" x-cloak class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-on-surface">Peringatan Stok Rendah</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="low_stock_alert_enabled" value="1"
                                    x-model="lowStockAlertEnabled" class="sr-only peer">
                                <div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>
                        <p class="text-xs text-on-surface-variant mb-3">Sistem akan memberi notifikasi saat stok di bawah ambang batas yang ditentukan.</p>
                        <div x-show="lowStockAlertEnabled" x-cloak>
                            <input type="range" name="low_stock_threshold" min="0" max="100"
                                x-model="lowStockThreshold"
                                class="w-full h-1.5 bg-outline-variant rounded-lg appearance-none cursor-pointer accent-primary">
                            <div class="flex justify-between mt-1 font-label-numeric text-[10px] text-outline">
                                <span>Min: 0</span>
                                <span x-text="'Threshold: ' + lowStockThreshold" class="text-primary font-semibold"></span>
                                <span>Max: 100</span>
                            </div>
                        </div>
                        <input type="hidden" name="low_stock_alert_enabled" x-bind:value="lowStockAlertEnabled ? '1' : '0'">
                    </div>
                </div>
            </div>

            </div>

            <div class="px-6 pb-4">
                <div class="border-t border-outline-variant/30 pt-5">
                    <h4 class="text-sm font-bold text-on-surface mb-1">Komponen Produk</h4>
                    <p class="text-xs text-on-surface-variant mb-4">Produk yang otomatis ikut terkirim saat produk ini dijual. Stok komponen ikut berkurang. Harga komponen Rp 0.</p>

                    <div class="flex items-end gap-2 mb-3">
                        <div class="flex-1">
                            <select x-model="componentSearch" class="w-full px-3 py-2 bg-white border border-outline rounded-lg text-sm">
                                <option value="">Pilih produk...</option>
                                @foreach($allProducts as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}@if($p->track_stock) (stok: {{ $p->stock }}) @endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-20">
                            <input type="number" x-model="componentQty" min="1" value="1"
                                class="w-full px-3 py-2 bg-white border border-outline rounded-lg text-sm text-center">
                        </div>
                        <button type="button"
                            @click="if(componentSearch && !components.find(c => c.product_id == componentSearch)) { components.push({product_id: componentSearch, quantity: parseInt(componentQty) || 1}); componentSearch = ''; componentQty = 1; }"
                            class="px-3 py-2 bg-primary text-white rounded-lg text-sm hover:opacity-90 transition">
                            <span class="material-symbols-outlined text-base">add</span>
                        </button>
                    </div>

                    <template x-for="(comp, i) in components" :key="i">
                        <div class="flex items-center justify-between px-3 py-2 bg-surface-container-low rounded-lg mb-2 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-outline text-base">link</span>
                                <span x-text="Object.values($el.closest('form').querySelectorAll('select option')).find(o => o.value == comp.product_id)?.text || comp.product_id"></span>
                                <span class="text-outline">×</span>
                                <span class="font-semibold" x-text="comp.quantity"></span>
                            </div>
                            <button type="button" @click="components.splice(i, 1)" class="text-error hover:text-red-700">
                                <span class="material-symbols-outlined text-base">close</span>
                            </button>
                            <input type="hidden" :name="'components[' + i + '][product_id]'" :value="comp.product_id">
                            <input type="hidden" :name="'components[' + i + '][quantity]'" :value="comp.quantity">
                        </div>
                    </template>
                </div>
            </div>

            <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant/20 flex justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'add-product')"
                    class="px-4 py-2 text-sm text-on-surface-variant hover:text-on-surface font-medium">
                    Batal
                </button>
                <button type="submit" :disabled="submitting"
                    class="btn-primary-animate px-5 py-2.5 bg-primary text-white rounded-xl font-semibold text-sm flex items-center gap-2 shadow-md shadow-primary/20 hover:shadow-lg transition-all disabled:opacity-50">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL'1;">save</span>
                    Simpan Produk
                </button>
            </div>

        </div>
    </form>
</x-modal>

{{-- Modal Edit Produk --}}
<x-modal name="edit-product" :show="$errors->any() && old('_form_type') === 'edit'" maxWidth="2xl" focusable>
    <form id="edit-product-form" method="POST" @submit="submitting = true"
        x-data="{
            category: 'kemasan',
            otherCategory: '',
            stockEnabled: 'true',
            lowStockAlertEnabled: true,
            lowStockThreshold: 30,
            components: [],
            componentSearch: '',
            componentQty: 1,
        }">
        @csrf
        @method('PUT')
        <input type="hidden" name="_form_type" value="edit">
        <input type="hidden" name="_edit_id" id="edit-product-id">

        <div class="glass-panel rounded-xl shadow-xl flex flex-col max-h-[640px] overflow-y-auto">

            <div class="px-6 py-4 border-b border-outline-variant/30 flex items-center justify-between bg-surface-container-lowest">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL'1;">edit</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-on-surface">Edit Produk</h2>
                        <p class="text-sm text-on-surface-variant">Ubah data inventaris produk.</p>
                    </div>
                </div>
                <button type="button" @click="$dispatch('close-modal', 'edit-product')"
                    class="p-2 hover:bg-surface-container-high rounded-full transition-colors text-on-surface-variant flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            @if($errors->any() && old('_form_type') === 'edit')
                <div class="mx-6 mt-4 bg-error-container text-on-error-container p-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Nama Produk</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">inventory</span>
                            <input type="text" id="edit-name" name="name"
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                                placeholder="Masukkan nama produk..." required>
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Kategori Produk</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors bg-white text-sm"
                                :class="category === 'galon' ? 'border-primary bg-primary/5' : 'border-outline hover:border-primary/50'">
                                <input type="radio" name="category_radio" value="galon" x-model="category" class="text-primary focus:ring-primary">
                                Galon
                            </label>
                            <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors bg-white text-sm"
                                :class="category === 'air_tanki' ? 'border-primary bg-primary/5' : 'border-outline hover:border-primary/50'">
                                <input type="radio" name="category_radio" value="air_tanki" x-model="category" class="text-primary focus:ring-primary">
                                Air Tanki
                            </label>
                            <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors bg-white text-sm"
                                :class="category === 'kemasan' ? 'border-primary bg-primary/5' : 'border-outline hover:border-primary/50'">
                                <input type="radio" name="category_radio" value="kemasan" x-model="category" class="text-primary focus:ring-primary">
                                Kemasan
                            </label>
                            <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors bg-white text-sm"
                                :class="category === 'lainnya' ? 'border-primary bg-primary/5' : 'border-outline hover:border-primary/50'">
                                <input type="radio" name="category_radio" value="lainnya" x-model="category" class="text-primary focus:ring-primary">
                                Lainnya
                            </label>
                        </div>
                        <input type="hidden" name="category" x-bind:value="category === 'lainnya' ? otherCategory : category">
                        <div x-show="category === 'lainnya'" x-cloak class="mt-2">
                            <input type="text" x-model="otherCategory"
                                class="w-full px-3 py-2 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                                placeholder="Masukkan nama kategori...">
                        </div>
                        <x-input-error :messages="$errors->get('category')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Stok</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors bg-white text-sm"
                                :class="stockEnabled === 'true' ? 'border-primary bg-primary/5' : 'border-outline hover:border-primary/50'">
                                <input type="radio" name="track_stock_radio" x-model="stockEnabled" value="true" class="text-primary focus:ring-primary">
                                Aktifkan Stok
                            </label>
                            <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border cursor-pointer transition-colors bg-white text-sm"
                                :class="stockEnabled === 'false' ? 'border-error bg-error/5' : 'border-outline hover:border-error/50'">
                                <input type="radio" name="track_stock_radio" x-model="stockEnabled" value="false" class="text-error focus:ring-error">
                                Nonaktifkan Stok
                            </label>
                        </div>
                        <input type="hidden" name="track_stock" x-bind:value="stockEnabled === 'true' ? '1' : '0'">
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Stok Awal</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">inventory_2</span>
                                <input type="number" id="edit-stock" name="stock" min="0"
                                    :disabled="stockEnabled === 'false'"
                                    :class="stockEnabled === 'true'
                                        ? 'bg-white border border-outline focus:ring-2 focus:ring-primary/20 focus:border-primary'
                                        : 'bg-gray-100 border border-gray-200 text-gray-400 cursor-not-allowed'"
                                    class="w-full pl-10 pr-4 py-2.5 rounded-lg outline-none transition-all text-sm"
                                    x-bind:required="stockEnabled === 'true'">
                            </div>
                            <x-input-error :messages="$errors->get('stock')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Harga Jual per Unit</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">payments</span>
                            <input type="number" id="edit-price" name="price" step="0.01" min="0"
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                                required>
                        </div>
                        <x-input-error :messages="$errors->get('price')" class="mt-1" />
                    </div>

                    <div x-show="stockEnabled === 'true'" x-cloak class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-on-surface">Peringatan Stok Rendah</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="low_stock_alert_enabled" value="1"
                                    x-model="lowStockAlertEnabled" class="sr-only peer">
                                <div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>
                        <p class="text-xs text-on-surface-variant mb-3">Sistem akan memberi notifikasi saat stok di bawah ambang batas yang ditentukan.</p>
                        <div x-show="lowStockAlertEnabled" x-cloak>
                            <input type="range" name="low_stock_threshold" min="0" max="100"
                                x-model="lowStockThreshold"
                                class="w-full h-1.5 bg-outline-variant rounded-lg appearance-none cursor-pointer accent-primary">
                            <div class="flex justify-between mt-1 font-label-numeric text-[10px] text-outline">
                                <span>Min: 0</span>
                                <span x-text="'Threshold: ' + lowStockThreshold" class="text-primary font-semibold"></span>
                                <span>Max: 100</span>
                            </div>
                        </div>
                        <input type="hidden" name="low_stock_alert_enabled" x-bind:value="lowStockAlertEnabled ? '1' : '0'">
                    </div>
                </div>
            </div>

            </div>

            <div class="px-6 pb-4">
                <div class="border-t border-outline-variant/30 pt-5">
                    <h4 class="text-sm font-bold text-on-surface mb-1">Komponen Produk</h4>
                    <p class="text-xs text-on-surface-variant mb-4">Produk yang otomatis ikut terkirim saat produk ini dijual. Stok komponen ikut berkurang. Harga komponen Rp 0.</p>

                    <div class="flex items-end gap-2 mb-3">
                        <div class="flex-1">
                            <select x-model="componentSearch" class="w-full px-3 py-2 bg-white border border-outline rounded-lg text-sm">
                                <option value="">Pilih produk...</option>
                                @foreach($allProducts as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}@if($p->track_stock) (stok: {{ $p->stock }}) @endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-20">
                            <input type="number" x-model="componentQty" min="1" value="1"
                                class="w-full px-3 py-2 bg-white border border-outline rounded-lg text-sm text-center">
                        </div>
                        <button type="button"
                            @click="if(componentSearch && !components.find(c => c.product_id == componentSearch)) { components.push({product_id: componentSearch, quantity: parseInt(componentQty) || 1}); componentSearch = ''; componentQty = 1; }"
                            class="px-3 py-2 bg-primary text-white rounded-lg text-sm hover:opacity-90 transition">
                            <span class="material-symbols-outlined text-base">add</span>
                        </button>
                    </div>

                    <template x-for="(comp, i) in components" :key="i">
                        <div class="flex items-center justify-between px-3 py-2 bg-surface-container-low rounded-lg mb-2 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-outline text-base">link</span>
                                <span x-text="Object.values($el.closest('form').querySelectorAll('select option')).find(o => o.value == comp.product_id)?.text || comp.product_id"></span>
                                <span class="text-outline">×</span>
                                <span class="font-semibold" x-text="comp.quantity"></span>
                            </div>
                            <button type="button" @click="components.splice(i, 1)" class="text-error hover:text-red-700">
                                <span class="material-symbols-outlined text-base">close</span>
                            </button>
                            <input type="hidden" :name="'components[' + i + '][product_id]'" :value="comp.product_id">
                            <input type="hidden" :name="'components[' + i + '][quantity]'" :value="comp.quantity">
                        </div>
                    </template>
                </div>
            </div>

            <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant/20 flex justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'edit-product')"
                    class="px-4 py-2 text-sm text-on-surface-variant hover:text-on-surface font-medium">
                    Batal
                </button>
                <button type="submit" :disabled="submitting"
                    class="btn-primary-animate px-5 py-2.5 bg-primary text-white rounded-xl font-semibold text-sm flex items-center gap-2 shadow-md shadow-primary/20 hover:shadow-lg transition-all disabled:opacity-50">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL'1;">save</span>
                    Simpan Perubahan
                </button>
            </div>

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
