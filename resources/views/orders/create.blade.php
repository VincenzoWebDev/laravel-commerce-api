@extends('layouts.api-ui')

@section('title', 'Nuovo Ordine API')

@section('content')
    <h1>Nuovo ordine</h1>
    <p>Seleziona i prodotti e invia l'ordine a <code>POST /api/orders</code>.</p>

    <div class="actions" style="margin-bottom: 12px;">
        <button id="reload-products-btn" class="btn btn-secondary" type="button">Ricarica prodotti</button>
        <a href="{{ route('ui.orders.index') }}" class="btn btn-secondary" style="text-decoration:none;">Vai agli ordini</a>
    </div>

    <div id="message" class="alert"></div>
    <div id="products" class="grid products-grid"></div>

    <div class="actions" style="margin-top: 16px;">
        <button id="submit-order-btn" class="btn" type="button">Crea ordine</button>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const axios = window.axios;
            const token = localStorage.getItem('api_token');
            const productsEl = document.getElementById('products');
            const messageEl = document.getElementById('message');
            const reloadBtn = document.getElementById('reload-products-btn');
            const submitBtn = document.getElementById('submit-order-btn');

            function showMessage(text, type) {
                messageEl.textContent = text;
                messageEl.className = 'alert ' + type + ' show';
            }

            function clearMessage() {
                messageEl.className = 'alert';
                messageEl.textContent = '';
            }

            function ensureAuth() {
                if (!token) {
                    showMessage('Devi prima fare login.', 'error');
                    setTimeout(function() {
                        window.location.href = '{{ route('ui.login') }}';
                    }, 600);
                    return false;
                }

                if (!axios) {
                    showMessage('Axios non disponibile. Verifica che Vite stia caricando resources/js/app.js.', 'error');
                    return false;
                }

                return true;
            }

            function getImageUrl(product) {
                if (!product.image) {
                    return null;
                }

                return product.image.startsWith('http') ? product.image : '/storage/' + product.image;
            }

            function renderProducts(products) {
                productsEl.innerHTML = '';

                if (!products.length) {
                    productsEl.innerHTML = '<div class="empty">Nessun prodotto disponibile.</div>';
                    return;
                }

                products.forEach(function(product) {
                    const card = document.createElement('article');
                    card.className = 'card';

                    const imageUrl = getImageUrl(product);
                    if (imageUrl) {
                        const img = document.createElement('img');
                        img.src = imageUrl;
                        img.alt = product.name;
                        card.appendChild(img);
                    }

                    const title = document.createElement('h2');
                    title.textContent = product.name;
                    card.appendChild(title);

                    const price = document.createElement('p');
                    price.innerHTML = '<strong>Prezzo:</strong> €' + Number(product.price).toFixed(2);
                    card.appendChild(price);

                    const stock = document.createElement('p');
                    stock.innerHTML = '<strong>Stock:</strong> ' + product.stock;
                    card.appendChild(stock);

                    const qtyField = document.createElement('div');
                    qtyField.className = 'field';

                    const qtyLabel = document.createElement('label');
                    qtyLabel.setAttribute('for', 'qty-' + product.id);
                    qtyLabel.textContent = 'Quantità da ordinare';
                    qtyField.appendChild(qtyLabel);

                    const qtyInput = document.createElement('input');
                    qtyInput.type = 'number';
                    qtyInput.id = 'qty-' + product.id;
                    qtyInput.min = '0';
                    qtyInput.max = String(product.stock);
                    qtyInput.step = '1';
                    qtyInput.value = '0';
                    qtyInput.dataset.productId = String(product.id);
                    qtyInput.dataset.productStock = String(product.stock);
                    qtyField.appendChild(qtyInput);

                    card.appendChild(qtyField);
                    productsEl.appendChild(card);
                });
            }

            async function loadProducts() {
                clearMessage();

                try {
                    const response = await axios.get('/api/products', {
                        headers: {
                            Authorization: 'Bearer ' + token,
                        },
                    });

                    renderProducts(response.data);
                } catch (error) {
                    if (error.response && error.response.status === 401) {
                        localStorage.removeItem('api_token');
                        localStorage.removeItem('api_user');
                        showMessage('Token non valido o scaduto. Effettua di nuovo il login.', 'error');
                        setTimeout(function() {
                            window.location.href = '{{ route('ui.login') }}';
                        }, 600);
                        return;
                    }

                    const fallback = (error.response && error.response.data && error.response.data.message) ?
                        error.response.data.message :
                        'Errore nel caricamento prodotti.';
                    showMessage(fallback, 'error');
                }
            }

            function buildPayload() {
                const quantityInputs = Array.from(productsEl.querySelectorAll('input[data-product-id]'));
                const items = [];

                quantityInputs.forEach(function(input) {
                    const quantity = Number(input.value);
                    const stock = Number(input.dataset.productStock || 0);

                    if (!Number.isInteger(quantity) || quantity < 0) {
                        throw new Error('La quantità deve essere un numero intero >= 0.');
                    }

                    if (quantity > stock) {
                        throw new Error('Una quantità supera lo stock disponibile.');
                    }

                    if (quantity > 0) {
                        items.push({
                            id: Number(input.dataset.productId),
                            quantity: quantity,
                        });
                    }
                });

                if (!items.length) {
                    throw new Error('Seleziona almeno un prodotto con quantità maggiore di 0.');
                }

                return {
                    products: items,
                };
            }

            async function submitOrder() {
                clearMessage();

                let payload;
                try {
                    payload = buildPayload();
                } catch (error) {
                    showMessage(error.message || 'Payload ordine non valido.', 'error');
                    return;
                }

                try {
                    const response = await axios.post('/api/orders', payload, {
                        headers: {
                            Authorization: 'Bearer ' + token,
                        },
                    });

                    showMessage('Ordine creato con successo.', 'success');
                    const orderIdentifier = response.data && response.data.data && response.data.data.uuid ?
                        response.data.data.uuid :
                        (response.data && response.data.data ? response.data.data.id : null);

                    if (orderIdentifier) {
                        setTimeout(function() {
                            window.location.href = '/orders/' + orderIdentifier;
                        }, 500);
                    }
                } catch (error) {
                    if (error.response && error.response.status === 401) {
                        localStorage.removeItem('api_token');
                        localStorage.removeItem('api_user');
                        showMessage('Token non valido o scaduto. Effettua di nuovo il login.', 'error');
                        setTimeout(function() {
                            window.location.href = '{{ route('ui.login') }}';
                        }, 600);
                        return;
                    }

                    const details = error.response && error.response.data && error.response.data.errors ?
                        Object.values(error.response.data.errors).flat().join(' ') :
                        '';
                    const fallback = (error.response && error.response.data && error.response.data.message) ?
                        error.response.data.message :
                        'Errore nella creazione ordine.';
                    showMessage(details || fallback, 'error');
                }
            }

            if (!ensureAuth()) {
                return;
            }

            loadProducts();
            reloadBtn.addEventListener('click', loadProducts);
            submitBtn.addEventListener('click', submitOrder);
        });
    </script>
@endpush
