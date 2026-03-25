@extends('layouts.api-ui')

@section('title', 'Nuovo Prodotto API')

@section('content')
    <h1>Nuovo prodotto</h1>
    <p>Il form invia i dati all'endpoint <code>POST /api/products</code> (multipart/form-data).</p>

    <div id="message" class="alert"></div>

    <form id="product-form" enctype="multipart/form-data">
        <div class="field">
            <label for="name">Nome</label>
            <input type="text" id="name" name="name" maxlength="255" required>
        </div>

        <div class="field">
            <label for="description">Descrizione</label>
            <textarea id="description" name="description"></textarea>
        </div>

        <div class="field">
            <label for="price">Prezzo</label>
            <input type="number" id="price" name="price" min="0" step="0.01" required>
        </div>

        <div class="field">
            <label for="stock">Stock</label>
            <input type="number" id="stock" name="stock" min="0" step="1" required>
        </div>

        <div class="field">
            <label for="image">Immagine</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <div class="actions">
            <button type="submit" class="btn">Salva prodotto</button>
            <a href="{{ route('ui.products.index') }}" class="btn btn-secondary" style="text-decoration:none;">Torna alla lista</a>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const axios = window.axios;
            const token = localStorage.getItem('api_token');
            const form = document.getElementById('product-form');
            const messageEl = document.getElementById('message');

            function showMessage(text, type) {
                messageEl.textContent = text;
                messageEl.className = 'alert ' + type + ' show';
            }

            function clearMessage() {
                messageEl.className = 'alert';
                messageEl.textContent = '';
            }

            function extractError(error) {
                if (error.response && error.response.data && error.response.data.errors) {
                    return Object.values(error.response.data.errors).flat().join(' ');
                }

                if (error.response && error.response.data && error.response.data.message) {
                    return error.response.data.message;
                }

                return 'Errore durante il salvataggio del prodotto.';
            }

            if (!token) {
                showMessage('Devi prima fare login.', 'error');
                setTimeout(function() {
                    window.location.href = '{{ route('ui.login') }}';
                }, 500);
                return;
            }

            if (!axios) {
                showMessage('Axios non disponibile. Verifica che Vite stia caricando resources/js/app.js.', 'error');
                return;
            }

            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                clearMessage();

                const formData = new FormData(form);

                try {
                    await axios.post('/api/products', formData, {
                        headers: {
                            Authorization: 'Bearer ' + token,
                        },
                    });

                    showMessage('Prodotto creato con successo.', 'success');
                    form.reset();

                    setTimeout(function() {
                        window.location.href = '{{ route('ui.products.index') }}';
                    }, 500);
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

                    showMessage(extractError(error), 'error');
                }
            });
        });
    </script>
@endpush
