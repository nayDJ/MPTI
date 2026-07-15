@section('title', 'Data Customer')
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
    <div class="flex justify-between items-center mb-8">

        <div>

            <nav class="text-sm text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-[#0F6E8C] transition">Dashboard</a>
                <span class="mx-1">›</span>
                <span class="text-slate-600">Data Customer</span>
            </nav>

            <h1 class="text-3xl font-bold text-[#0F6E8C]">
                Data Customer
            </h1>

            <p class="text-gray-500 mt-1">
                Kelola seluruh pelanggan NNQUA
            </p>

        </div>

        <button
            @click="$dispatch('open-modal', 'add-customer')"
            class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-5 py-3 rounded-xl shadow-sm transition">

            + Tambah Customer

        </button>

    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

            <p class="text-sm text-gray-500">
                Total Customer
            </p>

            <h2 class="text-3xl font-bold text-slate-800 mt-3">
                {{ $totalCustomers }}
            </h2>

        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

            <p class="text-sm text-gray-500">
                Customer Baru Hari Ini
            </p>

            <h2 class="text-3xl font-bold text-green-600 mt-3">
                {{ $newCustomers }}
            </h2>

        </div>

    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="p-6 border-b flex items-center justify-between gap-4">

            <h2 class="text-xl font-bold text-slate-800">
                Daftar Customer
            </h2>

            <form method="GET" action="{{ route('customers.index') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ $search ?? '' }}"
                    placeholder="Cari nama atau no HP..."
                    class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-64">
                <button type="submit"
                    class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-4 py-2 rounded-lg text-sm transition">
                    Cari
                </button>
                @if($search)
                    <a href="{{ route('customers.index') }}"
                        class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
                        Reset
                    </a>
                @endif
            </form>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>

                    <tr class="bg-slate-50 border-b">

                        <th class="p-4 text-left">
                            Nama
                        </th>

                        <th class="p-4 text-left">
                            No HP
                        </th>

                        <th class="p-4 text-left">
                            Alamat
                        </th>

                        <th class="p-4 text-center">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($customers as $customer)

                        <tr class="border-b hover:bg-slate-50">

                            <td class="p-4 font-medium">
                                {{ $customer->name }}
                            </td>

                            <td class="p-4">
                                {{ $customer->phone }}
                            </td>

                            <td class="p-4 text-gray-600">
                                {{ $customer->address }}
                            </td>

                            <td class="p-4">

                                <div class="flex items-center justify-center gap-2">

                                    <a href="{{ route('customers.show', $customer->id) }}"
                                       title="Lihat"
                                       class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg transition relative group">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">Lihat</span>
                                    </a>

                                    <button
                                        @click="openEdit({{ json_encode($customer->id) }}, {{ json_encode($customer->name) }}, {{ json_encode($customer->phone) }}, {{ json_encode($customer->address) }})"
                                        title="Edit"
                                        class="bg-gray-400 hover:bg-gray-500 text-white p-2 rounded-lg transition relative group">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>

                                    <button
                                        @click="confirmDelete('/customers/' + {{ $customer->id }})"
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

                            <td colspan="4"
                                class="text-center py-16 text-gray-500">

                                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>

                                <p class="text-lg font-medium text-gray-400 mb-2">
                                    Belum ada data customer
                                </p>

                                <p class="text-sm text-gray-400 mb-6">
                                    Tambah pelanggan pertama untuk memulai
                                </p>

                                <button
                                    @click="$dispatch('open-modal', 'add-customer')"
                                    class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                    Tambah Customer
                                </button>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="p-4 border-t">
            {{ $customers->links() }}
        </div>

    </div>

</div>

<x-modal name="add-customer" :show="$errors->any() && old('_form_type') === 'add'" focusable>
    <form action="{{ route('customers.store') }}" method="POST" class="p-6" @submit="submitting = true">
        @csrf
        <input type="hidden" name="_form_type" value="add">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Tambah Customer</h2>

        <div class="space-y-4">
            <div>
                <x-input-label for="name" value="Nama Customer" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('name')" />
            </div>
            <div>
                <x-input-label for="phone" value="No HP" />
                <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('phone')" />
            </div>
            <div>
                <x-input-label for="address" value="Alamat" />
                <textarea id="address" name="address"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                    rows="3" required></textarea>
                <x-input-error :messages="$errors->get('address')" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button
                type="button"
                @click="$dispatch('close-modal', 'add-customer')"
                class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
                Batal
            </button>
            <button
                type="submit"
                :disabled="submitting"
                class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-5 py-2 rounded-lg text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                Simpan
            </button>
        </div>
    </form>
</x-modal>

<x-modal name="edit-customer" :show="$errors->any() && old('_form_type') === 'edit'" focusable>
    <form id="edit-customer-form" method="POST" class="p-6" @submit="submitting = true">
        @csrf
        @method('PUT')
        <input type="hidden" name="_form_type" value="edit">
        <input type="hidden" name="_edit_id" id="edit-customer-id">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Edit Customer</h2>

        <div class="space-y-4">
            <div>
                <x-input-label for="edit-name" value="Nama Customer" />
                <x-text-input id="edit-name" name="name" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('name')" />
            </div>
            <div>
                <x-input-label for="edit-phone" value="No HP" />
                <x-text-input id="edit-phone" name="phone" type="tel" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('phone')" />
            </div>
            <div>
                <x-input-label for="edit-address" value="Alamat" />
                <textarea id="edit-address" name="address"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                    rows="3" required></textarea>
                <x-input-error :messages="$errors->get('address')" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button
                type="button"
                @click="$dispatch('close-modal', 'edit-customer')"
                class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800">
                Batal
            </button>
            <button
                type="submit"
                :disabled="submitting"
                class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-5 py-2 rounded-lg text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                Simpan
            </button>
        </div>
    </form>
</x-modal>

<x-modal name="confirm-delete-customer" focusable>
    <form id="delete-customer-form" method="POST" class="p-6" @submit="submitting = true">
        @csrf
        @method('DELETE')
        <div class="text-center">
            <svg class="mx-auto h-14 w-14 text-red-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
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