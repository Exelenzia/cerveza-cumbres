<div class="space-y-6">
    <a href="{{ route('admin.orders.index') }}" wire:navigate class="text-sm text-gold-400 hover:text-gold-300">← Volver a pedidos</a>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
            <h2 class="mb-4 font-display text-lg uppercase tracking-wide text-cream-50">Productos</h2>
            <div class="space-y-3">
                @foreach ($items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-cream-200/80">{{ $item->name }} × {{ $item->quantity }}</span>
                        <span class="text-cream-100">S/ {{ number_format($item->subtotal, 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 space-y-2 border-t border-cumbre-700 pt-4 text-sm">
                <div class="flex justify-between text-cream-200/80">
                    <span>Subtotal</span>
                    <span>S/ {{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-cream-200/80">
                    <span>Envío</span>
                    <span>S/ {{ number_format($order->shipping_cost, 2) }}</span>
                </div>
                <div class="flex justify-between font-display text-lg text-gold-400">
                    <span>Total</span>
                    <span>S/ {{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            <h2 class="mb-3 mt-8 font-display text-lg uppercase tracking-wide text-cream-50">Cliente y entrega</h2>
            <p class="text-sm text-cream-200/80">{{ $order->customer_name }} · {{ $order->customer_email }}</p>
            @if ($order->customer_phone)
                <p class="text-sm text-cream-200/80">Tel: {{ $order->customer_phone }}</p>
            @endif
            <p class="mt-2 text-sm text-cream-200/80">{{ $order->shipping_address }}, {{ $order->shipping_city }}</p>
            @if ($order->notes)
                <p class="mt-2 text-sm text-cream-200/60">Notas: {{ $order->notes }}</p>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
                <h2 class="mb-3 font-display text-lg uppercase tracking-wide text-cream-50">Estado</h2>
                <p class="mb-4 text-sm text-cream-200/70">Estado actual: <span class="text-gold-400">{{ $order->statusLabel() }}</span></p>
                <div class="space-y-2">
                    @foreach ($statuses as $s)
                        <button
                            wire:click="updateStatus('{{ $s }}')"
                            class="w-full rounded-lg border px-4 py-2 text-left text-sm capitalize transition {{ $order->status === $s ? 'border-gold-500 bg-gold-500 text-cumbre-950' : 'border-cumbre-700 text-cream-200 hover:border-gold-500' }}"
                        >
                            {{ $s }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
                <h2 class="mb-3 font-display text-lg uppercase tracking-wide text-cream-50">Pago</h2>
                <p class="text-sm text-cream-200/80">Método: {{ $order->payment_method ?? '—' }}</p>
                @if ($order->paid_at)
                    <p class="text-sm text-cream-200/80">Pagado: {{ $order->paid_at->format('d/m/Y H:i') }}</p>
                @endif
                <p class="mt-2 text-sm text-cream-200/60">Ref: {{ $order->payment_reference ?? '—' }}</p>
            </div>

            <div class="rounded-xl border border-cumbre-700 bg-cumbre-900 p-6">
                <h2 class="mb-3 font-display text-lg uppercase tracking-wide text-cream-50">Comprobante electrónico</h2>

                @if ($invoiceError)
                    <div class="mb-3 rounded-lg border border-red-500/40 bg-red-500/10 px-3 py-2 text-sm text-red-300">
                        {{ $invoiceError }}
                    </div>
                @endif

                @forelse ($invoices as $invoice)
                    <div class="mb-2 rounded-lg border border-cumbre-800 px-3 py-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-cream-100">{{ $invoice->tipoLabel() }} {{ $invoice->numeroCompleto() }}</span>
                            <span class="{{ $invoice->estado === 'aceptado' ? 'text-green-400' : ($invoice->estado === 'error' || $invoice->estado === 'rechazado' ? 'text-red-400' : 'text-gold-400') }}">
                                {{ ucfirst($invoice->estado) }}
                            </span>
                        </div>
                        @if ($invoice->sunat_response_description)
                            <p class="mt-1 text-xs text-cream-200/60">{{ $invoice->sunat_response_description }}</p>
                        @endif
                    </div>
                @empty
                    <p class="mb-3 text-sm text-cream-200/60">Este pedido aún no tiene comprobante emitido.</p>
                @endforelse

                <div class="mt-3 flex items-center gap-2">
                    <select wire:model="tipoComprobante" class="rounded-lg border border-cumbre-700 bg-cumbre-950 px-3 py-2 text-sm text-cream-50 focus:border-gold-500 focus:outline-none">
                        <option value="03">Boleta</option>
                        <option value="01">Factura</option>
                    </select>
                    <button wire:click="issueInvoice" wire:loading.attr="disabled" class="flex-1 rounded-lg bg-gold-500 px-4 py-2 text-sm font-semibold text-cumbre-950 hover:bg-gold-400 disabled:opacity-50">
                        Emitir comprobante
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
