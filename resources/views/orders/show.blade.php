@extends('layouts.api-ui')

@section('title', 'Dettaglio Ordine API')

@section('content')
    <h1>Dettaglio ordine</h1>
    <p>Pagina collegata a <code>GET /api/orders/{id}</code> (supporta anche uuid).</p>

    <div class="actions" style="margin-bottom: 12px;">
        <a href="{{ route('ui.orders.index') }}" class="btn btn-secondary" style="text-decoration:none;">Torna agli ordini</a>
    </div>

    <div id="message" class="alert"></div>
    <div id="order-summary" class="card" style="display:none; margin-bottom: 12px;"></div>
    <div id="order-items" class="grid products-grid"></div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const axios = window.axios;
            const token = localStorage.getItem('api_token');
            const messageEl = document.getElementById('message');
            const summaryEl = document.getElementById('order-summary');
            const itemsEl = document.getElementById('order-items');
            const identifier = @json(request()->route('id'));

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

            function money(value, currency) {
                const number = Number(value || 0);
                const symbol = currency === 'EUR' ? '€' : currency + ' ';
                return symbol + number.toFixed(2);
            }

            function renderOrder(order) {
                summaryEl.style.display = 'block';
                summaryEl.innerHTML = '';

                const title = document.createElement('h2');
                title.textContent = 'Ordine #' + order.id;
                summaryEl.appendChild(title);

                const uuid = document.createElement('p');
                uuid.innerHTML = '<strong>UUID:</strong> ' + (order.uuid || '-');
                summaryEl.appendChild(uuid);

                const status = document.createElement('p');
                status.innerHTML = '<strong>Stato:</strong> ' + order.status;
                summaryEl.appendChild(status);

                const total = document.createElement('p');
                total.innerHTML = '<strong>Totale:</strong> ' + money(order.total_price, order.currency);
                summaryEl.appendChild(total);

                itemsEl.innerHTML = '';
                const items = Array.isArray(order.items) ? order.items : [];

                if (!items.length) {
                    itemsEl.innerHTML = '<div class="empty">Nessuna riga ordine.</div>';
                    return;
                }

                items.forEach(function(item) {
                    const card = document.createElement('article');
                    card.className = 'card';

                    const productName = item.product && item.product.name ? item.product.name : ('Prodotto #' + item.product_id);
                    const title = document.createElement('h2');
                    title.textContent = productName;
                    card.appendChild(title);

                    const qty = document.createElement('p');
                    qty.innerHTML = '<strong>Quantità:</strong> ' + item.quantity;
                    card.appendChild(qty);

                    const price = document.createElement('p');
                    price.innerHTML = '<strong>Prezzo unitario storico:</strong> ' + money(item.price, order.currency);
                    card.appendChild(price);

                    const subtotal = document.createElement('p');
                    subtotal.innerHTML = '<strong>Subtotale:</strong> ' + money(Number(item.price) * Number(item.quantity), order.currency);
                    card.appendChild(subtotal);

                    itemsEl.appendChild(card);
                });
            }

            async function loadOrder() {
                clearMessage();

                try {
                    const response = await axios.get('/api/orders/' + identifier, {
                        headers: {
                            Authorization: 'Bearer ' + token,
                        },
                    });

                    const order = response.data && response.data.data ? response.data.data : null;
                    if (!order) {
                        showMessage('Ordine non trovato.', 'error');
                        return;
                    }

                    renderOrder(order);
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

                    if (error.response && error.response.status === 404) {
                        showMessage('Ordine non trovato o non autorizzato.', 'error');
                        return;
                    }

                    const fallback = (error.response && error.response.data && error.response.data.message) ?
                        error.response.data.message :
                        'Errore nel caricamento dettaglio ordine.';
                    showMessage(fallback, 'error');
                }
            }

            if (!ensureAuth()) {
                return;
            }

            loadOrder();
        });
    </script>
@endpush
