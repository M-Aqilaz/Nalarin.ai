@php
    $filter = $analyticsFilter ?? [
        'range' => '7d',
        'start_date' => null,
        'end_date' => null,
        'label' => '7 hari terakhir',
    ];
@endphp

<form method="GET" class="flex flex-wrap items-center gap-2">
    <select
        name="range"
        class="rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-zinc-100"
        onchange="this.form.submit()"
    >
        <option value="today" @selected($filter['range'] === 'today')>Hari ini</option>
        <option value="7d" @selected($filter['range'] === '7d')>7 hari</option>
        <option value="30d" @selected($filter['range'] === '30d')>30 hari</option>
        <option value="all" @selected($filter['range'] === 'all')>Semua waktu</option>
        <option value="custom" @selected($filter['range'] === 'custom')>Custom</option>
    </select>

    <input
        type="date"
        name="start_date"
        value="{{ $filter['start_date'] }}"
        class="rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-zinc-100"
    >
    <input
        type="date"
        name="end_date"
        value="{{ $filter['end_date'] }}"
        class="rounded-xl border border-zinc-700 bg-zinc-950 px-3 py-2 text-sm text-zinc-100"
    >

    <button type="submit" class="rounded-xl border border-sky-500/30 bg-sky-500/10 px-4 py-2 text-sm font-semibold text-sky-200 transition hover:bg-sky-500/15">
        Filter
    </button>
</form>
