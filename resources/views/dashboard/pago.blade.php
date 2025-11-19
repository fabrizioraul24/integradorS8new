@php
    use Illuminate\Support\Facades\Auth;
    $primary = '#4e6baf';
    $accent = '#86acd4';
    $qrImage = asset('storage/images/QR.jpeg');
    $mockItems = $cartItems ?? [
        ['name' => 'Leche Entera PIL 1L', 'qty' => 2, 'price' => 8.50],
        ['name' => 'Yogurt Frutilla 1L', 'qty' => 1, 'price' => 12.00],
    ];
    $items = $cartItems ?? $mockItems;
    $subtotal = collect($items)->sum(fn($i) => ($i['qty'] ?? 0) * ($i['price'] ?? 0));
    $shipping = $shipping ?? 0;
    $total = $subtotal + $shipping;
    $cartJson = json_encode($items);
    $recsFromCart = collect($items)->pluck('name')->take(5);
    $downloadUrl = isset($receiptNumber) && $receiptNumber ? route('dashboard.payment.receipt.download', $receiptNumber) : null;
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasarela de Pago | PIL Andina</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css">
    <style>
        :root { --primary: {{ $primary }}; --accent: {{ $accent }}; }
        * { box-sizing: border-box; margin:0; padding:0; }
        body {
            font-family:'Inter', Arial, sans-serif;
            background: linear-gradient(135deg, #3a5899 0%, #4e6baf 30%, #6b87c7 60%, #86acd4 100%);
            color:#0f172a;
            min-height:100vh;
            padding:32px 24px 40px;
        }
        .container { max-width: 1220px; margin:0 auto; display:grid; gap:28px; grid-template-columns: 1.1fr 0.9fr; }
        .glass { background: rgba(255,255,255,0.14); border:1px solid rgba(255,255,255,0.25); border-radius:18px; backdrop-filter: blur(14px); box-shadow:0 20px 45px rgba(0,0,0,0.18); }
        .header { padding:18px 22px; display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:18px; }
        .brand { display:flex; align-items:center; gap:12px; color:#fff; font-weight:900; letter-spacing:0.03em; font-size:1.05rem; }
        .user { display:flex; align-items:center; gap:8px; color:#e9efff; font-weight:700; }
        .pill { padding:0.55rem 1.1rem; border-radius:999px; border:1px solid rgba(255,255,255,0.2); background:rgba(255,255,255,0.1); color:#fff; }
        .back-btn { display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border-radius:12px; border:1px solid rgba(255,255,255,0.25); background:rgba(255,255,255,0.08); color:#fff; text-decoration:none; font-weight:700; }
        .grid { display:grid; gap:16px; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); padding:0 18px 18px; }
        .card { background: rgba(255,255,255,0.92); border-radius:18px; padding:18px; border:1px solid rgba(15,23,42,0.08); box-shadow:0 12px 32px rgba(15,23,42,0.12); color:#0f172a; }
        .card h3 { margin:0 0 8px; }
        .pay-option { display:flex; align-items:flex-start; gap:12px; padding:14px; border-radius:14px; border:1px solid rgba(15,23,42,0.12); cursor:pointer; transition:0.2s; background:#f8fbff; color:#0f172a; }
        .pay-option.active { border-color: var(--primary); box-shadow:0 10px 24px rgba(78,107,175,0.2); background:#fff; }
        .pay-option input { accent-color: var(--primary); margin-top:4px; }
        .section { padding:18px; }
        .qr-box { display:flex; flex-direction:column; align-items:center; gap:12px; padding:18px; border-radius:14px; border:1px solid rgba(15,23,42,0.08); background:#f8fafc; }
        .qr-box img { width:200px; height:200px; object-fit:cover; border-radius:10px; border:1px solid rgba(15,23,42,0.08); }
        .form-row { display:grid; gap:12px; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); margin-top:10px; }
        .input { width:100%; padding:12px; border-radius:12px; border:1px solid rgba(15,23,42,0.12); background:#fff; }
        .summary { padding:20px; display:flex; flex-direction:column; gap:14px; }
        .item { display:flex; justify-content:space-between; gap:10px; }
        .total { display:flex; justify-content:space-between; align-items:center; font-weight:800; font-size:1.1rem; }
        .btn-primary { width:100%; padding:14px; border:none; border-radius:12px; background: linear-gradient(135deg, var(--primary), var(--accent)); color:#fff; font-weight:800; cursor:pointer; font-size:1rem; box-shadow:0 14px 32px rgba(78,107,175,0.25); }
        .note { color:#475569; font-size:0.9rem; }
        .badge { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:10px; background:rgba(78,107,175,0.12); color:#0f172a; font-weight:700; font-size:0.85rem; }
        @media (max-width: 980px) {
            .container { grid-template-columns: 1fr; }
        }
        .modal { position:fixed; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.55); z-index:30; padding:1rem; }
        .modal-box { background:#fff; border-radius:16px; padding:18px; max-width:420px; width:100%; box-shadow:0 20px 40px rgba(0,0,0,0.25); text-align:center; }
        .btn { cursor:pointer; border:none; border-radius:10px; padding:10px 14px; font-weight:700; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--accent)); color:#fff; }
        .btn-link { background:none; color:var(--primary); }
    </style>
</head>
<body>
    <div class="glass header">
        <a href="{{ route('dashboard.comprador') }}" class="back-btn"><i class="ri-arrow-left-line"></i> Volver a la tienda</a>
        <div class="brand">
            <span>💳</span>
            <span>PIL | Pasarela de pago</span>
        </div>
        <div class="user">
            <span class="pill"><i class="ri-user-3-line"></i> {{ Auth::user()->name ?? 'Usuario' }}</span>
        </div>
    </div>

    @if(!empty($paymentSuccess) && $paymentSuccess && $downloadUrl)
        <div class="modal" id="successModal">
            <div class="modal-box">
                <h3 style="margin:0 0 8px;">Pago realizado</h3>
                <p style="margin:0 0 10px;">Tu recibo se descargará automáticamente.</p>
                <div style="display:flex; justify-content:center; gap:10px; flex-wrap:wrap;">
                    <button class="btn btn-primary" id="downloadNow"><i class="ri-download-2-line"></i> Descargar ahora</button>
                    <button class="btn btn-link" id="closeModal">Cerrar</button>
                </div>
            </div>
        </div>
    @endif

    <form id="paymentForm" method="POST" action="{{ route('dashboard.payment.process') }}" style="display:none;">
        @csrf
        <input type="hidden" name="payment_method" id="paymentMethodInput" value="qr">
        <input type="hidden" name="cart" value='@json($items)'>
    </form>

    <div class="container">
        <div class="glass">
            <div class="section">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px; gap:12px;">
                    <h3 style="color:#fff; margin:0;">Selecciona el método de pago</h3>
                    <span class="badge"><i class="ri-shield-check-line"></i> Seguro y cifrado</span>
                </div>
                <div class="grid" style="padding:0;">
                    <label class="pay-option" data-method="qr">
                        <input type="radio" name="method" value="qr" checked>
                        <div>
                            <strong style="color:#0f172a;">QR / Transferencia</strong>
                            <p class="note" style="color:#1f2937;">Escanea el QR y sube el comprobante.</p>
                        </div>
                    </label>
                    <label class="pay-option" data-method="efectivo">
                        <input type="radio" name="method" value="efectivo">
                        <div>
                            <strong style="color:#0f172a;">Efectivo</strong>
                            <p class="note" style="color:#1f2937;">Pago al momento de la entrega.</p>
                        </div>
                    </label>
                    <label class="pay-option" data-method="tarjeta">
                        <input type="radio" name="method" value="tarjeta">
                        <div>
                            <strong style="color:#0f172a;">Tarjeta</strong>
                            <p class="note" style="color:#1f2937;">Débito o crédito con validación.</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="section">
                <div id="panel-qr" class="card" style="background:#eef2f7;">
                    <h3>Pago con QR</h3>
                    <div class="qr-box">
                        <img src="{{ $qrImage }}" alt="QR de pago">
                        <div class="note" style="text-align:center;">
                            Escanea el código con tu app bancaria.<br>
                            Monto a pagar: <strong>Bs {{ number_format($total, 2) }}</strong>
                        </div>
                        <button class="btn-primary" style="width:auto; padding:10px 16px;">Adjuntar comprobante</button>
                    </div>
                </div>

                <div id="panel-efectivo" class="card" style="display:none;">
                    <h3>Pago en efectivo</h3>
                    <p class="note">Entrega el efectivo al repartidor. Se validará el monto contra la factura.</p>
                    <div class="badge"><i class="ri-information-line"></i> Lleva el monto exacto para agilizar la entrega.</div>
                </div>

                <div id="panel-tarjeta" class="card" style="display:none;">
                    <h3>Pago con tarjeta</h3>
                    <div class="form-row">
                        <input class="input" type="text" placeholder="Nombre en la tarjeta">
                        <input class="input" type="text" placeholder="Número de tarjeta" inputmode="numeric" maxlength="19">
                    </div>
                    <div class="form-row">
                        <input class="input" type="text" placeholder="MM/AA" maxlength="5">
                        <input class="input" type="password" placeholder="CVV" maxlength="4">
                    </div>
                    <p class="note" style="margin-top:8px;">Tus datos se procesan de forma segura.</p>
                </div>
            </div>

            <div class="section">
                <div style="display:grid; gap:12px; grid-template-columns: repeat(auto-fit, minmax(220px,1fr));">
                    <div class="card">
                        <strong>Productos nuevos</strong>
                        <p class="note" style="margin:6px 0 0;">Explora las últimas incorporaciones.</p>
                    </div>
                    <div class="card">
                        <strong>Recomendaciones inteligentes</strong>
                        <p class="note" style="margin:6px 0 0;">Basadas en tu carrito y compras recientes.</p>
                        @if(!empty($recommended))
                            <ul style="margin:6px 0 0 12px; color:#0f172a;">
                                @foreach($recommended as $rec)
                                    <li>{{ $rec }}</li>
                                @endforeach
                            </ul>
                        @endif
                        @if($recsFromCart->count())
                            <p class="note" style="margin:8px 0 0;">Por tu carrito:</p>
                            <ul style="margin:4px 0 0 12px; color:#0f172a;">
                                @foreach($recsFromCart as $rec)
                                    <li>{{ $rec }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="glass">
            <div class="summary">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h3 style="color:#fff; margin:0;">Resumen</h3>
                    <span class="pill" style="background:rgba(255,255,255,0.15); border-color:rgba(255,255,255,0.3); color:#fff;">{{ count($items) }} items</span>
                </div>
                <div class="card" style="max-height:320px; overflow-y:auto;">
                    @foreach($items as $item)
                        <div class="item">
                            <div>
                                <strong>{{ $item['name'] ?? 'Producto' }}</strong>
                                <p class="note" style="margin:0;">Cantidad: {{ $item['qty'] ?? 0 }}</p>
                            </div>
                            <span>Bs {{ number_format(($item['qty'] ?? 0) * ($item['price'] ?? 0), 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="item"><span>Subtotal</span><span>Bs {{ number_format($subtotal, 2) }}</span></div>
                <div class="item"><span>Envio</span><span>Bs {{ number_format($shipping, 2) }}</span></div>
                <div class="total"><span>Total</span><span>Bs {{ number_format($total, 2) }}</span></div>
                <button class="btn-primary" type="button" id="confirmPayment">Confirmar pago</button>
                <p class="note" style="text-align:center;">Al confirmar aceptas los términos y condiciones.</p>
            </div>
        </div>
    </div>

    <script>
        const options = document.querySelectorAll('.pay-option');
        const panels = {
            qr: document.getElementById('panel-qr'),
            efectivo: document.getElementById('panel-efectivo'),
            tarjeta: document.getElementById('panel-tarjeta'),
        };
        options.forEach(opt => {
            opt.addEventListener('click', () => {
                const value = opt.dataset.method;
                options.forEach(o => o.classList.remove('active'));
                opt.classList.add('active');
                Object.entries(panels).forEach(([k, el]) => {
                    el.style.display = k === value ? '' : 'none';
                });
                opt.querySelector('input').checked = true;
                document.getElementById('paymentMethodInput').value = value;
            });
        });

        document.getElementById('confirmPayment').addEventListener('click', () => {
            document.getElementById('paymentForm').submit();
        });

        @if(!empty($paymentSuccess) && $paymentSuccess && $downloadUrl)
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('successModal');
                const closeBtn = document.getElementById('closeModal');
                const downloadBtn = document.getElementById('downloadNow');
                const url = "{{ $downloadUrl }}";
                const auto = setTimeout(() => window.location = url, 800);
                closeBtn?.addEventListener('click', () => modal.style.display = 'none');
                downloadBtn?.addEventListener('click', () => { clearTimeout(auto); window.location = url; });
            });
        @endif
    </script>
</body>
</html>
