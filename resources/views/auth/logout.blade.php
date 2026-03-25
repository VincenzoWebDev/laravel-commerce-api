@extends('layouts.api-ui')

@section('title', 'Logout API')

@section('content')
    <h1>Logout</h1>
    <p class="page-subtitle">Questa pagina chiude la sessione chiamando <code>POST /api/logout</code> con token Bearer.</p>

    <div id="message" class="alert"></div>

    <div class="actions">
        <button id="logout-btn" class="btn btn-danger">Esegui logout</button>
        <a href="{{ route('ui.products.index') }}" class="btn btn-secondary">Annulla</a>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const axios = window.axios;
            const btn = document.getElementById('logout-btn');
            const messageEl = document.getElementById('message');

            function showMessage(text, type) {
                messageEl.textContent = text;
                messageEl.className = 'alert ' + type + ' show';
            }

            btn.addEventListener('click', async function() {
                const token = localStorage.getItem('api_token');

                if (!token) {
                    showMessage('Nessun token trovato. Reindirizzamento al login...', 'error');
                    setTimeout(function() {
                        window.location.href = '{{ route('ui.login') }}';
                    }, 500);
                    return;
                }

                if (!axios) {
                    showMessage('Axios non disponibile. Verifica che Vite stia caricando resources/js/app.js.', 'error');
                    return;
                }

                try {
                    await axios.post('/api/logout', {}, {
                        headers: {
                            Authorization: 'Bearer ' + token,
                        },
                    });

                    showMessage('Logout completato.', 'success');
                } catch (error) {
                    const fallback = (error.response && error.response.data && error.response.data.message)
                        ? error.response.data.message
                        : 'Errore durante il logout, pulizia locale in corso.';
                    showMessage(fallback, 'error');
                } finally {
                    localStorage.removeItem('api_token');
                    localStorage.removeItem('api_user');
                    setTimeout(function() {
                        window.location.href = '{{ route('ui.login') }}';
                    }, 500);
                }
            });
        });
    </script>
@endpush
