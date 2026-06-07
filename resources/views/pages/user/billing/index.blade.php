<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="user-kicker text-[11px] text-cyan-100/90">Billing</p>
            <h2 class="mt-2 font-outfit text-2xl font-bold leading-tight soft-gradient-text md:text-3xl">
                Pembayaran
            </h2>
            <p class="mt-2 text-sm text-slate-300/80">Pantau invoice Pakasir dan status aktivasi paket.</p>
        </div>
    </x-slot>

    <div class="space-y-6 py-4 md:py-6">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-2 lg:px-4">
            @if (session('billing_status'))
                <div class="rounded-2xl border border-cyan-200 bg-cyan-50 px-5 py-4 text-sm font-semibold text-cyan-800">
                    {{ session('billing_status') }}
                </div>
            @endif

            <section class="overflow-hidden rounded-[1.75rem] border border-sky-200 bg-white/88 shadow-[0_18px_38px_rgba(14,116,144,0.12)]">
                <div class="flex items-center justify-between border-b border-sky-200 p-5">
                    <div>
                        <p class="text-[11px] font-extrabold uppercase tracking-[0.22em] text-sky-700">Riwayat</p>
                        <h3 class="mt-2 font-outfit text-xl font-extrabold text-slate-950">Transaksi Pembayaran</h3>
                    </div>
                    <a href="{{ route('pricing') }}" class="rounded-xl bg-sky-500 px-4 py-2 text-sm font-bold text-white shadow-md shadow-sky-500/20 transition hover:bg-sky-600">Upgrade</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-sky-100 text-sm">
                        <thead class="bg-sky-50 text-left text-[11px] uppercase tracking-[0.18em] text-sky-700">
                            <tr>
                                <th class="px-5 py-3">Order</th>
                                <th class="px-5 py-3">Paket</th>
                                <th class="px-5 py-3">Nominal</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sky-100 text-slate-700">
                            @forelse ($payments as $payment)
                                <tr>
                                    <td class="px-5 py-4 font-semibold text-slate-950">{{ $payment->order_id }}</td>
                                    <td class="px-5 py-4">{{ $payment->plan_name }}</td>
                                    <td class="px-5 py-4">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-extrabold {{ $payment->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ strtoupper($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">{{ $payment->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-slate-500">Belum ada transaksi pembayaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-sky-100 px-5 py-4">
                    {{ $payments->links() }}
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
