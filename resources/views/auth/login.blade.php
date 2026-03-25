@extends('layouts.api-ui')

@section('title', 'Login API')

@section('content')
    <h1>Login</h1>
    <p class="page-subtitle">Questa pagina usa esclusivamente l'endpoint API <code>POST /api/login</code>.</p>

    <div id="message" class="alert"></div>

    <form id="login-form">
        <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="esempio@email.com" required>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" minlength="6" required>
        </div>

        <div class="actions">
            <button type="submit" class="btn">Accedi</button>
            <a href="{{ route('ui.products.index') }}" class="btn btn-secondary">Vai ai
                prodotti</a>
        </div>
    </form>

    <p class="muted" id="token-status"></p>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const axios = window.axios;
            const form = document.getElementById('login-form');
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

                return 'Errore inatteso durante il login.';
            }

            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                message.className = 'alert';

                const payload = {
                    email: form.email.value,
                    password: form.password.value,
                };

                try {
                    const response = await axios.post('/api/login', payload);
                    localStorage.setItem('api_token', response.data.token);
                    localStorage.setItem('api_user', JSON.stringify(response.data.user));
                    showMessage('Login eseguito correttamente. Reindirizzamento...', 'success');

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
