<x-app-layout>
    <div class="p-6 max-w-xl mx-auto">

        <h1 class="text-2xl font-bold mb-6">
            Edit Customer
        </h1>

        <form
            action="{{ route('customers.update', $customer->id) }}"
            method="POST"
            class="space-y-4"
        >
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label class="block font-semibold mb-1">Nama</label>
                <input
                    type="text"
                    name="name"
                    value="{{ $customer->name }}"
                    class="w-full border border-gray-300 rounded px-3 py-2"
                >
            </div>

            <!-- Address -->
            <div>
                <label class="block font-semibold mb-1">Alamat</label>
                <textarea
                    name="address"
                    class="w-full border border-gray-300 rounded px-3 py-2"
                >{{ $customer->address }}</textarea>
            </div>

            <!-- Phone -->
            <div>
                <label class="block font-semibold mb-1">No HP</label>
                <input
                    type="text"
                    name="phone"
                    value="{{ $customer->phone }}"
                    class="w-full border border-gray-300 rounded px-3 py-2"
                >
            </div>

            <!-- Button -->
            <button
                type="submit"
                class="bg-blue-500 text-black px-4 py-2 rounded hover:bg-blue-600"
            >
                Update
            </button>

        </form>

    </div>
</x-app-layout>