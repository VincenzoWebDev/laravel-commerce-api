@extends('layouts.api-ui')

@section('title', 'API Commerce UI')

@section('content')
    <h1>Laravel Commerce API</h1>
    <p class="page-subtitle">Interfaccia Blade di prova che usa solo le tue REST API (auth + products), senza sistema auth Blade nativo.</p>

    <div class="grid grid-cards">
        <a href="{{ route('ui.login') }}" class="card card-link">
            <h2>Login API</h2>
            <p>Autenticazione via <code>POST /api/login</code></p>
        </a>

        <a href="{{ route('ui.products.index') }}" class="card card-link">
            <h2>Lista prodotti</h2>
            <p>Recupero via <code>GET /api/products</code></p>
        </a>

        <a href="{{ route('ui.products.create') }}" class="card card-link">
            <h2>Nuovo prodotto</h2>
            <p>Creazione via <code>POST /api/products</code></p>
        </a>

        <a href="{{ route('ui.orders.index') }}" class="card card-link">
            <h2>Ordini utente</h2>
            <p>Recupero via <code>GET /api/orders</code></p>
        </a>

        <a href="{{ route('ui.orders.create') }}" class="card card-link">
            <h2>Nuovo ordine</h2>
            <p>Creazione via <code>POST /api/orders</code></p>
        </a>

        <a href="{{ route('ui.logout') }}" class="card card-link">
            <h2>Logout API</h2>
            <p>Disconnessione via <code>POST /api/logout</code></p>
        </a>
    </div>
@endsection
