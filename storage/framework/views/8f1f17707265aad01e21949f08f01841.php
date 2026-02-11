

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0 d-flex flex-column" style="height: calc(100vh - 20px); overflow: hidden;">
    
    <div class="d-flex justify-content-between align-items-center bg-white border-bottom px-4 py-2 mb-3 shadow-sm flex-shrink-0">
        <div class="d-flex align-items-center">
            <a href="<?php echo e(route('pos.index')); ?>" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <div>
                <h5 class="fw-bold mb-0 text-primary">Mesa: <?php echo e($table->name); ?></h5>
                <small class="text-muted">Zona: <?php echo e($table->area->name); ?></small>
            </div>
        </div>
            <!-- Mobile: categorías en scroll horizontal (aparece debajo en pantallas pequeñas) -->
            <div class="d-md-none mt-2 px-2 w-100">
                <div class="d-flex overflow-auto gap-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="filterProducts('all')">Todo</button>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button class="btn btn-sm btn-outline-secondary" onclick="filterProducts('cat-<?php echo e($category->id); ?>')"><?php echo e($category->name); ?></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        
        <div class="d-flex align-items-center gap-2">
            <?php if($order): ?>
                <button type="button" class="btn btn-outline-primary btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#moveTableModal">
                    <i class="bi bi-arrow-left-right me-1"></i> Mover Mesa
                </button>
            <?php endif; ?>
            <div class="vr mx-2"></div>
            <span class="badge bg-light text-dark border p-2">
                <i class="bi bi-person-fill me-1"></i> <?php echo e(auth()->user()->name); ?>

            </span>
            <!-- Mobile: botón para abrir la cuenta (visible sólo en sm) -->
            <button class="btn btn-outline-primary d-md-none ms-2" type="button" onclick="toggleCartMobile()" aria-label="Ver Cuenta">
                <i class="bi bi-cart-fill"></i>
            </button>
        </div>
    </div>

    <div class="row g-0 flex-grow-1 overflow-hidden">
        
        <div class="col-md-2 bg-light border-end overflow-auto h-100 pb-5">
            <div class="list-group list-group-flush">
                <button onclick="filterProducts('all')" class="list-group-item list-group-item-action active text-center py-3 category-btn" id="cat-btn-all">
                    <i class="bi bi-grid-fill d-block fs-4 mb-1"></i> Todo
                </button>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button onclick="filterProducts('cat-<?php echo e($category->id); ?>')" class="list-group-item list-group-item-action text-center py-3 category-btn" id="cat-btn-<?php echo e($category->id); ?>">
                        <?php if($category->image): ?>
                            <img src="<?php echo e(asset('storage/'.$category->image)); ?>" class="rounded mb-1" width="40" height="40" style="object-fit: cover;">
                        <?php else: ?>
                            <i class="bi bi-tag d-block fs-4 mb-1"></i>
                        <?php endif; ?>
                        <span class="d-block small fw-bold lh-sm"><?php echo e($category->name); ?></span>
                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="col-md-7 bg-white overflow-auto h-100 px-3 pb-5" id="products-container">
            <div class="sticky-top bg-white pt-3 pb-2 mb-2" style="z-index: 10;">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-search"></i></span>
                    <input type="text" id="productSearchInput" class="form-control border-start-0" placeholder="Buscar producto..." autocomplete="off">
                </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 pb-5">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $category->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col product-item cat-<?php echo e($category->id); ?>" data-name="<?php echo e(strtolower($product->name)); ?>">
                            <div class="card h-100 border-0 shadow-sm product-card" onclick="addToOrder(<?php echo e($product->id); ?>)" style="cursor: pointer; transition: transform 0.1s; touch-action: manipulation;">
                                <div class="position-relative">
                                        <?php if($product->image): ?>
                                        <img src="<?php echo e(asset('storage/'.$product->image)); ?>" class="card-img-top" style="height: 140px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex justify-content-center align-items-center" style="height: 120px;">
                                            <i class="bi bi-cup-straw fs-1 text-muted opacity-25"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-dark opacity-75"><?php echo e($currency ?? 'S/'); ?><?php echo e(number_format($product->price, 0)); ?></span>
                                    </div>

                                    <?php if(!is_null($product->stock)): ?>
                                        <div class="position-absolute bottom-0 start-0 m-2">
                                            <span class="badge <?php echo e($product->stock <= 5 ? 'bg-danger' : 'bg-success'); ?> border border-white shadow-sm">
                                                Stock: <?php echo e($product->stock); ?>

                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body p-3 text-center">
                                    <h6 class="card-title fs-5 mb-0 text-truncate"><?php echo e($product->name); ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="col-md-3 bg-white border-start h-100 d-flex flex-column">
            <div class="p-3 bg-light border-bottom flex-shrink-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-cart"></i> Cuenta Actual</h6>
            </div>
            <div id="cart-container" class="flex-grow-1 d-flex flex-column overflow-hidden">
                <?php echo $__env->make('pos.partials.cart', ['order' => $order], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="noteModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2 bg-warning">
                <h6 class="modal-title fw-bold text-dark">Nota Cocina</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="noteDetailId">
                <textarea id="noteText" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer p-1">
                <button type="button" class="btn btn-warning w-100 btn-sm text-dark fw-bold" onclick="saveNote()">Guardar Nota</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="moveTableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2 bg-info text-white">
                <h6 class="modal-title fw-bold">Mover Mesa</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <?php if($order): ?>
                <form action="<?php echo e(route('pos.move', $order->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <label class="form-label small text-muted">Destino:</label>
                        <select name="target_table_id" class="form-select" required>
                            <option value="" selected disabled>-- Elegir Mesa --</option>
                            <?php $__currentLoopData = $freeTables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ft): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ft->id); ?>"><?php echo e($ft->name); ?> (<?php echo e($ft->area->name); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="modal-footer p-1">
                        <button type="submit" class="btn btn-info w-100 btn-sm text-white fw-bold">Confirmar</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="optionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-2">
                <h6 class="modal-title fw-bold">Ajustes</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Descuento Global</label>
                    <input type="number" step="0.01" id="inputDiscount" class="form-control" value="<?php echo e($order ? $order->discount : 0); ?>" onclick="this.select()">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Propina</label>
                    <input type="number" step="0.01" id="inputTip" class="form-control" value="<?php echo e($order ? $order->tip : 0); ?>" onclick="this.select()">
                </div>
            </div>
            <div class="modal-footer p-1">
                <button type="button" class="btn btn-primary w-100 btn-sm fw-bold" onclick="applyOptions()">Aplicar Cambios</button>
            </div>
        </div>
    </div>
</div>

<?php if($order): ?>
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?php echo e(route('pos.checkout', $order->id)); ?>" method="POST" class="modal-content border-0 shadow-lg">
            <?php echo csrf_field(); ?>
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title fw-bold">Cobrar Venta</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">CLIENTE</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" list="clientsList" id="clientSearchInput" placeholder="Buscar..." oninput="searchClient(this)" autocomplete="off">
                        <button class="btn btn-light border" type="button" onclick="document.getElementById('clientSearchInput').value=''; searchClient({value:''})"><i class="bi bi-x"></i></button>
                    </div>
                    <datalist id="clientsList">
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($client->name); ?>" data-id="<?php echo e($client->id); ?>" data-document="<?php echo e($client->document_number); ?>"></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </datalist>
                    <input type="hidden" name="client_id" id="clientId">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-8"><input type="text" name="client_document" id="clientDoc" class="form-control bg-light" placeholder="RUC/DNI" readonly></div>
                    <div class="col-4"><select name="document_type" class="form-select fw-bold"><option value="Ticket">Ticket</option><option value="Boleta">Boleta</option><option value="Factura">Factura</option></select></div>
                </div>
                <div class="mb-3 text-center">
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="payment_method" id="payCash" value="cash" checked onclick="toggleCashInput(true)">
                        <label class="btn btn-outline-success fw-bold" for="payCash">Efectivo</label>

                        <input type="radio" class="btn-check" name="payment_method" id="payYape" value="yape" onclick="toggleCashInput(false)">
                        <label class="btn btn-outline-warning fw-bold" for="payYape">Yape</label>

                        <input type="radio" class="btn-check" name="payment_method" id="payCard" value="card" onclick="toggleCashInput(false)">
                        <label class="btn btn-outline-primary fw-bold" for="payCard">Tarjeta</label>
                    </div>
                </div>
                <div id="cashInputGroup">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Recibido</label>
                           <input type="number" step="0.01" name="received_amount" id="receivedAmount" class="form-control text-center fw-bold fs-4 text-success" 
                               value="<?php echo e(number_format($order->total, 2, '.', '')); ?>" 
                               oninput="calculateChange()" onclick="this.select()">
                    </div>
                    <div class="d-flex justify-content-between">
                        <small>Cambio:</small>
                        <h4 class="fw-bold mb-0 text-secondary" id="changeAmount">0.00</h4>
                    </div>
                </div>
                <input type="hidden" id="hiddenTotal" value="<?php echo e(number_format($order->total, 2, '.', '')); ?>">
            </div>
            <div class="modal-footer p-2 bg-light">
                <button type="submit" class="btn btn-success w-100 btn-lg fw-bold">CONFIRMAR PAGO</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
    const tableId = <?php echo e($table->id); ?>;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Buscador de productos (cliente)
    const productSearchInput = document.getElementById('productSearchInput');
    if (productSearchInput) {
        productSearchInput.addEventListener('input', debounce(function (e) {
            const q = (e.target.value || '').trim().toLowerCase();
            document.querySelectorAll('.product-item').forEach(function (el) {
                const name = el.getAttribute('data-name') || '';
                el.style.display = q === '' || name.indexOf(q) !== -1 ? 'block' : 'none';
            });
        }, 200));
    }

    window.addToOrder = function(productId) {
        fetch(`<?php echo e(url('/pos/order')); ?>/${tableId}/add`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ product_id: productId })
        }).then(r => r.text()).then(html => {
            document.getElementById('cart-container').innerHTML = html;
            updateCheckoutTotal();
        });
    };

    window.updateQty = function(id, qty) {
        if(qty < 1 && !confirm('¿Eliminar producto?')) return;
        fetch(`<?php echo e(url('/pos/detail')); ?>/${id}/update`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ quantity: qty })
        }).then(r => r.text()).then(html => {
            document.getElementById('cart-container').innerHTML = html;
            updateCheckoutTotal();
        });
    };

    window.removeItem = function(id) {
        fetch(`<?php echo e(url('/pos/detail')); ?>/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        }).then(r => r.text()).then(html => {
            document.getElementById('cart-container').innerHTML = html;
            updateCheckoutTotal();
        });
    };

    window.applyOptions = function() {
        var discount = document.getElementById('inputDiscount').value;
        var tip = document.getElementById('inputTip').value;
        var modal = bootstrap.Modal.getInstance(document.getElementById('optionsModal'));
        modal.hide();

        fetch(`<?php echo e(url('/pos/order')); ?>/<?php echo e($order ? $order->id : 0); ?>/discount`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ discount: discount, tip: tip })
        }).then(r => r.text()).then(html => {
            document.getElementById('cart-container').innerHTML = html;
            updateCheckoutTotal();
        });
    };

    // Utils
    window.filterProducts = function(cat) {
        document.querySelectorAll('.category-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(cat === 'all' ? 'cat-btn-all' : 'cat-btn-' + cat.replace('cat-', '')).classList.add('active');
        document.querySelectorAll('.product-item').forEach(item => {
            item.style.display = (cat === 'all' || item.classList.contains(cat)) ? 'block' : 'none';
        });
    };

    window.updateCheckoutTotal = function() {
        setTimeout(() => {
            var newTotal = document.getElementById('cartTotalValue') ? document.getElementById('cartTotalValue').value : 0;
            var hiddenInput = document.getElementById('hiddenTotal');
            var receivedInput = document.getElementById('receivedAmount');
            if(hiddenInput) hiddenInput.value = newTotal;
            if(receivedInput) receivedInput.value = newTotal;
            // actualizar barra inferior móvil
            var itemCount = document.getElementById('cartItemCount') ? document.getElementById('cartItemCount').value : 0;
            var bottomTotal = document.getElementById('bottomTotal');
            var bottomCount = document.getElementById('bottomCount');
            if(bottomTotal) bottomTotal.innerText = parseFloat(newTotal).toFixed(2);
            if(bottomCount) bottomCount.innerText = itemCount;
        }, 500);
    };

    // Simple debounce util
    function debounce(fn, wait) {
        let t;
        return function() {
            const args = arguments;
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), wait);
        };
    }

    // Modal Notas
    var noteModalEl = document.getElementById('noteModal');
    if(noteModalEl){
        noteModalEl.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('noteDetailId').value = button.getAttribute('data-detail-id');
            document.getElementById('noteText').value = button.getAttribute('data-note-content') || '';
            setTimeout(() => document.getElementById('noteText').focus(), 500);
        });
    }
    window.saveNote = function() {
        var detailId = document.getElementById('noteDetailId').value;
        var note = document.getElementById('noteText').value;
        var modal = bootstrap.Modal.getInstance(document.getElementById('noteModal'));
        modal.hide();
        fetch(`<?php echo e(url('/pos/detail')); ?>/${detailId}/note`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ note: note })
        }).then(r => r.text()).then(html => document.getElementById('cart-container').innerHTML = html);
    };

    // Cobro
    window.toggleCashInput = function(show) { document.getElementById('cashInputGroup').style.display = show ? 'block' : 'none'; }
    window.calculateChange = function() {
        var total = parseFloat(document.getElementById('hiddenTotal').value) || 0;
        var received = parseFloat(document.getElementById('receivedAmount').value) || 0;
        var el = document.getElementById('changeAmount');
        if(el) el.innerText = (received - total).toFixed(2);
    }
    window.searchClient = function(input) {
        var list = document.getElementById('clientsList');
        if(!list || input.value === '') { document.getElementById('clientId').value=''; document.getElementById('clientDoc').value=''; return; }
        for(var i=0; i<list.options.length; i++) {
            if(list.options[i].value === input.value) {
                document.getElementById('clientId').value = list.options[i].getAttribute('data-id');
                document.getElementById('clientDoc').value = list.options[i].getAttribute('data-document');
                break;
            }
        }
    }

    // Mobile cart toggle
    window.toggleCartMobile = function(show) {
        var panel = document.getElementById('mobileCartPanel');
        if(!panel) return;
        var isOpen = panel.classList.contains('open');
        var open = (typeof show === 'boolean') ? show : !isOpen;
        if(open) {
            panel.classList.add('open'); document.body.classList.add('mobile-cart-open');
        } else {
            panel.classList.remove('open'); document.body.classList.remove('mobile-cart-open');
        }
    }
</script>

<!-- Mobile: fixed bottom bar with quick access to cart (Offcanvas) -->
<div class="d-md-none fixed-bottom bg-white border-top p-2">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <div class="small text-muted">Total</div>
            <div class="fw-bold"><?php echo e($currency ?? 'S/'); ?><span id="bottomTotal"><?php echo e(number_format($order->total ?? 0, 2)); ?></span></div>
        </div>
        <div>
            <button class="btn btn-primary btn-lg" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCart" aria-controls="offcanvasCart">
                Ver Cuenta (<span id="bottomCount"><?php echo e($order ? $order->details->sum('quantity') : 0); ?></span>)
            </button>
        </div>
    </div>
</div>

<!-- Offcanvas cart (mobile) -->
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasCart" aria-labelledby="offcanvasCartLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasCartLabel">Cuenta</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body small">
    <?php echo $__env->make('pos.partials.cart', ['order' => $order], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  </div>
</div>

<style>
    .product-card:active { transform: scale(0.95); background-color: #f8f9fa; }
    /* Mobile cart panel styles and overall mobile adjustments */
    @media (max-width: 767.98px) {
        /* Hide desktop left sidebar and desktop cart (we use mobile panel) */
        .col-md-2 { display: none !important; }
        #cart-container { display: none; }
        body.mobile-cart-open { overflow: hidden; }
        #mobileCartPanel { position: fixed; left: 0; right: 0; bottom: 0; height: 75%; background: #fff; z-index: 1060; box-shadow: 0 -6px 18px rgba(0,0,0,0.12); transform: translateY(100%); transition: transform .25s ease-in-out; overflow: auto; }
        #mobileCartPanel.open { transform: translateY(0); }
        .mobile-cart-header { padding: 12px; border-bottom: 1px solid #eee; display:flex; align-items:center; justify-content:space-between; }
        .product-card .card-img-top { height: 160px; }
        .product-card { border-radius: .6rem; }
        .card-body { padding: .75rem !important; }
        .card-title { font-size: 1rem !important; }
        .btn { touch-action: manipulation; }
        .list-group { display: none; }
        .category-btn { padding: .5rem 0.25rem !important; }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\restaurante\resources\views/pos/order.blade.php ENDPATH**/ ?>