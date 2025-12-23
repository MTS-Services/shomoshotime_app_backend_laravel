@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'bg-slate-800 border-gray-800 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs  text-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:outline-hidden']) }}>
