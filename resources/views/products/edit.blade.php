<x-app-layout>

<form
    action="{{ route('products.update', $product->id) }}"
    method="POST"
>

    @csrf
    @method('PUT')

    <input
        type="text"
        name="name"
        value="{{ $product->name }}"
    >

    <input
        type="number"
        name="stock"
        value="{{ $product->stock }}"
    >

    <input
        type="number"
        name="price"
        value="{{ $product->price }}"
    >

    <button type="submit">
        Update
    </button>

</form>

</x-app-layout>