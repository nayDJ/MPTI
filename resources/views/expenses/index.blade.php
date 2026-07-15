@section('title', 'Data Pengeluaran')
<x-app-layout>

<div class="min-h-screen bg-[#F3F6F8] p-8" x-data="{
    submitting: false,
    showNewCategory: false,
    newCategoryName: '',
    categoryList: @js($categories->pluck('name')),
    openEdit(id, desc, amount, category, date) {
        document.getElementById('edit-expense-form').action = '/expenses/' + id;
        document.getElementById('edit-description').value = desc;
        document.getElementById('edit-amount').value = amount;
        document.getElementById('edit-category').value = category;
        document.getElementById('edit-expense-date').value = date;
        document.getElementById('edit-expense-id').value = id;
        this.$dispatch('open-modal', 'edit-expense');
    },
    confirmDelete(url) {
        document.getElementById('delete-expense-form').action = url;
        this.$dispatch('open-modal', 'confirm-delete-expense');
    },
    async addCategory() {
        if (!this.newCategoryName.trim()) return;
        try {
            const res = await fetch('/categories', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content
                },
                body: JSON.stringify({name: this.newCategoryName})
            });
            const cat = await res.json();
            if (cat.name) {
                this.categoryList.push(cat.name);
                document.getElementById('category').value = cat.name;
                this.showNewCategory = false;
                this.newCategoryName = '';
            }
        } catch (e) {
            alert('Gagal menambah kategori');
        }
    },
    init() {
        @if(old('_form_type') === 'edit' && old('_edit_id'))
            document.getElementById('edit-expense-form').action = '/expenses/' + @js(old('_edit_id'));
            document.getElementById('edit-description').value = @js(old('description'));
            document.getElementById('edit-amount').value = @js(old('amount'));
            document.getElementById('edit-category').value = @js(old('category'));
            document.getElementById('edit-expense-date').value = @js(old('expense_date'));
            document.getElementById('edit-expense-id').value = @js(old('_edit_id'));
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
                <span class="text-slate-600">Data Pengeluaran</span>
            </nav>

            <h1 class="text-3xl font-bold text-[#0F6E8C]">
                Data Pengeluaran
            </h1>

            <p class="text-gray-500 mt-1">
                Catat dan pantau seluruh pengeluaran operasional NNQUA.
            </p>
        </div>

        <button
            @click="$dispatch('open-modal', 'add-expense')"
            class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-5 py-3 rounded-xl shadow-sm transition whitespace-nowrap">
            + Tambah Pengeluaran
        </button>

    </div>

    {{-- Period --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 inline-block mb-6">
        <div class="flex gap-2">
            <a href="{{ request()->fullUrlWithQuery(['period' => null]) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition
                {{ !request('period') ? 'bg-[#0F6E8C] text-white' : 'bg-gray-100 text-slate-600 hover:bg-gray-200' }}">
                Semua
            </a>
            <a href="{{ request()->fullUrlWithQuery(['period' => 'harian']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition
                {{ request('period') === 'harian' ? 'bg-[#0F6E8C] text-white' : 'bg-gray-100 text-slate-600 hover:bg-gray-200' }}">
                Harian
            </a>
            <a href="{{ request()->fullUrlWithQuery(['period' => 'bulanan']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition
                {{ request('period') === 'bulanan' ? 'bg-[#0F6E8C] text-white' : 'bg-gray-100 text-slate-600 hover:bg-gray-200' }}">
                Bulanan
            </a>
            <a href="{{ request()->fullUrlWithQuery(['period' => 'tahunan']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition
                {{ request('period') === 'tahunan' ? 'bg-[#0F6E8C] text-white' : 'bg-gray-100 text-slate-600 hover:bg-gray-200' }}">
                Tahunan
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

        <div class="bg-white rounded-2xl shadow-sm border p-6">
            <p class="text-sm text-gray-500">Total Pengeluaran</p>
            <h2 class="text-3xl font-bold text-red-500 mt-2">
                Rp {{ number_format($totalExpense) }}
            </h2>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border p-6">
            <p class="text-sm text-gray-500">Jumlah Transaksi</p>
            <h2 class="text-3xl font-bold mt-2">{{ $totalItems }}</h2>
        </div>

    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="p-6 border-b flex items-center justify-between gap-4 flex-wrap">
            <h2 class="text-xl font-bold text-slate-800">Daftar Pengeluaran</h2>

            <div class="flex gap-2 flex-wrap items-center">
                <a href="{{ route('expenses.index') }}"
                   class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 {{ request()->has('category') || request()->has('search') || request()->has('from') || request()->has('to') ? '' : 'hidden' }}">
                    Reset
                </a>

                <form method="GET" action="{{ route('expenses.index') }}" class="flex gap-2 flex-wrap items-center">
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Cari pengeluaran..."
                        class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-48">

                    <select name="category"
                        class="border border-gray-300 rounded-lg px-4 py-2 text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" @selected(request('category') === $cat->name)>{{ $cat->name }}</option>
                        @endforeach
                    </select>

                    <input type="date" name="from" value="{{ $from ?? '' }}"
                        class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-36">

                    <input type="date" name="to" value="{{ $to ?? '' }}"
                        class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-36">

                    <button type="submit"
                        class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-4 py-2 rounded-lg text-sm transition">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">

                <thead>
                    <tr class="bg-slate-50 border-b">
                        <th class="p-4 text-left">Tanggal</th>
                        <th class="p-4 text-left">Deskripsi</th>
                        <th class="p-4 text-left">Kategori</th>
                        <th class="p-4 text-right">Jumlah</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($expenses as $expense)
                        <tr class="border-b hover:bg-slate-50">
                            <td class="p-4">{{ $expense->expense_date }}</td>
                            <td class="p-4 font-medium">{{ $expense->description }}</td>
                            <td class="p-4">
                                <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $expense->category }}
                                </span>
                            </td>
                            <td class="p-4 text-right font-semibold text-red-600">
                                Rp {{ number_format($expense->amount) }}
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('expenses.show', $expense->id) }}"
                                        title="Detail"
                                        class="bg-green-500 hover:bg-green-600 text-white p-2 rounded-lg transition relative group">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">Detail</span>
                                    </a>
                                    <button
                                        @click="openEdit(@js($expense->id), @js($expense->description), @js($expense->amount), @js($expense->category), @js($expense->expense_date))"
                                        title="Edit"
                                        class="bg-gray-400 hover:bg-gray-500 text-white p-2 rounded-lg transition relative group">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    <button
                                        @click="confirmDelete('/expenses/' + {{ $expense->id }})"
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-400 mb-2">Belum ada data pengeluaran</p>
                                <p class="text-sm text-gray-400 mb-6">Catat pengeluaran pertama Anda</p>
                                <button
                                    @click="$dispatch('open-modal', 'add-expense')"
                                    class="bg-[#0F6E8C] hover:bg-[#0b5b74] text-white px-5 py-2.5 rounded-lg text-sm font-medium transition inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Tambah Pengeluaran
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <div class="p-4 border-t flex items-center justify-between">
            <p class="text-sm text-slate-500">
                Menampilkan {{ $expenses->firstItem() ?? 0 }}-{{ $expenses->lastItem() ?? 0 }} dari {{ $expenses->total() }} item
            </p>
            {{ $expenses->links() }}
        </div>

    </div>

</div>

{{-- Modal Tambah --}}
<x-modal name="add-expense" :show="$errors->any() && old('_form_type') === 'add'" focusable>
    <form action="{{ route('expenses.store') }}" method="POST" class="p-6"
          x-data="{
              showNewCategory: false,
              newCategoryName: '',
              async addCategory() {
                  if (!this.newCategoryName.trim()) return;
                  try {
                      const res = await fetch('/categories', {
                          method: 'POST',
                          headers: {
                              'Content-Type': 'application/json',
                              'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content
                          },
                          body: JSON.stringify({name: this.newCategoryName})
                      });
                      const cat = await res.json();
                      if (!cat.name) return;
                      const opt = document.createElement('option');
                      opt.value = cat.name;
                      opt.textContent = cat.name;
                      document.getElementById('category').appendChild(opt);
                      document.getElementById('category').value = cat.name;
                      this.showNewCategory = false;
                      this.newCategoryName = '';
                  } catch (e) {
                      alert('Gagal menambah kategori');
                  }
              }
          }" @submit="submitting = true">
        @csrf
        <input type="hidden" name="_form_type" value="add">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Tambah Pengeluaran</h2>

        <div class="space-y-4">
            <div>
                <x-input-label for="description" value="Deskripsi" />
                <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('description')" />
            </div>
            <div>
                <x-input-label for="amount" value="Jumlah" />
                <x-text-input id="amount" name="amount" type="number" step="0.01" min="0" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('amount')" />
            </div>
            <div>
                <x-input-label for="category" value="Kategori" />
                <select id="category" name="category"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                    required>
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <button type="button" @click="showNewCategory = true"
                    x-show="!showNewCategory"
                    class="mt-2 px-3 py-1 text-xs font-medium border border-[#0F6E8C] text-[#0F6E8C] rounded-lg hover:bg-[#0F6E8C] hover:text-white transition">
                    + Tambah Kategori Baru
                </button>
                <div x-show="showNewCategory" class="mt-2 flex gap-2">
                    <input type="text" x-model="newCategoryName"
                        placeholder="Nama kategori"
                        class="flex-1 border border-gray-300 rounded-md px-3 py-1.5 text-sm">
                    <button type="button" @click="addCategory()"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-xs transition">
                        Simpan
                    </button>
                    <button type="button" @click="showNewCategory = false; newCategoryName = ''"
                        class="text-xs text-slate-600 hover:text-slate-800 px-2">
                        Batal
                    </button>
                </div>
                <x-input-error :messages="$errors->get('category')" />
            </div>
            <div>
                <x-input-label for="expense_date" value="Tanggal" />
                <x-text-input id="expense_date" name="expense_date" type="date"
                    value="{{ old('expense_date', now()->toDateString()) }}"
                    class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('expense_date')" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" @click="$dispatch('close-modal', 'add-expense')"
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

{{-- Modal Edit --}}
<x-modal name="edit-expense" :show="$errors->any() && old('_form_type') === 'edit'" focusable>
    <form id="edit-expense-form" method="POST" class="p-6" @submit="submitting = true">
        @csrf
        @method('PUT')
        <input type="hidden" name="_form_type" value="edit">
        <input type="hidden" name="_edit_id" id="edit-expense-id">
        <h2 class="text-lg font-bold text-slate-800 mb-4">Edit Pengeluaran</h2>

        <div class="space-y-4">
            <div>
                <x-input-label for="edit-description" value="Deskripsi" />
                <x-text-input id="edit-description" name="description" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('description')" />
            </div>
            <div>
                <x-input-label for="edit-amount" value="Jumlah" />
                <x-text-input id="edit-amount" name="amount" type="number" step="0.01" min="0" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('amount')" />
            </div>
            <div>
                <x-input-label for="edit-category" value="Kategori" />
                <select id="edit-category" name="category"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                    required>
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category')" />
            </div>
            <div>
                <x-input-label for="edit-expense-date" value="Tanggal" />
                <x-text-input id="edit-expense-date" name="expense_date" type="date" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('expense_date')" />
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" @click="$dispatch('close-modal', 'edit-expense')"
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
<x-modal name="confirm-delete-expense" focusable>
    <form id="delete-expense-form" method="POST" class="p-6" @submit="submitting = true">
        @csrf
        @method('DELETE')
        <div class="text-center">
            <svg class="mx-auto h-14 w-14 text-red-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <h2 class="text-lg font-bold text-slate-800 mb-2">Hapus Pengeluaran</h2>
            <p class="text-sm text-gray-500 mb-6">Yakin ingin menghapus pengeluaran ini? Tindakan ini tidak bisa dibatalkan.</p>
        </div>
        <div class="flex justify-end gap-3">
            <button type="button" @click="$dispatch('close-modal', 'confirm-delete-expense')"
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
