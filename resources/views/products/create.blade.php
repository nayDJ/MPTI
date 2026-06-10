<x-app-layout>

<form action="{{ route('products.store') }}" method="POST">

    @csrf

    <input
        type="text"
        name="name"
        placeholder="Nama Produk"
    >

    <input
        type="number"
        name="stock"
        placeholder="Stok"
    >

    <input
        type="number"
        name="price"
        placeholder="Harga"
    >

    <button type="submit">
        Simpan
    </button>

</form>

</x-app-layout>