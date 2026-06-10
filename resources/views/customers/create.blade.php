<x-app-layout>

<form action="{{ route('customers.store') }}" method="POST">

    @csrf

    <input
        type="text"
        name="name"
        placeholder="Nama Customer"
    >

    <textarea
        name="address"
        placeholder="Alamat"
    ></textarea>

    <input
        type="text"
        name="phone"
        placeholder="Nomor HP"
    >

    <button type="submit">
        Simpan
    </button>

</form>

</x-app-layout>