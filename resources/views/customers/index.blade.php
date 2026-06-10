<x-app-layout>
    <div class="p-6">

        <h1 class="text-2xl font-bold mb-4">
            Data Customer
        </h1>

        <a href="{{ route('customers.create') }}" class="text-blue-500 underline mb-4 inline-block">
            Tambah Customer
        </a>

        <table class="w-full border-collapse border border-gray-300 mt-4">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2 text-left">Nama</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">No HP</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Alamat</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($customers as $customer)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">
                            {{ $customer->name }}
                        </td>

                        <td class="border border-gray-300 px-4 py-2">
                            {{ $customer->phone }}
                        </td>

                        <td class="border border-gray-300 px-4 py-2">
                            {{ $customer->address }}
                        </td>

                        <td class="border border-gray-300 px-4 py-2 flex gap-2">
                            <a href="{{ route('customers.edit', $customer->id) }}" class="text-yellow-600 underline">
                                Edit
                            </a>

                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="text-red-600 underline">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</x-app-layout>