@extends('layouts.api-ui')

@section('title', 'Registrazione API')

@section('content')
    <h1>Registrazione</h1>
    <p class="page-subtitle">Questa pagina usa esclusivamente l'endpoint API <code>POST /api/register</code>.</p>

    <div id="message" class="alert"></div>

    <form id="register-form">
        <div class="field">
            <label for="name">Nome</label>
            <input type="text" id="name" name="name" placeholder="Il tuo nome" required>
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="esempio@email.com" required>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" minlength="8" required>
        </div>

        <div class="actions">
            <button type="submit" class="btn">Registrati</button>
            <a href="{{ route('ui.login') }}" class="btn btn-secondary">Hai già un account? Accedi</a>
        </div>
    </form>

    <p class="muted" id="token-status"></p>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const axios = window.axios;
            const form = document.getElementById('register-form');
            const message = document.getElementById('message');
            const tokenStatus = document.getElementById('token-status');

            if (!axios) {
                message.textContent = 'Axios non disponibile. Verifica che Vite stia caricando resources/js/app.js.';
                message.className = 'alert error show';
                return;
            }

            const existingToken = localStorage.getItem('api_token');
            if (existingToken) {
                tokenStatus.textContent = 'Sessione API già presente nel browser.';
            }

            function showMessage(text, type) {
                message.textContent = text;
                message.className = 'alert ' + type + ' show';
            }

            function extractError(error) {
                if (error.response && error.response.data && error.response.data.message) {
                    return error.response.data.message;
                }

                if (error.response && error.response.data && error.response.data.errors) {
                    return Object.values(error.response.data.errors).flat().join(' ');
                }

                return 'Errore inatteso durante la registrazione.';
            }

            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                message.className = 'alert';

                const payload = {
                    name: form.name.value,
                    email: form.email.value,
                    password: form.password.value,
                };

                try {
                    const response = await axios.post('/api/register', payload);
                    localStorage.setItem('api_token', response.data.token);
                    localStorage.setItem('api_user', JSON.stringify(response.data.user));
                    showMessage('Registrazione eseguita correttamente. Reindirizzamento...', 'success');

                    setTimeout(function() {
                        window.location.href = '{{ route('ui.products.index') }}';
                    }, 350);
                } catch (error) {
                    showMessage(extractError(error), 'error');
                }
            });
        });
    </script>
@endpush
