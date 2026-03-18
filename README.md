# Laravel Commerce API

Progetto di esempio di un backend e-commerce sviluppato con **Laravel 12**, **RESTful API**, **Sanctum** per autenticazione e gestione prodotti con upload immagini.

---

## Features

- Autenticazione API con **Sanctum** (register, login, logout)
- CRUD completo per i prodotti:
    - Creazione, lettura, aggiornamento e cancellazione
    - Validazione dei dati
    - Upload immagini prodotto
- Struttura RESTful per tutte le API
- Protezione endpoint con middleware `auth:sanctum`
- Blade come pannello admin leggero (interfaccia)
- Preparato per integrazione futura con ordini e pagamenti
- Repository pronto per estensione e integrazione front-end o app mobile

---

## Tecnologie

- PHP 8+ / Laravel 12
- MySQL
- Blade (frontend leggero)
- Sanctum (autenticazione token)
- Storage Laravel per immagini
- Postman consigliato per test API

---

## Installazione

1. Clona il repository:

git clone <tuo-repo-url>
cd laravel-commerce-api

2. Installa dipendenze:

composer install
npm install && npm run dev

## Configura Database

DB_DATABASE=laravel_commerce_api
DB_USERNAME=root
DB_PASSWORD=

## Migra database e crea symbolic link per storage

php artisan migrate
php artisan storage:link

## Avvia server

php artisan serve

---

## API principali

Tutte le rotte API sono protette da token Sanctum (tranne register/login).

### Auth

| Metodo | Endpoint      | Descrizione          |
| ------ | ------------- | -------------------- |
| POST   | /api/register | Registrazione utente |
| POST   | /api/login    | Login utente         |
| POST   | /api/logout   | Logout (protetto)    |

### Products

| Metodo | Endpoint           | Descrizione                     |
| ------ | ------------------ | ------------------------------- |
| GET    | /api/products      | Lista prodotti                  |
| GET    | /api/products/{id} | Dettaglio prodotto              |
| POST   | /api/products      | Crea prodotto (upload immagine) |
| PUT    | /api/products/{id} | Aggiorna prodotto               |
| DELETE | /api/products/{id} | Cancella prodotto               |

> **Nota:** per POST/PUT con immagini usare `form-data` in Postman.  
> Per ottenere l’immagine completa via API, usare `$product->image_url` (accessor nel model).

---

## Contributi

Progetto personale, aperto a suggerimenti.  
Segui la struttura RESTful e gli standard Laravel per aggiungere nuove funzionalità.

## Autore

**Vincenzo De Leonardis**  
Backend Laravel / RESTful API
