# Laravel Commerce API

Backend e-commerce sviluppato con **Laravel 12**, **REST API**, **Sanctum** e una UI Blade leggera che consuma esclusivamente le API.

---

## Features

- Autenticazione API con Sanctum (`register`, `login`, `logout`)
- CRUD prodotti con validazione e upload immagine
- Soft delete prodotti
- Gestione ordini completa:
  - Creazione ordine con `DB::transaction`
  - Righe ordine con prezzo storico (`order_items.price`)
  - Controllo e decremento stock
  - Simulazione pagamento ordine
- Stati ordine centralizzati con enum:
  - `pending`, `paid`, `shipped`, `cancelled`
- API Resource per output pulito (`OrderResource`, `OrderItemResource`)
- Service layer (`OrderService`) con controller sottili
- Sicurezza ownership: ogni utente vede/paga solo i propri ordini
- Eventi dominio:
  - `OrderCreated`
  - `OrderPaid`
- Logging operazioni ordini
- UUID ordine esposto in API (oltre all'id)
- UI Blade moderna, responsive e API-first per:
  - auth
  - prodotti
  - ordini (lista, creazione, dettaglio, pagamento)

---

## Tecnologie

- PHP 8+ / Laravel 12
- MySQL
- Blade (frontend API-first)
- Sanctum (token auth)
- Storage Laravel per immagini
- Postman consigliato per test API

---

## Installazione

1. Clona il repository:

```bash
git clone <tuo-repo-url>
cd laravel-commerce-api
```

2. Installa dipendenze:

```bash
composer install
npm install && npm run dev
```

## Configura Database

```env
DB_DATABASE=laravel_commerce_api
DB_USERNAME=root
DB_PASSWORD=
```

## Migra database e crea symbolic link per storage

```bash
php artisan migrate
php artisan storage:link
```

## Avvia server

```bash
php artisan serve
```

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

| Metodo | Endpoint           | Descrizione                      |
| ------ | ------------------ | -------------------------------- |
| GET    | /api/products      | Lista prodotti                   |
| GET    | /api/products/{id} | Dettaglio prodotto               |
| POST   | /api/products      | Crea prodotto (upload immagine)  |
| PUT    | /api/products/{id} | Aggiorna prodotto                |
| DELETE | /api/products/{id} | Soft delete prodotto             |

### Orders

| Metodo | Endpoint              | Descrizione                               |
| ------ | --------------------- | ----------------------------------------- |
| POST   | /api/orders           | Crea ordine                               |
| GET    | /api/orders           | Lista ordini utente autenticato           |
| GET    | /api/orders/{id}      | Dettaglio ordine (id o uuid)              |
| POST   | /api/orders/{id}/pay  | Simulazione pagamento (da pending a paid) |

---

## UI Frontend (Blade)

Pagine disponibili:

- `/login`
- `/logout`
- `/products`
- `/products/new`
- `/orders`
- `/orders/new`
- `/orders/{id}`

La UI usa soltanto le API (`/api/*`) con token salvato in `localStorage`.

---

## Architettura Backend

- `app/Services/OrderService.php`: logica core ordini/pagamento
- `app/Http/Requests/CreateOrderRequest.php`: validazione creazione ordine
- `app/Http/Resources/OrderResource.php`: serializzazione ordine
- `app/Http/Resources/OrderItemResource.php`: serializzazione righe + prodotto
- `app/Enums/OrderStatus.php`: stati ordine
- `app/Events/OrderCreated.php`, `app/Events/OrderPaid.php`: eventi dominio

---

## Note Migrazioni

- `orders.status` viene creato come stringa con default `pending`
- Su database già migrati, la migration di alter converte il campo in enum MySQL con i valori previsti
- Soft delete prodotti aggiunto con migration dedicata

---

## Contributi

Progetto personale, aperto a suggerimenti.  
Segui la struttura RESTful e gli standard Laravel per estendere funzionalità e UI.

## Autore

**Vincenzo De Leonardis**  
Backend Laravel / RESTful API
