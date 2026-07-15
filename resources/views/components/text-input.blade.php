@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#0F6E8C] focus:ring-[#0F6E8C] rounded-md shadow-sm']) }}>
