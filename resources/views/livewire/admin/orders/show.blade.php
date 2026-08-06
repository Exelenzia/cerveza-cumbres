<div class="space-y-6">
    <a href="{{ route('admin.orders.index') }}" wire:navigate class="text-sm text-primary hover:text-primary-hover">← Volver a pedidos</a>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-xl border border-border bg-surface-elevated p-6">
            <h2 class="mb-4 font-display text-lg uppercase tracking-wide text-text">Productos</h2>
            <div class="space-y-3">
                @foreach ($items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-text-muted/80">{{ $item->name }} × {{ $item->quantity }}</span>
                        <span class="text-text">S/ {{ number_format($item->subtotal, 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 space-y-2 border-t border-border pt-4 text-sm">
                <div class="flex justify-between text-text-muted/80">
                    <span>Subtotal</span>
                    <span>S/ {{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-text-muted/80">
                    <span>Envío</span>
                    <span>S/ {{ number_format($order->shipping_cost, 2) }}</span>
                </div>
                <div class="flex justify-between font-display text-lg text-primary">
                    <span>Total</span>
                    <span>S/ {{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            <h2 class="mb-3 mt-8 font-display text-lg uppercase tracking-wide text-text">Cliente y entrega</h2>
            <p class="text-sm text-text-muted/80">{{ $order->customer_name }} · {{ $order->customer_email }}</p>
            @if ($order->customer_phone)
                <p class="text-sm text-text-muted/80">Tel: {{ $order->customer_phone }}</p>
            @endif
            <p class="mt-2 text-sm text-text-muted/80">{{ $order->shipping_address }}, {{ $order->shipping_city }}</p>
            @if ($order->notes)
                <p class="mt-2 text-sm text-text-muted/60">Notas: {{ $order->notes }}</p>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-border bg-surface-elevated p-6">
                <h2 class="mb-3 font-display text-lg uppercase tracking-wide text-text">Estado</h2>
                <p class="mb-4 text-sm text-text-muted/70">Estado actual: <span class="text-primary">{{ $order->statusLabel() }}</span></p>
                <div class="space-y-2">
                    @foreach ($statuses as $s)
                        <button
                            wire:click="updateStatus('{{ $s }}')"
                            class="w-full rounded-lg border px-4 py-2 text-left text-sm capitalize transition {{ $order->status === $s ? 'border-primary bg-primary text-primary-on' : 'border-border text-text-muted hover:border-primary' }}"
                        >
                            {{ $s }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-border bg-surface-elevated p-6">
                <h2 class="mb-3 font-display text-lg uppercase tracking-wide text-text">Pago</h2>
                <p class="text-sm text-text-muted/80">Método: {{ $order->payment_method ?? '—' }}</p>
                @if ($order->paid_at)
                    <p class="text-sm text-text-muted/80">Pagado: {{ $order->paid_at->format('d/m/Y H:i') }}</p>
                @endif
                <p class="mt-2 text-sm text-text-muted/60">Ref: {{ $order->payment_reference ?? '—' }}</p>
            </div>

            <div class="rounded-xl border border-border bg-surface-elevated p-6">
                <h2 class="mb-3 font-display text-lg uppercase tracking-wide text-text">Comprobante electrónico</h2>

                @if ($invoiceError)
                    <div class="mb-3 rounded-lg border border-red-500/40 bg-red-500/10 px-3 py-2 text-sm text-red-300">
                        {{ $invoiceError }}
                    </div>
                @endif

                @forelse ($invoices as $invoice)
                    <div class="mb-2 rounded-lg border border-border px-3 py-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-text">{{ $invoice->tipoLabel() }} {{ $invoice->numeroCompleto() }}</span>
                            <span class="{{ $invoice->estado === 'aceptado' ? 'text-green-400' : ($invoice->estado === 'error' || $invoice->estado === 'rechazado' ? 'text-red-400' : 'text-primary') }}">
                                {{ ucfirst($invoice->estado) }}
                            </span>
                        </div>
                        @if ($invoice->sunat_response_description)
                            <p class="mt-1 text-xs text-text-muted/60">{{ $invoice->sunat_response_description }}</p>
                        @endif
                    </div>
                @empty
                    <p class="mb-3 text-sm text-text-muted/60">Este pedido aún no tiene comprobante emitido.</p>
                @endforelse

                <div class="mt-3 flex items-center gap-2">
                    <select wire:model="tipoComprobante" class="rounded-lg border border-border bg-surface px-3 py-2 text-sm text-text focus:border-primary focus:outline-none">
                        <option value="03">Boleta</option>
                        <option value="01">Factura</option>
                    </select>
                    <button wire:click="issueInvoice" wire:loading.attr="disabled" class="flex-1 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-primary-on hover:bg-primary-hover disabled:opacity-50">
                        Emitir comprobante
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
