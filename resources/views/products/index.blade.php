@extends('layouts.api-ui')

@section('title', 'Lista Prodotti API')

@section('content')
    <h1>Lista prodotti</h1>
    <p class="page-subtitle">I prodotti vengono caricati via <code>GET /api/products</code> con Bearer token.</p>

    <div class="toolbar">
        <button id="reload-btn" class="btn">Ricarica</button>
        <a href="{{ route('ui.products.create') }}" class="btn btn-secondary">Nuovo prodotto</a>
    </div>

    <div id="message" class="alert"></div>
    <div id="products" class="grid products-grid"></div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const axios = window.axios;
            const token = localStorage.getItem('api_token');
            const productsEl = document.getElementById('products');
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

                    const description = document.createElement('p');
                    description.textContent = product.description || 'Nessuna descrizione';
                    card.appendChild(description);

                    const price = document.createElement('p');
                    price.innerHTML = '<strong>Prezzo:</strong> €' + Number(product.price).toFixed(2);
                    card.appendChild(price);

                    const stock = document.createElement('p');
                    stock.innerHTML = '<strong>Stock:</strong> ' + product.stock;
                    card.appendChild(stock);

                    productsEl.appendChild(card);
                });
            }

            async function loadProducts() {
                clearMessage();

                if (!axios) {
                    showMessage('Axios non disponibile. Verifica che Vite stia caricando resources/js/app.js.', 'error');
                    return;
                }

                try {
                    const response = await axios.get('/api/products', {
                        headers: {
                            Authorization: 'Bearer ' + token,
                        },
                    });

                    renderProducts(response.data);
                } catch (error) {
                    const status = error.response && error.response.status;
                    if (status === 401) {
                        localStorage.removeItem('api_token');
                        localStorage.removeItem('api_user');
                        showMessage('Token non valido o scaduto. Effettua di nuovo il login.', 'error');
                        setTimeout(function() {
                            window.location.href = '{{ route('ui.login') }}';
                        }, 600);
                        return;
                    }

                    const fallback = (error.response && error.response.data && error.response.data.message)
                        ? error.response.data.message
                        : 'Errore nel caricamento dei prodotti.';
                    showMessage(fallback, 'error');
                }
            }

            if (!token) {
                showMessage('Devi prima fare login.', 'error');
                setTimeout(function() {
                    window.location.href = '{{ route('ui.login') }}';
                }, 500);
            } else {
                loadProducts();
            }

            reloadBtn.addEventListener('click', loadProducts);
        });
    </script>
@endpush
