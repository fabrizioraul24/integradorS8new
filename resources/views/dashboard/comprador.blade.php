@php
    use Illuminate\Support\Facades\Auth;

    $primary = '#4e6baf';
    $accent = '#86acd4';
    $user = Auth::user();
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprador | PIL Andina</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css">
    <style>
        :root { --primary: {{ $primary }}; --accent: {{ $accent }}; }
        * { box-sizing: border-box; margin:0; padding:0; }
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: linear-gradient(135deg, #3a5899 0%, #4e6baf 30%, #6b87c7 60%, #86acd4 100%);
            color: #0f172a;
            min-height: 100vh;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 24px; }
        .navbar {
            display:flex; justify-content:space-between; align-items:center;
            padding: 18px 24px; border-radius: 18px;
            background: rgba(255,255,255,0.14);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255,255,255,0.25);
            margin-bottom: 24px;
        }
        .brand { display:flex; align-items:center; gap:12px; color:#fff; font-weight:900; letter-spacing:0.03em; }
        .nav-menu { display:flex; gap:10px; flex-wrap:wrap; }
        .nav-btn {
            padding: 10px 16px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.25);
            background: rgba(255,255,255,0.1);
            color:#fff; cursor:pointer; font-weight:600;
        }
        .nav-btn.active { background: rgba(255,255,255,0.22); }
        .cart-btn {
            display:flex; align-items:center; gap:8px;
            padding: 12px 18px;
            border-radius: 14px;
            border:none;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color:#fff; font-weight:800; cursor:pointer;
            position: relative;
        }
        .cart-badge {
            position:absolute; top:-6px; right:-6px;
            background:#f97316; color:#fff; font-weight:800;
            border-radius:999px; padding:3px 7px; font-size:0.78rem;
            min-width:22px; text-align:center; display:none;
        }
        .user-chip {
            display:flex; align-items:center; gap:10px;
            padding:10px 14px;
            border-radius:14px;
            background: rgba(255,255,255,0.14);
            border:1px solid rgba(255,255,255,0.28);
            color:#fff;
        }
        .user-chip small { display:block; color:rgba(255,255,255,0.8); font-size:0.78rem; }
        .hero {
            background: rgba(255,255,255,0.12);
            border:1px solid rgba(255,255,255,0.25);
            border-radius:18px;
            padding: 24px;
            backdrop-filter: blur(14px);
            display:flex; justify-content:space-between; align-items:center;
            margin-bottom: 16px;
            color:#fff;
        }
        .chip {
            display:inline-flex; align-items:center; gap:6px;
            padding:6px 12px; border-radius:999px;
            background: rgba(255,255,255,0.15); color:#fff; font-weight:600;
            border:1px solid rgba(255,255,255,0.25);
        }
        .categories {
            display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px;
        }
        .grid {
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 18px;
        }
        .card {
            background: rgba(255,255,255,0.14);
            border:1px solid rgba(255,255,255,0.22);
            border-radius:16px;
            padding:16px;
            backdrop-filter: blur(10px);
            color:#fff;
        }
        .product-img {
            width:100%; height:220px; object-fit:contain;
            border-radius:12px; margin-bottom:12px;
            border:1px solid rgba(255,255,255,0.2);
            background:#fff;
        }
        .title-row { display:flex; justify-content:space-between; align-items:center; gap:8px; }
        .title-row h3 { margin:0; font-size:1.08rem; }
        .price { font-weight:900; font-size:1.2rem; }
        .muted { color:rgba(255,255,255,0.8); font-size:0.9rem; }
        .bottom { display:flex; justify-content:space-between; align-items:center; margin-top:8px; }
        .qty-row { display:flex; gap:8px; align-items:center; margin-top:12px; }
        .qty-row input { flex:0 0 90px; padding:10px; border-radius:10px; border:1px solid rgba(255,255,255,0.25); background:rgba(255,255,255,0.12); color:#fff; }
        .qty-row button { flex:1; text-align:center; padding:10px; border:none; border-radius:10px; background: linear-gradient(135deg, var(--primary), var(--accent)); color:#fff; font-weight:700; cursor:pointer; }
        .badge-agotado { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:10px; background:rgba(239,68,68,0.18); border:1px solid rgba(239,68,68,0.3); color:#fee2e2; font-weight:700; }
        .history-card ul { margin:8px 0 0 14px; color:#e2e8f0; }
        /* Carrito lateral */
        .drawer-backdrop {
            position: fixed; inset:0; background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);
            display:none; z-index:20;
        }
        .drawer {
            position: fixed; top:0; right:0; width:380px; max-width:100%;
            height:100vh; background: linear-gradient(160deg, #f8fbff, #eef2f7);
            box-shadow: -10px 0 40px rgba(0,0,0,0.25);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            z-index:21; display:flex; flex-direction:column;
        }
        .drawer.active { transform: translateX(0); }
        .drawer-header { padding:18px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(15,23,42,0.08); position:sticky; top:0; background:inherit; z-index:1; }
        .drawer-content { padding:16px; overflow-y:auto; flex:1; display:flex; flex-direction:column; gap:12px; }
        .drawer-item { display:flex; gap:12px; background: rgba(255,255,255,0.8); border-radius:14px; padding:12px; border:1px solid rgba(15,23,42,0.05); box-shadow:0 12px 24px rgba(15,23,42,0.08); position:relative; }
        .drawer-item img { width:68px; height:68px; object-fit:contain; border-radius:10px; background:#fff; border:1px solid rgba(15,23,42,0.06); }
        .drawer-footer { padding:16px; border-top:1px solid rgba(15,23,42,0.08); background:inherit; position:sticky; bottom:0; }
        .drawer-footer .total { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; font-weight:800; }
        .btn-outline { width:100%; padding:12px; border-radius:12px; border:1px solid var(--primary); color:var(--primary); background:#fff; font-weight:700; cursor:pointer; margin-top:8px; }
        .trash-btn {
            background: none; border:none; color:#dc2626; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            padding:6px; border-radius:10px;
        }
        .trash-btn:hover { background:rgba(220,38,38,0.1); }
        /* Modal aviso stock */
        .modal { position:fixed; inset:0; display:none; align-items:center; justify-content:center; background:rgba(0,0,0,0.55); z-index:30; padding:1rem; }
        .modal-content { background:#fff; border-radius:16px; padding:18px; max-width:420px; width:100%; box-shadow:0 20px 40px rgba(0,0,0,0.25); }
        .modal-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:14px; }
        .modal-large { background: rgba(255,255,255,0.9); border-radius:18px; padding:18px; max-width:900px; width:100%; box-shadow:0 24px 48px rgba(0,0,0,0.28); color:#0f172a; max-height:80vh; overflow-y:auto; }
        .history-item { display:flex; gap:12px; padding:12px; border:1px solid rgba(15,23,42,0.08); border-radius:14px; background:#f8fafc; margin-bottom:10px; }
        .history-item img { width:72px; height:72px; object-fit:contain; border-radius:12px; border:1px solid rgba(15,23,42,0.08); background:#fff; }
        .history-meta { font-size:0.9rem; color:#475569; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <div class="brand">PIL COMPRADOR</div>
                <div class="nav-menu">
                    <button class="nav-btn">Descuentos</button>
                    <button class="nav-btn">Promos imperdibles</button>
                    <button class="nav-btn">Productos nuevos</button>
                    <button class="nav-btn" id="historyBtn">Historial de compras</button>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <div class="user-chip">
                    <i class="ri-user-3-line"></i>
                    <div>
                        <strong>{{ $user->name ?? 'Usuario Pil' }}</strong>
                        <small>{{ optional($user->role)->name ?? 'Rol no asignado' }}</small>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="nav-btn">Cerrar sesion</button>
                </form>
                <button class="cart-btn" id="openCart"><i class="ri-shopping-cart-2-fill"></i> Carrito <span class="cart-badge" id="cartBadge">0</span></button>
            </div>
        </nav>

        <header class="hero">
            <div>
                <h2 style="margin:0;">Productos disponibles</h2>
                <p style="margin:4px 0 0;">Productos activos en bodega.</p>
            </div>
            <span class="chip"><i class="ri-box-3-line"></i> {{ $products->count() }} productos</span>
        </header>

        <div class="card" style="padding:12px; margin-bottom:16px;">
            <strong>Categorias</strong>
            <div class="categories">
                <button class="nav-btn filter-button active" data-filter="all">Todas</button>
                @foreach($categories as $cat)
                    <button class="nav-btn filter-button" data-filter="cat-{{ $cat['id'] }}">{{ $cat['name'] }} ({{ $cat['count'] }})</button>
                @endforeach
            </div>
        </div>
        <section class="grid">
            @foreach($products as $product)
                @php
                    $imgPath = $product->image_path;
                    $imgUrl = $imgPath ? Storage::url($imgPath) : asset('storage/images/logo.png');
                @endphp
                <article class="card addable"
                         data-id="{{ $product->id }}"
                         data-category="cat-{{ $product->category->id ?? 0 }}"
                         data-name="{{ $product->name }}"
                         data-price="{{ $product->price_for_buyer }}"
                         data-available="{{ $product->available_qty }}"
                         data-img="{{ $imgUrl }}">
                    <img class="product-img" src="{{ $imgUrl }}" alt="{{ $product->name }}">
                    <div class="title-row">
                        <h3>{{ $product->name }}</h3>
                        <span class="chip">{{ $product->category->name ?? 'Sin categoria' }}</span>
                    </div>
                    <p class="muted">{{ \Illuminate\Support\Str::limit($product->description ?? 'Sin descripcion', 80) }}</p>
                    @if($product->available_qty > 0)
                        <div class="bottom">
                            <span class="price">Bs {{ number_format($product->price_for_buyer, 2) }}</span>
                            <span class="chip" style="background:rgba(34,197,94,0.2); border-color:rgba(34,197,94,0.35);">
                                <i class="ri-check-line"></i> {{ $product->available_qty }} disp.
                            </span>
                        </div>
                        <div class="qty-row">
                            <input type="number" min="1" value="1" class="qty-input">
                            <button class="add-cart">Agregar al carrito</button>
                        </div>
                    @else
                        <div class="bottom" style="justify-content:flex-start;">
                            <span class="badge-agotado"><i class="ri-close-line"></i> Agotado</span>
                        </div>
                    @endif
                </article>
            @endforeach
        </section>
    </div>
    <div class="drawer-backdrop" id="cartBackdrop"></div>
    <aside class="drawer" id="cartDrawer">
        <div class="drawer-header">
            <strong>Tu carrito</strong>
            <button class="nav-btn" id="closeCart">Cerrar</button>
        </div>
        <div class="drawer-content" id="cartItems"></div>
        <div class="drawer-footer">
            <div class="total"><span>Total</span><span id="cartTotal">Bs 0.00</span></div>
            <button class="cart-btn" id="finalizePurchase" style="width:100%; justify-content:center; border-radius:12px;">Finalizar compra</button>
            <button class="btn-outline" id="clearCart">Vaciar carrito</button>
        </div>
    </aside>
    <div class="modal" id="stockModal">
        <div class="modal-content">
            <h3 style="margin:0 0 8px;">Stock insuficiente</h3>
            <p style="margin:0;" id="stockModalMsg">No hay unidades suficientes.</p>
            <div class="modal-actions">
                <button class="nav-btn" id="closeModal">Entendido</button>
            </div>
        </div>
    </div>
    @if(isset($history) && $history->count())
    <div class="modal" id="historyModal">
        <div class="modal-large">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <strong style="font-size:1.1rem;">Historial de compras</strong>
                <button class="nav-btn" id="closeHistory">Cerrar</button>
            </div>
            @foreach($history as $h)
                <div class="history-item">
                    <div style="flex:1;">
                        <div style="display:flex; justify-content:space-between; gap:8px; align-items:flex-start;">
                            <div>
                                <strong>#{{ $h->receipt_number }}</strong>
                                <p class="history-meta" style="margin:2px 0;">{{ optional($h->issued_at)->format('Y-m-d H:i') }}</p>
                                <p class="history-meta" style="margin:2px 0;">Pago: {{ ucfirst($h->payment_method) }} Â· Estado: {{ ucfirst($h->payment_status) }}</p>
                            </div>
                            <span class="chip" style="color:#0f172a;">Bs {{ number_format($h->total, 2) }}</span>
                        </div>
                        @foreach($h->items as $item)
                            @php
                                $pi = $item->product;
                                $pImg = $pi && $pi->image_path ? Storage::url($pi->image_path) : asset('storage/images/logo.png');
                            @endphp
                            <div style="display:flex; gap:10px; align-items:center; margin-top:8px;">
                                <img src="{{ $pImg }}" alt="{{ $item->product_name }}" style="width:56px; height:56px; object-fit:contain; border-radius:10px; border:1px solid rgba(15,23,42,0.08); background:#fff;">
                                <div style="flex:1;">
                                    <strong>{{ $item->product_name }}</strong>
                                    <p class="history-meta" style="margin:2px 0;">Cant: {{ $item->quantity }} Â· Unit: Bs {{ number_format($item->unit_price, 2) }}</p>
                                    <p class="history-meta" style="margin:0;">Total: Bs {{ number_format($item->quantity * $item->unit_price, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    <script>
        const filterButtons = document.querySelectorAll('.filter-button');
        const productCards = document.querySelectorAll('[data-category]');

        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.dataset.filter;
                filterButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                productCards.forEach(card => {
                    card.style.display = (filter === 'all' || card.dataset.category === filter) ? '' : 'none';
                });
            });
        });

        const cart = new Map();
        const cartBadge = document.getElementById('cartBadge');
        const cartItems = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');
        const cartDrawer = document.getElementById('cartDrawer');
        const cartBackdrop = document.getElementById('cartBackdrop');
        const stockModal = document.getElementById('stockModal');
        const stockModalMsg = document.getElementById('stockModalMsg');

        function openCart() {
            cartDrawer.classList.add('active');
            cartBackdrop.style.display = 'block';
        }
        function closeCart() {
            cartDrawer.classList.remove('active');
            cartBackdrop.style.display = 'none';
        }
        document.getElementById('openCart').addEventListener('click', openCart);
        document.getElementById('closeCart').addEventListener('click', closeCart);
        cartBackdrop.addEventListener('click', closeCart);

        function updateBadge() {
            let totalQty = 0;
            cart.forEach(item => totalQty += item.qty);
            cartBadge.textContent = totalQty;
            cartBadge.style.display = totalQty > 0 ? 'inline-flex' : 'none';
        }

        function renderCart() {
        cartItems.innerHTML = '';
        let total = 0;
        cart.forEach(item => {
            total += item.qty * item.price;
            const row = document.createElement('div');
            row.className = 'drawer-item';
            row.innerHTML = `
                    <img src="${item.img}" alt="${item.name}">
                    <div style="flex:1;">
                        <strong>${item.name}</strong>
                        <p style="margin:2px 0; color:#475569;">Bs ${item.price.toFixed(2)}</p>
                        <div style="display:flex; align-items:center; gap:8px; margin-top:6px;">
                            <input type="number" min="1" max="${item.available}" value="${item.qty}" style="width:70px; padding:6px; border-radius:10px; border:1px solid #cbd5e1;">
                            <span style="color:#0f172a;">/ ${item.available} disp.</span>
                        </div>
                    </div>
                    <button class="trash-btn" title="Quitar" data-remove="${item.id}"><i class="ri-delete-bin-6-line"></i></button>
                `;
                const qtyInput = row.querySelector('input');
                qtyInput.addEventListener('change', (e) => {
                    let val = parseInt(e.target.value, 10) || 1;
                    if (val > item.available) {
                        showStockModal(item.available);
                        val = item.available;
                    }
                    cart.set(item.id, {...item, qty: val});
                    updateBadge(); renderCart();
                });
                row.querySelector('[data-remove]').addEventListener('click', () => {
                    cart.delete(item.id);
                    updateBadge(); renderCart();
                });
                cartItems.appendChild(row);
            });
            cartTotal.textContent = 'Bs ' + total.toFixed(2);
            if (cart.size === 0) {
                cartItems.innerHTML = '<p style="margin:0; color:#475569;">Tu carrito está vacío.</p>';
            }
        }

        function showStockModal(max) {
            stockModalMsg.textContent = `No hay unidades suficientes. MÃ¡ximo disponible: ${max}.`;
            stockModal.style.display = 'flex';
        }
        document.getElementById('closeModal').addEventListener('click', () => {
            stockModal.style.display = 'none';
        });

        document.querySelectorAll('.addable').forEach(card => {
            const addBtn = card.querySelector('.add-cart');
            const qtyInput = card.querySelector('.qty-input');
            if (!addBtn || !qtyInput) return;
            const available = parseInt(card.dataset.available, 10) || 0;
            if (available <= 0) {
                addBtn.disabled = true;
                qtyInput.disabled = true;
                return;
            }
            addBtn.addEventListener('click', () => {
                const availableNow = parseInt(card.dataset.available, 10) || 0;
                let qty = parseInt(qtyInput.value, 10) || 1;
                if (qty > availableNow) {
                    showStockModal(availableNow);
                    qtyInput.value = availableNow > 0 ? availableNow : 1;
                    return;
                }
                const id = card.dataset.id;
                const existing = cart.get(id) || {
                    id,
                    name: card.dataset.name,
                    price: parseFloat(card.dataset.price),
                    available: availableNow,
                    img: card.dataset.img,
                    qty: 0,
                };
                const newQty = Math.min(existing.qty + qty, availableNow);
                if (newQty < existing.qty + qty) {
                    showStockModal(availableNow);
                }
                cart.set(id, {...existing, qty: newQty, available: availableNow});
                updateBadge(); renderCart(); openCart();
            });
        });

        document.getElementById('clearCart').addEventListener('click', () => {
            cart.clear(); updateBadge(); renderCart();
        });

        // Checkout hacia pasarela de pago
        const checkoutForm = document.createElement('form');
        checkoutForm.method = 'POST';
        checkoutForm.action = "{{ route('dashboard.payment') }}";
        checkoutForm.style.display = 'none';
        checkoutForm.innerHTML = `
            @csrf
            <input type="hidden" name="cart" id="cartPayload">
        `;
        document.body.appendChild(checkoutForm);
        const cartPayload = document.getElementById('cartPayload');

        document.getElementById('finalizePurchase').addEventListener('click', () => {
            if (cart.size === 0) {
                alert('Tu carrito está vacío.');
                return;
            }
            const payload = Array.from(cart.values()).map(item => ({
                id: item.id,
                name: item.name,
                price: item.price,
                qty: item.qty,
            }));
            cartPayload.value = JSON.stringify(payload);
            checkoutForm.submit();
        });

        const historyModal = document.getElementById('historyModal');
        document.getElementById('historyBtn')?.addEventListener('click', () => {
            if (historyModal) historyModal.style.display = 'flex';
        });
        document.getElementById('closeHistory')?.addEventListener('click', () => {
            if (historyModal) historyModal.style.display = 'none';
        });
        updateBadge(); renderCart();
    </script>
</body>
</html>



