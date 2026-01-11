# Rendria PHP API

Core PHP implementation of Rendria’s public API.
This focuses on the API layer itself: routing, auth, rate limiting, and credit usage.

The actual image rendering is mocked. The goal here is to show backend structure and logic in plain PHP without a framework.


## About

Rendria is an API for automated image generation. This repo is a rewrite of the public API layer in core PHP.

What’s implemented:
- API key generation
- API key authentication
- Credit-based usage
- Simple rate limiting
- Manual routing and controllers

The rendering part (Puppeteer / Chrome) is intentionally not included so the focus stays on backend logic.


## Stack

- PHP 8.2+
- MySQL
- PDO (prepared statements only)
- Apache with mod_rewrite

No framework. Everything is wired manually.


## Project Structure

```
rendria-php/
├── public/
│   └── index.php
├── src/
│   ├── Core/          # Router, DB connection
│   ├── Middleware/    # API key auth, rate limiting
│   ├── Controllers/   # Request handling
│   └── Models/        # DB access
├── database/
│   ├── schema.sql
│   └── seed.sql
├── config/
└── .env
```


## Setup

### Requirements

- PHP 8.2+
- MySQL
- Apache (XAMPP works fine)

### Install

```bash
git clone <repo-url>
cd rendria-php
cp .env.example .env
```

Update `.env` with your DB credentials.

Create database:

```bash
mysql -u root -p -e "CREATE DATABASE rendria_php"
mysql -u root -p rendria_php < database/schema.sql
```

Optional seed data:

```bash
mysql -u root -p rendria_php < database/seed.sql
```

Serve from `public/`:

```bash
php -S localhost:8000 -t public
```

Health check:

```bash
curl http://localhost/rendria-php/public/health
# {"status":"ok"}
```


## API Usage

### Create API key

```bash
curl -X POST http://localhost/rendria-php/public/api/v1/keys \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com"}'
```

Response:

```json
{
  "api_key": "rnd_xxx",
  "credits": 100,
  "rate_limit": 100
}
```


### Render request

```bash
curl -X POST http://localhost/rendria-php/public/api/v1/render \
  -H "X-API-Key: your_key" \
  -H "Content-Type: application/json" \
  -d '{"template":"certificate","data":{"name":"John Doe"}}'
```

Each render deducts credits.

Response:

```json
{
  "request_id": 1,
  "status": "completed",
  "output_url": "https://example.com/mock.png",
  "credits_remaining": 98
}
```


### Usage stats

```bash
curl http://localhost/rendria-php/public/api/v1/usage \
  -H "X-API-Key: your_key"
```

Returns current credit balance and request counts.


## Implementation Notes

### Routing

Custom router that maps HTTP method + path to controllers. All requests go through index.php.

### Authentication

API keys are hashed before storage. Middleware validates keys on protected routes.

### Rate Limiting

Simple per key daily limit. Counters reset daily. Exceeded limits return 429.

### Credits

Render requests deduct credits. If credits run out, request fails.

### Database

PDO everywhere.  
No raw queries. Models are thin on purpose.

### Rendering

Rendering is mocked.

```php
// NOTE: rendering is mocked to keep focus on API logic
```

In production this would call a headless browser service via Lamda. 


## Endpoints

| Method | Path | Auth |
|------|------|------|
| GET | /health | No |
| POST | /api/v1/keys | No |
| POST | /api/v1/render | Yes |
| GET | /api/v1/usage | Yes |


## Final Note

This repo exists to show how an API layer works in core PHP without any framework.  
