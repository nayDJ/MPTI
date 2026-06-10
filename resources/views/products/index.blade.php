<x-app-layout>
    <div class="p-6">

        <h1 class="text-2xl font-bold mb-4">
            Data Produk
        </h1>

        <a href="{{ route('products.create') }}" class="text-blue-500 underline mb-4 inline-block">
            Tambah Produk
        </a>

        <table class="w-full border border-gray-300 mt-4">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-4 py-2 text-left">Nama</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Stok</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Harga</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2">
                            {{ $product->name }}
                        </td>

                        <td class="border border-gray-300 px-4 py-2">
                            {{ $product->stock }}
                        </td>

                        <td class="border border-gray-300 px-4 py-2">
                            {{ $product->price }}
                        </td>

                        <td class="border border-gray-300 px-4 py-2 flex gap-2">
                            <a href="{{ route('products.edit', $product->id) }}" class="text-yellow-600 underline">
                                Edit
                            </a>

                            <form action="{{ route('products.destroy', $product->id) }}" method="POST">
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