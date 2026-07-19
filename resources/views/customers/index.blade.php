@section('title', 'Data Pelanggan')
@section('topbar-title', 'Data Pelanggan')
<x-app-layout>

<div class="min-h-screen bg-[#F3F6F8] p-8" x-data="{
    submitting: false,
    openEdit(id, name, phone, address) {
        document.getElementById('edit-customer-form').action = '/customers/' + id;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-phone').value = phone;
        document.getElementById('edit-address').value = address;
        document.getElementById('edit-customer-id').value = id;
        this.$dispatch('open-modal', 'edit-customer');
    },
    confirmDelete(url) {
        document.getElementById('delete-customer-form').action = url;
        this.$dispatch('open-modal', 'confirm-delete-customer');
    },
    init() {
        @if(old('_form_type') === 'edit' && old('_edit_id'))
            document.getElementById('edit-customer-form').action = '/customers/' + {{ json_encode(old('_edit_id')) }};
            document.getElementById('edit-name').value = {{ json_encode(old('name')) }};
            document.getElementById('edit-phone').value = {{ json_encode(old('phone')) }};
            document.getElementById('edit-address').value = {{ json_encode(old('address')) }};
            document.getElementById('edit-customer-id').value = {{ json_encode(old('_edit_id')) }};
        @endif
    }
}">

{{-- Header --}}
<div class="flex justify-between items-start mb-8">
    <div>
        <nav class="text-sm text-slate-400 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-[#0F6E8C] transition">Dashboard</a>
            <span class="material-symbols-outlined text-[16px] align-middle mx-1">chevron_right</span>
            <span class="text-slate-600">Data Pelanggan</span>
        </nav>
        <h1 class="text-3xl font-bold text-[#0F6E8C]">
            Data Pelanggan
        </h1>
        <p class="text-gray-500 mt-1">
            Kelola seluruh pelanggan NNQUA
        </p>
    </div>
    <button @click="$dispatch('open-modal', 'add-customer')"
        class="bg-primary hover:bg-primary-container text-white px-5 py-3 rounded-xl shadow-sm transition whitespace-nowrap inline-flex items-center gap-2">
        <span class="material-symbols-outlined">add</span>
        Tambah Pelanggan
    </button>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 shadow-sm p-5 flex flex-col gap-2">
        <span class="material-symbols-outlined text-primary bg-primary-fixed p-2 rounded-lg self-start">group</span>
        <p class="text-sm text-on-surface-variant">Total Pelanggan</p>
        <p class="text-3xl font-bold text-on-surface">{{ number_format($totalCustomers) }}</p>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 shadow-sm p-5 flex flex-col gap-2">
        <span class="material-symbols-outlined text-tertiary bg-tertiary-fixed p-2 rounded-lg self-start">receipt_long</span>
        <p class="text-sm text-on-surface-variant">Total Transaksi</p>
        <p class="text-3xl font-bold text-on-surface">{{ number_format($totalTransactions) }}</p>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 shadow-sm p-5 flex flex-col gap-2">
        <span class="material-symbols-outlined text-secondary bg-secondary-fixed p-2 rounded-lg self-start">person_add</span>
        <p class="text-sm text-on-surface-variant">Pelanggan Baru Hari Ini</p>
        <p class="text-3xl font-bold text-green-600">{{ $newCustomers }}</p>
    </div>
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/30 shadow-sm p-5 flex flex-col gap-2">
        <span class="material-symbols-outlined text-error bg-error-container p-2 rounded-lg self-start">credit_score</span>
        <p class="text-sm text-on-surface-variant">Piutang Pelanggan</p>
        <p class="text-3xl font-bold text-red-500">{{ number_format($debtorCount) }}</p>
    </div>
</div>

{{-- Table --}}
<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">

    <div class="p-5 border-b border-outline-variant/20">

        <form method="GET" action="{{ route('customers.index') }}" class="flex flex-col md:flex-row justify-between items-center gap-4">

            <div class="relative w-full md:w-96">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
                <input type="text" name="search" value="{{ $search ?? '' }}"
                    placeholder="Cari nama, No. Telepon atau alamat..."
                    class="w-full pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-full text-sm focus:ring-2 focus:ring-primary/20 outline-none">
            </div>

            <div class="flex items-center gap-2">
                <div class="relative" x-data="{ showFilter: false }">
                    <button type="button" @click="showFilter = !showFilter"
                        class="flex items-center gap-1 px-3 py-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high transition text-sm">
                        <span class="material-symbols-outlined text-[18px]">filter_list</span>
                        Akun
                        <span x-show="'{{ request('is_active', '') }}' !== ''" x-cloak class="w-2 h-2 rounded-full bg-primary"></span>
                    </button>
                    <div x-show="showFilter" @click.outside="showFilter = false" x-cloak
                        class="absolute right-0 mt-2 w-40 bg-surface-container-lowest rounded-xl border border-outline-variant shadow-lg z-20 overflow-hidden py-1">
                        <a href="{{ route('customers.index', array_merge(request()->query(), ['is_active' => ''])) }}"
                            @click="showFilter = false"
                            class="block w-full text-left px-4 py-2 text-sm flex items-center gap-2 hover:bg-surface-container-high transition-colors
                                {{ request('is_active', '') === '' ? 'bg-primary/10 text-primary font-semibold' : 'text-on-surface' }}">
                            <span x-show="'{{ request('is_active', '') }}' === ''" class="material-symbols-outlined text-primary text-base">check</span>
                            <span>Semua</span>
                        </a>
                        <a href="{{ route('customers.index', array_merge(request()->query(), ['is_active' => '1'])) }}"
                            @click="showFilter = false"
                            class="block w-full text-left px-4 py-2 text-sm flex items-center gap-2 hover:bg-surface-container-high transition-colors
                                {{ request('is_active') === '1' ? 'bg-primary/10 text-primary font-semibold' : 'text-on-surface' }}">
                            <span x-show="'{{ request('is_active') }}' === '1'" class="material-symbols-outlined text-primary text-base">check</span>
                            <span>Aktif</span>
                        </a>
                        <a href="{{ route('customers.index', array_merge(request()->query(), ['is_active' => '0'])) }}"
                            @click="showFilter = false"
                            class="block w-full text-left px-4 py-2 text-sm flex items-center gap-2 hover:bg-surface-container-high transition-colors
                                {{ request('is_active') === '0' ? 'bg-primary/10 text-primary font-semibold' : 'text-on-surface' }}">
                            <span x-show="'{{ request('is_active') }}' === '0'" class="material-symbols-outlined text-primary text-base">check</span>
                            <span>Nonaktif</span>
                        </a>
                    </div>
                </div>
                <div class="relative" x-data="{ showDebtFilter: false }">
                    <button type="button" @click="showDebtFilter = !showDebtFilter"
                        class="flex items-center gap-1 px-3 py-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high transition text-sm">
                        <span class="material-symbols-outlined text-[18px]">payments</span>
                        Status
                        <span x-show="'{{ request('debt_status', '') }}' !== ''" x-cloak class="w-2 h-2 rounded-full bg-primary"></span>
                    </button>
                    <div x-show="showDebtFilter" @click.outside="showDebtFilter = false" x-cloak
                        class="absolute right-0 mt-2 w-40 bg-surface-container-lowest rounded-xl border border-outline-variant shadow-lg z-20 overflow-hidden py-1">
                        <a href="{{ route('customers.index', array_merge(request()->query(), ['debt_status' => ''])) }}"
                            @click="showDebtFilter = false"
                            class="block w-full text-left px-4 py-2 text-sm flex items-center gap-2 hover:bg-surface-container-high transition-colors
                                {{ request('debt_status', '') === '' ? 'bg-primary/10 text-primary font-semibold' : 'text-on-surface' }}">
                            <span x-show="'{{ request('debt_status', '') }}' === ''" class="material-symbols-outlined text-primary text-base">check</span>
                            <span>Semua</span>
                        </a>
                        <a href="{{ route('customers.index', array_merge(request()->query(), ['debt_status' => 'lunas'])) }}"
                            @click="showDebtFilter = false"
                            class="block w-full text-left px-4 py-2 text-sm flex items-center gap-2 hover:bg-surface-container-high transition-colors
                                {{ request('debt_status') === 'lunas' ? 'bg-primary/10 text-primary font-semibold' : 'text-on-surface' }}">
                            <span x-show="'{{ request('debt_status') }}' === 'lunas'" class="material-symbols-outlined text-primary text-base">check</span>
                            <span class="w-2 h-2 rounded-full bg-green-500 inline-block mr-1"></span> Lunas
                        </a>
                        <a href="{{ route('customers.index', array_merge(request()->query(), ['debt_status' => 'hutang'])) }}"
                            @click="showDebtFilter = false"
                            class="block w-full text-left px-4 py-2 text-sm flex items-center gap-2 hover:bg-surface-container-high transition-colors
                                {{ request('debt_status') === 'hutang' ? 'bg-primary/10 text-primary font-semibold' : 'text-on-surface' }}">
                            <span x-show="'{{ request('debt_status') }}' === 'hutang'" class="material-symbols-outlined text-primary text-base">check</span>
                            <span class="w-2 h-2 rounded-full bg-red-500 inline-block mr-1"></span> Hutang
                        </a>
                    </div>
                </div>
                <a href="{{ route('customers.export.pdf', request()->only(['search', 'is_active', 'debt_status'])) }}"
                   class="btn-pdf inline-flex items-center gap-2">
                    <span class="text">PDF</span>
                    <span class="icon material-symbols-outlined">picture_as_pdf</span>
                </a>
                @if($search || request('is_active', '') !== '' || request('debt_status', '') !== '')
                    <a href="{{ route('customers.index') }}"
                        class="flex items-center gap-1 px-3 py-2 rounded-lg border border-outline-variant text-on-surface-variant hover:bg-surface-container-high transition text-sm">
                        <span class="material-symbols-outlined text-[18px]">refresh</span>
                        Reset
                    </a>
                @endif
            </div>
            <input type="hidden" name="is_active" value="{{ request('is_active', '') }}">
            <input type="hidden" name="debt_status" value="{{ request('debt_status', '') }}">
        </form>

    </div>

        <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-surface-container-low/50">
                    <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Nama Pelanggan</th>
                    <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">No. Telepon</th>
                    <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Alamat</th>
                    <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Total Transaksi</th>
                    <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Status</th>
                    <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-left">Status Pelanggan</th>
                    <th class="px-4 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    @php
                        $initial = strtoupper(substr($customer->name, 0, 1));
                        $colors = ['bg-primary/10 text-primary', 'bg-secondary/10 text-secondary', 'bg-tertiary/10 text-tertiary', 'bg-error/10 text-error'];
                        $color = $colors[crc32($customer->id) % 4];
                        $hasDebt = ($customer->total_purchase ?? 0) > ($customer->total_paid ?? 0);
                    @endphp
                    <tr class="border-b border-outline-variant/20 hover:bg-primary-container/5 transition-colors group">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full {{ $color }} flex items-center justify-center font-bold text-sm flex-shrink-0">
                                    {{ $initial }}
                                </div>
                                <div>
                                    <p class="font-semibold text-on-surface group-hover:text-primary transition-colors">{{ $customer->name }}</p>
                                    <p class="text-xs text-on-surface-variant">#CUST-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-on-surface-variant">{{ $customer->phone ?: '-' }}</td>
                        <td class="px-4 py-3 text-sm text-on-surface-variant max-w-[200px] truncate">{{ $customer->address ?: '-' }}</td>
                        <td class="px-4 py-3 font-label-numeric text-label-numeric text-on-surface">Rp {{ number_format($customer->total_purchase ?? 0) }}</td>
                        <td class="px-4 py-3">
                            @if($hasDebt)
                                <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-semibold">Hutang</span>
                            @else
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Aktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($customer->is_active)
                                <span class="text-green-600 font-semibold">Aktif</span>
                            @else
                                <span class="text-red-500 font-semibold">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('customers.show', $customer->id) }}"
                                    title="Lihat"
                                    class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined">visibility</span>
                                </a>
                                <button
                                    @click="openEdit({{ json_encode($customer->id) }}, {{ json_encode($customer->name) }}, {{ json_encode($customer->phone) }}, {{ json_encode($customer->address) }})"
                                    title="Edit"
                                    class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                                <form method="POST" action="{{ route('customers.toggle-status', $customer) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        title="{{ $customer->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-primary-container/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined">{{ $customer->is_active ? 'toggle_on' : 'toggle_off' }}</span>
                                    </button>
                                </form>
                                <button
                                    @click="confirmDelete('/customers/' + {{ $customer->id }})"
                                    title="Hapus"
                                    class="p-1.5 text-on-surface-variant hover:text-error hover:bg-error-container/10 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-16 text-on-surface-variant">
                            <span class="material-symbols-outlined text-6xl text-outline mb-4 inline-block">group</span>
                            <p class="text-lg font-medium text-on-surface-variant mb-2">Belum ada data pelanggan</p>
                            <p class="text-sm text-on-surface-variant mb-6">Tambah pelanggan pertama untuk memulai</p>
                            <button
                                @click="$dispatch('open-modal', 'add-customer')"
                                class="bg-primary hover:bg-primary-container text-white px-5 py-2.5 rounded-xl text-sm font-medium transition inline-flex items-center gap-2 shadow-sm">
                                <span class="material-symbols-outlined text-base">add</span>
                                Tambah Pelanggan
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-outline-variant/20">
        {{ $customers->links() }}
    </div>

</div>

</div>

<x-modal name="add-customer" :show="$errors->any() && old('_form_type') === 'add'" maxWidth="lg" focusable>
    <form action="{{ route('customers.store') }}" method="POST" @submit="submitting = true">
        @csrf
        <input type="hidden" name="_form_type" value="add">

        <div class="glass-panel rounded-xl shadow-xl flex flex-col">

            <div class="px-6 py-4 border-b border-outline-variant/30 flex items-center justify-between bg-surface-container-lowest">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL'1;">person</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-on-surface">Tambah Pelanggan</h2>
                        <p class="text-sm text-on-surface-variant">Tambahkan pelanggan baru ke sistem.</p>
                    </div>
                </div>
                <button type="button" @click="$dispatch('close-modal', 'add-customer')"
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
                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Nama Pelanggan</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">person</span>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                            placeholder="Masukkan nama..." required>
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-1.5">No HP (opsional)</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">phone</span>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                            placeholder="08xxxxxxxxxx">
                    </div>
                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Alamat (opsional)</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-3 text-outline text-lg">location_on</span>
                        <textarea name="address" rows="3"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm resize-none"
                            placeholder="Masukkan alamat...">{{ old('address') }}</textarea>
                    </div>
                    <x-input-error :messages="$errors->get('address')" class="mt-1" />
                </div>
            </div>

            <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant/20 flex justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'add-customer')"
                    class="px-4 py-2 text-sm text-on-surface-variant hover:text-on-surface font-medium">
                    Batal
                </button>
                <button type="submit" :disabled="submitting"
                    class="btn-primary-animate px-5 py-2.5 bg-primary text-white rounded-xl font-semibold text-sm flex items-center gap-2 shadow-md shadow-primary/20 hover:shadow-lg transition-all disabled:opacity-50">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL'1;">save</span>
                    Simpan Pelanggan
                </button>
            </div>

        </div>
    </form>
</x-modal>

<x-modal name="edit-customer" :show="$errors->any() && old('_form_type') === 'edit'" maxWidth="lg" focusable>
    <form id="edit-customer-form" method="POST" @submit="submitting = true">
        @csrf
        @method('PUT')
        <input type="hidden" name="_form_type" value="edit">
        <input type="hidden" name="_edit_id" id="edit-customer-id">

        <div class="glass-panel rounded-xl shadow-xl flex flex-col">

            <div class="px-6 py-4 border-b border-outline-variant/30 flex items-center justify-between bg-surface-container-lowest">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined" style="font-variation-settings:'FILL'1;">person</span>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-on-surface">Edit Pelanggan</h2>
                        <p class="text-sm text-on-surface-variant">Edit data pelanggan di sistem.</p>
                    </div>
                </div>
                <button type="button" @click="$dispatch('close-modal', 'edit-customer')"
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
                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Nama Pelanggan</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">person</span>
                        <input type="text" id="edit-name" name="name"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                            placeholder="Masukkan nama..." required>
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-1.5">No HP (opsional)</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">phone</span>
                        <input type="tel" id="edit-phone" name="phone"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                            placeholder="08xxxxxxxxxx">
                    </div>
                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-1.5">Alamat (opsional)</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-3 text-outline text-lg">location_on</span>
                        <textarea id="edit-address" name="address" rows="3"
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm resize-none"
                            placeholder="Masukkan alamat..."></textarea>
                    </div>
                    <x-input-error :messages="$errors->get('address')" class="mt-1" />
                </div>
            </div>

            <div class="px-6 py-4 bg-surface-container-low border-t border-outline-variant/20 flex justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'edit-customer')"
                    class="px-4 py-2 text-sm text-on-surface-variant hover:text-on-surface font-medium">
                    Batal
                </button>
                <button type="submit" :disabled="submitting"
                    class="btn-primary-animate px-5 py-2.5 bg-primary text-white rounded-xl font-semibold text-sm flex items-center gap-2 shadow-md shadow-primary/20 hover:shadow-lg transition-all disabled:opacity-50">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL'1;">save</span>
                    Simpan Pelanggan
                </button>
            </div>

        </div>
    </form>
</x-modal>

<x-modal name="confirm-delete-customer" focusable>
    <form id="delete-customer-form" method="POST" class="p-6" @submit="submitting = true">
        @csrf
        @method('DELETE')
        <div class="text-center">
            <span class="material-symbols-outlined text-error text-5xl mb-4">warning</span>
            <h2 class="text-lg font-bold text-slate-800 mb-2">Hapus Customer</h2>
            <p class="text-sm text-gray-500 mb-6">Yakin ingin menghapus customer ini? Tindakan ini tidak bisa dibatalkan.</p>
        </div>
        <div class="flex justify-end gap-3">
            <button
                type="button"
                @click="$dispatch('close-modal', 'confirm-delete-customer')"
                class="px-5 py-2.5 text-sm text-slate-600 hover:text-slate-800 font-medium">
                Batal
            </button>
            <button
                type="submit"
                :disabled="submitting"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                Ya, Hapus
            </button>
        </div>
    </form>
</x-modal>

</x-app-layout>