<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#0F6E8C] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0b5b74] focus:bg-[#0b5b74] active:bg-[#0a4d63] focus:outline-none focus:ring-2 focus:ring-[#0F6E8C] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
