@section('title', 'Data Pengeluaran')
@section('topbar-title', 'Dashboard Pengeluaran')
<x-app-layout>

<div class="min-h-screen bg-[#F3F6F8] p-8" x-data="{
    submitting: false,
    openEdit(id, desc, amount, category, date) {
        document.getElementById('edit-expense-form').action = '/expenses/' + id;
        document.getElementById('edit-description').value = desc;
        document.getElementById('edit-amount').value = amount;
        document.getElementById('edit-category').value = category;
        document.getElementById('edit-category').dispatchEvent(new Event('input', {bubbles: true}));
        document.getElementById('edit-expense-date').value = date;
        document.getElementById('edit-expense-id').value = id;
        this.$dispatch('open-modal', 'edit-expense');
    },
    confirmDelete(url) {
        document.getElementById('delete-expense-form').action = url;
        this.$dispatch('open-modal', 'confirm-delete-expense');
    },
    init() {
        @if(old('_form_type') === 'edit' && old('_edit_id'))
            document.getElementById('edit-expense-form').action = '/expenses/' + @js(old('_edit_id'));
            document.getElementById('edit-description').value = @js(old('description'));
            document.getElementById('edit-amount').value = @js(old('amount'));
            document.getElementById('edit-category').value = @js(old('category'));
            document.getElementById('edit-category').dispatchEvent(new Event('input', {bubbles: true}));
            document.getElementById('edit-expense-date').value = @js(old('expense_date'));
            document.getElementById('edit-expense-id').value = @js(old('_edit_id'));
        @endif
    }
}">

    {{-- Header --}}
    <div class="flex justify-between items-start mb-8">
        <div>
            <nav class="text-sm text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-primary transition">Dashboard</a>
                <span class="mx-1">›</span>
                <span class="text-on-surface-variant">Pengeluaran</span>
            </nav>
            <h1 class="text-3xl font-bold text-primary">Data Pengeluaran</h1>
            <p class="text-on-surface-variant mt-1">Catat dan pantau seluruh pengeluaran operasional NNQUA.</p>
        </div>
        <button
            @click="$dispatch('open-modal', 'add-expense')"
            class="bg-primary hover:bg-primary-container text-white px-5 py-3 rounded-xl shadow-sm transition inline-flex items-center gap-2">
            <span class="material-symbols-outlined">add_circle</span>
            Catat Pengeluaran
        </button>
    </div>

    {{-- Bento Grid: Filter Tabs + Total Expense Card --}}
    <div class="grid grid-cols-12 gap-6 mb-8">
        <div class="col-span-12 lg:col-span-8 bg-surface-container-lowest border border-outline-variant rounded-xl p-4 flex items-center justify-between">
            <div class="flex gap-1 p-1 bg-surface-container-low rounded-lg">
                <a href="{{ request()->fullUrlWithQuery(['period' => null]) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ !request('period') ? 'bg-primary text-white shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
                   Semua
                </a>
                <a href="{{ request()->fullUrlWithQuery(['period' => 'hari_ini']) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('period') === 'hari_ini' ? 'bg-primary text-white shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
                   Hari Ini
                </a>
                <a href="{{ request()->fullUrlWithQuery(['period' => 'minggu_ini']) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('period') === 'minggu_ini' ? 'bg-primary text-white shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
                   Minggu Ini
                </a>
                <a href="{{ request()->fullUrlWithQuery(['period' => 'bulan_ini']) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('period') === 'bulan_ini' ? 'bg-primary text-white shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
                   Bulan Ini
                </a>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('expenses.index') }}" class="hidden sm:flex items-center gap-1">
                    @if(request('period')) <input type="hidden" name="period" value="{{ request('period') }}"> @endif
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    <input type="date" name="from" value="{{ $from ?? '' }}"
                        class="w-28 px-2 py-1.5 border border-outline rounded text-xs outline-none bg-surface"
                        onchange="this.form.submit()">
                    <span class="text-on-surface-variant text-xs">—</span>
                    <input type="date" name="to" value="{{ $to ?? '' }}"
                        class="w-28 px-2 py-1.5 border border-outline rounded text-xs outline-none bg-surface"
                        onchange="this.form.submit()">
                </form>
                <a href="{{ route('expenses.index') }}"
                   class="p-1.5 text-on-surface-variant hover:bg-surface-container-highest rounded-lg transition-colors"
                   title="Reset Filter">
                    <span class="material-symbols-outlined">filter_list</span>
                </a>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-4 bg-primary-container text-on-primary-container rounded-xl p-5 border-t-2 border-primary-fixed flex flex-col justify-between shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -top-4 opacity-10">
                <span class="material-symbols-outlined" style="font-size: 120px;">account_balance_wallet</span>
            </div>
            <div>
                <p class="text-sm uppercase tracking-wider opacity-80">Total Pengeluaran</p>
                <h3 class="text-3xl font-bold mt-1">Rp {{ number_format($totalExpense) }}</h3>
            </div>
            @if($expenseGrowth != 0)
                <div class="flex items-center gap-1 mt-2 text-primary-fixed">
                    <span class="material-symbols-outlined text-sm">{{ $expenseGrowth >= 0 ? 'trending_up' : 'trending_down' }}</span>
                    <span class="text-sm">{{ $expenseGrowth >= 0 ? '+' : '' }}{{ $expenseGrowth }}% dari bulan lalu</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Table Section --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
        <div class="p-5 border-b border-outline-variant flex justify-between items-center bg-surface-bright">
            <h4 class="font-title-md text-on-surface">Daftar Transaksi</h4>
            <form method="GET" action="{{ route('expenses.index') }}" class="flex items-center gap-2">
                @if(request('period')) <input type="hidden" name="period" value="{{ request('period') }}"> @endif
                @if(request('from')) <input type="hidden" name="from" value="{{ request('from') }}"> @endif
                @if(request('to')) <input type="hidden" name="to" value="{{ request('to') }}"> @endif
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-sm">search</span>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Cari transaksi..."
                        class="pl-10 pr-3 py-2 border border-outline-variant rounded-lg bg-surface text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none min-w-[240px]">
                </div>
                <button type="submit"
                    class="bg-primary text-white p-2 rounded-lg hover:bg-primary-container transition">
                    <span class="material-symbols-outlined text-sm">search</span>
                </button>
                <a href="{{ route('expenses.index') }}"
                   class="bg-primary text-white px-3 py-2 rounded-lg text-sm hover:bg-primary-container transition">
                    Reset
                </a>
                <a href="{{ route('expenses.export.pdf', request()->only(['search', 'period', 'from', 'to'])) }}"
                   class="btn-pdf flex items-center gap-2">
                    <span class="text">PDF</span>
                    <span class="icon material-symbols-outlined">picture_as_pdf</span>
                </a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-label-numeric uppercase text-xs tracking-wider">
                        <th class="px-5 py-3 border-b border-outline-variant">Deskripsi</th>
                        <th class="px-5 py-3 border-b border-outline-variant">Kategori</th>
                        <th class="px-5 py-3 border-b border-outline-variant text-right">Jumlah</th>
                        <th class="px-5 py-3 border-b border-outline-variant">Tanggal</th>
                        <th class="px-5 py-3 border-b border-outline-variant text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @forelse($expenses as $expense)
                        @php
                            $iconColors = [
                                ['bg' => '#dbeafe', 'icon' => 'bolt', 'text' => '#1e40af'],
                                ['bg' => '#fef3c7', 'icon' => 'payments', 'text' => '#92400e'],
                                ['bg' => '#fee2e2', 'icon' => 'build', 'text' => '#991b1b'],
                                ['bg' => '#e0e7ff', 'icon' => 'local_shipping', 'text' => '#3730a3'],
                                ['bg' => '#ccfbf1', 'icon' => 'water_drop', 'text' => '#0f766e'],
                                ['bg' => '#f3e8ff', 'icon' => 'receipt_long', 'text' => '#6b21a8'],
                                ['bg' => '#ffedd5', 'icon' => 'coffee', 'text' => '#9a3412'],
                                ['bg' => '#fce7f3', 'icon' => 'home', 'text' => '#9d174d'],
                                ['bg' => '#cffafe', 'icon' => 'inventory_2', 'text' => '#0e7490'],
                                ['bg' => '#f0fdf4', 'icon' => 'eco', 'text' => '#166534'],
                            ];
                            $ic = $iconColors[abs(crc32($expense->category)) % count($iconColors)];

                            $badgeColors = [
                                ['bg' => '#dbeafe', 'text' => '#1e40af'],
                                ['bg' => '#fef3c7', 'text' => '#92400e'],
                                ['bg' => '#fee2e2', 'text' => '#991b1b'],
                                ['bg' => '#e0e7ff', 'text' => '#3730a3'],
                                ['bg' => '#ccfbf1', 'text' => '#0f766e'],
                                ['bg' => '#f3e8ff', 'text' => '#6b21a8'],
                                ['bg' => '#ffedd5', 'text' => '#9a3412'],
                                ['bg' => '#fce7f3', 'text' => '#9d174d'],
                                ['bg' => '#cffafe', 'text' => '#0e7490'],
                                ['bg' => '#f0fdf4', 'text' => '#166534'],
                                ['bg' => '#f5f5f4', 'text' => '#44403c'],
                            ];
                            $bc = $badgeColors[abs(crc32($expense->category)) % count($badgeColors)];
                        @endphp
                        <tr class="hover:bg-surface-container-lowest transition-colors group">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: {{ $ic['bg'] }}; color: {{ $ic['text'] }};">
                                        <span class="material-symbols-outlined">{{ $ic['icon'] }}</span>
                                    </div>
                                    <span class="font-semibold text-on-surface">{{ $expense->description }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider inline-block" style="background-color: {{ $bc['bg'] }}; color: {{ $bc['text'] }};">
                                    {{ $expense->category }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right font-bold text-on-surface">Rp {{ number_format($expense->amount) }}</td>
                            <td class="px-5 py-4 text-on-surface-variant text-sm">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('expenses.show', $expense->id) }}"
                                       title="Lihat Detail"
                                       class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </a>
                                    <button @click="openEdit(@js($expense->id), @js($expense->description), @js($expense->amount), @js($expense->category), @js($expense->expense_date))"
                                            title="Edit"
                                            class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined">edit</span>
                                    </button>
                                    <button @click="confirmDelete('/expenses/' + {{ $expense->id }})"
                                            title="Hapus"
                                            class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-16 text-on-surface-variant">
                                <span class="material-symbols-outlined text-6xl text-outline block mb-4">receipt_long</span>
                                <p class="text-lg font-medium mb-2">Belum ada data pengeluaran</p>
                                <p class="text-sm mb-6">Catat pengeluaran pertama Anda</p>
                                <button @click="$dispatch('open-modal', 'add-expense')"
                                    class="bg-primary hover:bg-primary-container text-white px-5 py-2.5 rounded-lg text-sm font-medium transition inline-flex items-center gap-2">
                                    <span class="material-symbols-outlined text-base">add_circle</span>
                                    Catat Pengeluaran
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-outline-variant flex items-center justify-between text-on-surface-variant text-sm">
            <p>Menampilkan {{ $expenses->firstItem() ?? 0 }}-{{ $expenses->lastItem() ?? 0 }} dari {{ $expenses->total() }} transaksi</p>
            {{ $expenses->links() }}
        </div>
    </div>

    {{-- Bottom Analytics Section --}}
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 relative overflow-hidden">
            <div class="flex items-center gap-3 mb-4">
                <span class="material-symbols-outlined text-primary">analytics</span>
                <h5 class="font-title-md text-on-surface">Analisis Kategori</h5>
            </div>
            @if($categoryDistribution->count() > 0)
                @php $grandTotal = $categoryDistribution->sum('total'); @endphp
                <div class="space-y-3">
                    @foreach($categoryDistribution as $cat)
                        @php
                            $pct = $grandTotal > 0 ? round($cat->total / $grandTotal * 100) : 0;
                            $barColors = ['bg-primary', 'bg-error', 'bg-[#586377]', 'bg-tertiary', 'bg-[#0F6E8C]', 'bg-secondary-container'];
                            $barColor = $barColors[$loop->index % count($barColors)];
                        @endphp
                        <div class="space-y-1">
                            <div class="flex justify-between text-sm">
                                <span class="text-on-surface-variant">{{ $cat->category }}</span>
                                <span class="font-bold text-on-surface">{{ $pct }}%</span>
                            </div>
                            <div class="h-2 bg-surface-container-highest rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 {{ $barColor }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-on-surface-variant text-center py-8">Belum ada data untuk dianalisis</p>
            @endif
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5">
            <div class="flex items-center gap-3 mb-4">
                <span class="material-symbols-outlined text-primary">summarize</span>
                <h5 class="font-title-md text-on-surface">Ringkasan Cepat</h5>
            </div>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-on-surface-variant">Pengeluaran Tertinggi</p>
                    @if($highestExpense)
                        <p class="font-semibold text-on-surface mt-1">{{ $highestExpense->description }}</p>
                        <p class="font-bold text-primary text-lg">Rp {{ number_format($highestExpense->amount) }}</p>
                    @else
                        <p class="text-sm text-on-surface-variant mt-1">—</p>
                    @endif
                </div>
                <div class="border-t border-outline-variant/30 pt-3">
                    <p class="text-sm text-on-surface-variant">Rata-rata per Transaksi</p>
                    <p class="font-bold text-on-surface text-xl mt-1">Rp {{ number_format($averageDaily) }}</p>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Modal Tambah --}}
<x-modal name="add-expense" :show="$errors->any() && old('_form_type') === 'add'" maxWidth="2xl" focusable>
    <form action="{{ route('expenses.store') }}" method="POST" @submit="submitting = true"
        x-data="{
            categoryText: '{{ old('category') }}',
            showCategoryDropdown: false,
            cats: @js($categories->pluck('name')),
            get filteredCats() {
                if (!this.categoryText) return this.cats;
                const q = this.categoryText.toLowerCase();
                return this.cats.filter(c => c.toLowerCase().includes(q));
            },
            selectCat(name) { this.categoryText = name; this.showCategoryDropdown = false; }
        }">
        @csrf
        <input type="hidden" name="_form_type" value="add">

        <div class="glass-panel rounded-xl shadow-xl flex flex-col">
            <div class="px-6 py-4 border-b border-outline-variant/30 flex items-center justify-between bg-surface-container-lowest">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL'1;">receipt_long</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-on-surface">Catat Pengeluaran</h2>
                        <p class="text-sm text-on-surface-variant">Catat biaya operasional manajemen air Anda.</p>
                    </div>
                </div>
                <button type="button" @click="$dispatch('close-modal', 'add-expense')"
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

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Kategori</label>
                        <div class="relative" @click.away="showCategoryDropdown = false"
                             @keydown.escape="showCategoryDropdown = false">
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">folder</span>
                                <input type="text" name="category" x-model="categoryText"
                                    @focus="showCategoryDropdown = true"
                                    @input="showCategoryDropdown = true"
                                    @keydown.enter.prevent="if(filteredCats.length) selectCat(filteredCats[0])"
                                    class="w-full pl-10 pr-10 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                                    placeholder="Cari atau ketik baru..." autocomplete="off" required>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline cursor-pointer select-none"
                                      @click="showCategoryDropdown = !showCategoryDropdown">expand_more</span>
                            </div>
                            <div x-show="showCategoryDropdown && filteredCats.length > 0"
                                 x-cloak
                                 class="absolute z-50 mt-1 w-full bg-white border border-outline rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                <template x-for="cat in filteredCats" :key="cat">
                                    <div @click="selectCat(cat)"
                                         class="px-3 py-2 cursor-pointer hover:bg-primary/10 text-sm text-on-surface"
                                         x-text="cat"></div>
                                </template>
                            </div>
                            <div x-show="showCategoryDropdown && filteredCats.length === 0 && categoryText"
                                 x-cloak
                                 class="absolute z-50 mt-1 w-full bg-white border border-outline rounded-lg shadow-lg p-3 text-sm text-on-surface-variant">
                                Kategori baru: "<span x-text="categoryText" class="font-semibold not-italic"></span>"
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('category')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Tanggal</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">calendar_today</span>
                            <input type="date" name="expense_date"
                                value="{{ old('expense_date', now()->toDateString()) }}"
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm" required>
                        </div>
                        <x-input-error :messages="$errors->get('expense_date')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Deskripsi Pengeluaran</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">description</span>
                        <input type="text" id="description" name="description"
                            value="{{ old('description') }}"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                            placeholder="Masukkan deskripsi..." required>
                    </div>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Jumlah Nominal (Rp)</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">payments</span>
                        <input type="number" id="amount" name="amount" step="0.01" min="0"
                            value="{{ old('amount') }}"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                            placeholder="0" required>
                    </div>
                    <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                </div>
            </div>

            <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant/20 flex justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'add-expense')"
                    class="px-4 py-2 text-sm text-on-surface-variant hover:text-on-surface font-medium">
                    Batal
                </button>
                <button type="submit" :disabled="submitting"
                    class="btn-primary-animate px-5 py-2.5 bg-primary text-white rounded-xl font-semibold text-sm flex items-center gap-2 shadow-md shadow-primary/20 hover:shadow-lg transition-all disabled:opacity-50">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL'1;">save</span>
                    Simpan Pengeluaran
                </button>
            </div>
        </div>
    </form>
</x-modal>

{{-- Modal Edit --}}
<x-modal name="edit-expense" :show="$errors->any() && old('_form_type') === 'edit'" maxWidth="2xl" focusable>
    <form id="edit-expense-form" method="POST" @submit="submitting = true"
        x-data="{
            categoryText: '',
            showCategoryDropdown: false,
            cats: @js($categories->pluck('name')),
            get filteredCats() {
                if (!this.categoryText) return this.cats;
                const q = this.categoryText.toLowerCase();
                return this.cats.filter(c => c.toLowerCase().includes(q));
            },
            selectCat(name) { this.categoryText = name; this.showCategoryDropdown = false; }
        }">
        @csrf
        @method('PUT')
        <input type="hidden" name="_form_type" value="edit">
        <input type="hidden" name="_edit_id" id="edit-expense-id">

        <div class="glass-panel rounded-xl shadow-xl flex flex-col">
            <div class="px-6 py-4 border-b border-outline-variant/30 flex items-center justify-between bg-surface-container-lowest">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL'1;">edit</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-on-surface">Edit Pengeluaran</h2>
                        <p class="text-sm text-on-surface-variant">Ubah data pengeluaran yang sudah tercatat.</p>
                    </div>
                </div>
                <button type="button" @click="$dispatch('close-modal', 'edit-expense')"
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

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Kategori</label>
                        <div class="relative" @click.away="showCategoryDropdown = false"
                             @keydown.escape="showCategoryDropdown = false">
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">folder</span>
                                <input type="text" id="edit-category" name="category" x-model="categoryText"
                                    @focus="showCategoryDropdown = true"
                                    @input="showCategoryDropdown = true"
                                    @keydown.enter.prevent="if(filteredCats.length) selectCat(filteredCats[0])"
                                    class="w-full pl-10 pr-10 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                                    placeholder="Cari atau ketik baru..." autocomplete="off" required>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline cursor-pointer select-none"
                                      @click="showCategoryDropdown = !showCategoryDropdown">expand_more</span>
                            </div>
                            <div x-show="showCategoryDropdown && filteredCats.length > 0"
                                 x-cloak
                                 class="absolute z-50 mt-1 w-full bg-white border border-outline rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                <template x-for="cat in filteredCats" :key="cat">
                                    <div @click="selectCat(cat)"
                                         class="px-3 py-2 cursor-pointer hover:bg-primary/10 text-sm text-on-surface"
                                         x-text="cat"></div>
                                </template>
                            </div>
                            <div x-show="showCategoryDropdown && filteredCats.length === 0 && categoryText"
                                 x-cloak
                                 class="absolute z-50 mt-1 w-full bg-white border border-outline rounded-lg shadow-lg p-3 text-sm text-on-surface-variant">
                                Kategori baru: "<span x-text="categoryText" class="font-semibold not-italic"></span>"
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('category')" class="mt-1" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Tanggal</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">calendar_today</span>
                            <input type="date" id="edit-expense-date" name="expense_date"
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm" required>
                        </div>
                        <x-input-error :messages="$errors->get('expense_date')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Deskripsi Pengeluaran</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">description</span>
                        <input type="text" id="edit-description" name="description"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm" required>
                    </div>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Jumlah Nominal (Rp)</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">payments</span>
                        <input type="number" id="edit-amount" name="amount" step="0.01" min="0"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm" required>
                    </div>
                    <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                </div>
            </div>

            <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant/20 flex justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'edit-expense')"
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
<x-modal name="confirm-delete-expense" focusable>
    <form id="delete-expense-form" method="POST" @submit="submitting = true">
        @csrf
        @method('DELETE')

        <div class="glass-panel rounded-xl shadow-xl">
            <div class="px-6 py-4 border-b border-outline-variant/30 bg-surface-container-lowest">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-error/10 flex items-center justify-center text-error">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL'1;">warning</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-on-surface">Hapus Pengeluaran</h2>
                        <p class="text-sm text-on-surface-variant">Tindakan ini tidak bisa dibatalkan.</p>
                    </div>
                </div>
            </div>

            <div class="p-6 text-center">
                <p class="text-sm text-on-surface-variant">Yakin ingin menghapus pengeluaran ini?</p>
            </div>

            <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant/20 flex justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'confirm-delete-expense')"
                    class="px-4 py-2 text-sm text-on-surface-variant hover:text-on-surface font-medium">
                    Batal
                </button>
                <button type="submit" :disabled="submitting"
                    class="px-5 py-2.5 bg-error text-white rounded-xl font-semibold text-sm flex items-center gap-2 shadow-md shadow-error/20 hover:shadow-lg transition-all disabled:opacity-50">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL'1;">delete</span>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </form>
</x-modal>

</x-app-layout>
