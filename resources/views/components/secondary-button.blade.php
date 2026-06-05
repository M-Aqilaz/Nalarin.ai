<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-xl border border-sky-200 bg-white px-4 py-2 text-xs font-extrabold uppercase tracking-widest text-slate-700 shadow-sm transition ease-in-out duration-150 hover:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
