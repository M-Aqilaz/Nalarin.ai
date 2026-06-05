<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-xl border border-transparent bg-sky-500 px-4 py-2 text-xs font-extrabold uppercase tracking-widest text-white shadow-sm shadow-sky-500/20 transition ease-in-out duration-150 hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 active:bg-sky-700']) }}>
    {{ $slot }}
</button>
