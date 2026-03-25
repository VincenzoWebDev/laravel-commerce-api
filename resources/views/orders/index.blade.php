@extends('layouts.api-ui')

@section('title', 'Lista Ordini API')

@section('content')
    <h1>I tuoi ordini</h1>
    <p>Elenco caricato da <code>GET /api/orders</code> con azione pagamento su <code>POST /api/orders/{id}/pay</code>.</p>

    <div class="actions" style="margin-bottom: 12px;">
        <button id="reload-btn" class="btn" type="button">Ricarica</button>
        <a href="{{ route('ui.orders.create') }}" class="btn btn-secondary" style="text-decoration:none;">Nuovo ordine</a>
    </div>

    <div id="message" class="alert"></div>
    <div id="orders" class="grid"></div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const axios = window.axios;
            const token = localStorage.getItem('api_token');
            const ordersEl = document.getElementById('orders');
            const messageEl = document.getElementById('message');
            const reloadBtn = document.getElementById('reload-btn');

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

            function renderOrders(orders) {
                ordersEl.innerHTML = '';

                if (!orders.length) {
                    ordersEl.innerHTML = '<div class="empty">Non hai ancora ordini.</div>';
                    return;
                }

                orders.forEach(function(order) {
                    const card = document.createElement('article');
                    card.className = 'card';

                    const title = document.createElement('h2');
                    title.textContent = 'Ordine #' + order.id + ' (' + (order.uuid || '-') + ')';
                    card.appendChild(title);

                    const status = document.createElement('p');
                    status.innerHTML = '<strong>Stato:</strong> ' + order.status;
                    card.appendChild(status);

                    const total = document.createElement('p');
                    total.innerHTML = '<strong>Totale:</strong> ' + money(order.total_price, order.currency);
                    card.appendChild(total);

                    const itemsCount = document.createElement('p');
                    const count = Array.isArray(order.items) ? order.items.length : 0;
                    itemsCount.innerHTML = '<strong>Righe ordine:</strong> ' + count;
                    card.appendChild(itemsCount);

                    const actions = document.createElement('div');
                    actions.className = 'actions';

                    const details = document.createElement('a');
                    details.className = 'btn btn-secondary';
                    details.style.textDecoration = 'none';
                    details.href = '/orders/' + (order.uuid || order.id);
                    details.textContent = 'Dettaglio';
                    actions.appendChild(details);

                    if (order.status === 'pending') {
                        const payBtn = document.createElement('button');
                        payBtn.className = 'btn';
                        payBtn.type = 'button';
                        payBtn.textContent = 'Paga ordine';
                        payBtn.addEventListener('click', function() {
                            payOrder(order.uuid || order.id);
                        });
                        actions.appendChild(payBtn);
                    }

                    card.appendChild(actions);
                    ordersEl.appendChild(card);
                });
            }

            async function loadOrders() {
                clearMessage();

                try {
                    const response = await axios.get('/api/orders', {
                        headers: {
                            Authorization: 'Bearer ' + token,
                        },
                    });

                    const data = Array.isArray(response.data && response.data.data) ? response.data.data : [];
                    renderOrders(data);
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
                        'Errore nel caricamento ordini.';
                    showMessage(fallback, 'error');
                }
            }

            async function payOrder(identifier) {
                clearMessage();

                try {
                    const response = await axios.post('/api/orders/' + identifier + '/pay', {}, {
                        headers: {
                            Authorization: 'Bearer ' + token,
                        },
                    });

                    const text = response.data && response.data.message ? response.data.message : 'Pagamento riuscito';
                    showMessage(text, 'success');
                    loadOrders();
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
                        'Errore durante il pagamento.';
                    showMessage(details || fallback, 'error');
                }
            }

            if (!ensureAuth()) {
                return;
            }

            loadOrders();
            reloadBtn.addEventListener('click', loadOrders);
        });
    </script>
@endpush
