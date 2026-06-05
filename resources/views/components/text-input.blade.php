@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border border-sky-200 bg-white px-3 py-2 text-slate-950 shadow-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500 disabled:bg-slate-100 disabled:text-slate-500']) }}>
