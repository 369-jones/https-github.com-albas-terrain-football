# Terrain Football API

A bearer-token REST API covering the multi-stadium marketplace (pitches, bookings,
payouts) and the club back-office (équipes, réservations, paiements, factures).

- **Base URL:** `{APP_URL}/api/v1`
- **Format:** JSON in, JSON out (`Accept: application/json` on every request)
- **Auth:** [Laravel Sanctum](https://laravel.com/docs/sanctum) personal access tokens

## Getting a token

Only the platform admin can generate an API key today, from **Paramètres → Clé API**
in the club back-office (`/parametres`, admin-only). Click **Générer une clé API** —
the plaintext key is shown exactly once, immediately after creation. Copy it then;
it isn't stored anywhere retrievable and can't be shown again. Regenerating replaces
it; revoking removes it. There's one active key per account.

Send it on every request:

```
Authorization: Bearer <your-key>
Accept: application/json
```

A request without a valid token gets:

```http
HTTP/1.1 401 Unauthorized
{"message": "Unauthenticated."}
```

**Note on browsers:** typing an API URL directly into the address bar won't show you
JSON. A plain navigation sends `Accept: text/html`, so an unauthenticated hit
redirects to the login page instead of returning a 401 — that's normal Laravel
behavior, not an API bug. Use `curl`, `fetch()`, Postman, or similar for real calls.

## Rate limiting

60 requests/minute, keyed by user (or by IP if somehow unauthenticated). Exceeding it
returns `429 Too Many Requests`.

## Authorization model

Two different scoping rules apply depending on the resource, matching the web app
exactly:

| Resource | Who can access it |
|---|---|
| Pitches, Bookings | `owner` or `admin` role. An `owner` only sees/manages stadiums they're assigned to (`Pitch.owner_id`); `admin` sees and manages every stadium. |
| Payouts | Always scoped to the caller's own balance/destination — never widened for admin, since a payout is tied to one specific bank/mobile-money account. `mark-paid`/`mark-failed` are the exception: they require the `finance` role and can act on *any* owner's payout (that's the actual cross-owner oversight mechanism). |
| Équipes, Réservations, Paiements, Factures | `admin` role only. This is the legacy single-club back-office — there's no per-stadium concept here, so there's no owner-level access at all. |

A request that's authenticated but lacks the right role gets:

```http
HTTP/1.1 403 Forbidden
{"message": "This action is unauthorized."}
```

## Response shapes

**Single resource:**
```json
{ "data": { "id": 1, "...": "..." } }
```

**Collections** are paginated (20 per page) with Laravel's standard pagination envelope:
```json
{
  "data": [ { "...": "..." } ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "last_page": 3, "per_page": 20, "total": 47, "...": "..." }
}
```
Paginate with `?page=2`.

**Validation errors** (422):
```json
{ "message": "The email field is required. (and 1 more error)", "errors": { "email": ["The email field is required."] } }
```

---

## Pitches (stadiums)

| Method | Endpoint | Notes |
|---|---|---|
| GET | `/pitches` | List — scoped per the table above |
| GET | `/pitches/{id}` | Single pitch |
| POST | `/pitches` | Create — the caller becomes the owner |
| PUT | `/pitches/{id}` | Full replace (not PATCH — every field is required, same as the web edit form) |
| DELETE | `/pitches/{id}` | Soft-delete |

**Fields** (`store`/`update`): `name_fr`, `name_en`, `name_pt?`, `name_sw?`,
`description_fr?`, `description_en?`, `sport` (`football`\|`basketball`\|`volleyball`),
`country` (2-letter), `city`, `address?`, `surface_type`
(`natural_grass`\|`synthetic_turf`\|`concrete`\|`indoor`\|`hardwood`\|`sand`),
`capacity` (2–22), `amenities?` (array of strings), `price_per_hour`, `currency`
(3-letter), `is_active?`.

```bash
curl -X POST {base}/pitches \
  -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"name_fr":"Terrain X","name_en":"Court X","sport":"basketball","country":"CD","city":"Kinshasa","surface_type":"hardwood","capacity":10,"price_per_hour":25,"currency":"USD"}'
```

```json
{ "data": { "id": 9, "slug": "court-x", "sport": "basketball", "name": "Court X", "city": "Kinshasa", "country": "CD", "address": null, "surface_type": "hardwood", "capacity": 10, "amenities": [], "price_per_hour": 25, "currency": "USD", "is_active": false, "owner": { "id": 1, "name": "Moov", "email": "admin@terrainfoot.com" }, "created_at": "...", "updated_at": "..." } }
```

---

## Bookings

| Method | Endpoint | Notes |
|---|---|---|
| GET | `/bookings` | List — filters: `?status=`, `?from=YYYY-MM-DD`, `?to=YYYY-MM-DD` |
| GET | `/bookings/{id}` | Single booking |
| POST | `/bookings` | Manager/admin-entered booking (phone or walk-in) |
| PATCH | `/bookings/{id}` | Partial update — `status`, `payment_status`, `notes` |

**Fields** (`store`): `pitch_id`, `user_id` (an existing account — this endpoint
doesn't create players), `booking_date` (any date, including past — this endpoint
also backfills), `start_time`/`end_time` (`HH:MM`), `status?`
(`pending`\|`confirmed`\|`cancelled`\|`completed`\|`no_show`, default `confirmed`),
`payment_status?` (`unpaid`\|`paid`\|`refunded`\|`failed`, default `unpaid`), `notes?`.

Overlapping the same pitch/date/time-range with an existing active booking returns
`422` — the exact same row-locked conflict check as the public booking flow, so two
concurrent writes can't double-book a slot.

```bash
curl -X PATCH {base}/bookings/15 \
  -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"status":"cancelled"}'
```

---

## Payouts

| Method | Endpoint | Notes |
|---|---|---|
| GET | `/payouts` | Caller's own balance + payout history |
| PUT | `/payouts/destination` | Set the caller's own payout method |
| POST | `/payouts` | Request a payout of the caller's own available balance |
| POST | `/payouts/{id}/mark-paid` | **`finance` role required.** Any owner's payout. |
| POST | `/payouts/{id}/mark-failed` | **`finance` role required.** Any owner's payout — frees its payments to be withdrawn again. |

`GET /payouts` response includes an `available_balance` object keyed by currency
alongside the paginated history:
```json
{ "data": [ ... ], "links": {...}, "meta": {...}, "available_balance": { "USD": 30 } }
```

**`PUT /payouts/destination`** fields: `payout_method` (`bank_transfer`\|`mobile_money`),
then either `bank_name`/`account_name`/`account_number` or `mobile_provider`/`mobile_number`
depending on the method.

**`POST /payouts`** fields: `currency` (3-letter). Fails with `422` if no destination
is set yet, or if there's nothing to withdraw in that currency.

---

## Équipes (teams)

Full CRUD, `admin` only.

| Method | Endpoint |
|---|---|
| GET | `/equipes` |
| GET | `/equipes/{id}` |
| POST | `/equipes` |
| PUT/PATCH | `/equipes/{id}` |
| DELETE | `/equipes/{id}` |

**Fields:** `nom` (unique), `responsable`, `contact`, `faculte?`, `email?`.

---

## Réservations (club matches)

`admin` only. "Delete" cancels (`statut = annule`) rather than hard-deleting —
payments/invoices key off a reservation's existence.

| Method | Endpoint |
|---|---|
| GET | `/reservations` |
| GET | `/reservations/{id}` |
| POST | `/reservations` |
| PUT/PATCH | `/reservations/{id}` |
| DELETE | `/reservations/{id}` (cancels) |

**Fields:** `equipe_a_id`, `equipe_b_id` (must differ), `date_match`, `creneau`
(one of `08h00-10h00`\|`10h00-12h00`\|`14h00-16h00`\|`16h00-18h00`\|`18h00-20h00`),
`type_match` (one of `Match amical`\|`Championnat universitaire`\|`Coupe interfacultés`\|`Tournoi`),
`montant`, `devise?` (required on update, defaults to `XAF` on create), `notes?`.

Booking the same `date_match` + `creneau` as an existing non-cancelled reservation
returns `422`.

---

## Paiements (payments)

`admin` only. Index/show/store only — there's no edit or delete. A correction is
recording a new payment against the same reservation; it updates the existing
payment row in place (and its invoice, if one exists) rather than creating a second.

| Method | Endpoint |
|---|---|
| GET | `/paiements` |
| GET | `/paiements/{id}` |
| POST | `/paiements` |

**Fields:** `reservation_id`, `montant_paye` (capped at the reservation's `montant` —
paying the full amount sets the reservation to `confirme` and auto-issues its
invoice), `mode_paiement` (one of `Espèces`\|`Mobile Money`\|`Virement`\|`Chèque`),
`date_paiement`, `reference?`.

---

## Factures (invoices)

`admin` only. Entirely generated as a side effect of recording a full payment —
there's no create/update endpoint, only index/show/delete/pdf.

| Method | Endpoint | Notes |
|---|---|---|
| GET | `/factures` | List |
| GET | `/factures/{id}` | Single invoice — includes a `pdf_url` |
| DELETE | `/factures/{id}` | Hard delete |
| GET | `/factures/{id}/pdf` | Downloads the actual invoice PDF (same template as the web app) |

---

## Quick reference: try it yourself

```bash
TOKEN="paste your key here"
BASE="http://your-app-url/api/v1"

# Who am I
curl -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" $BASE/me

# List my stadiums
curl -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" $BASE/pitches

# Cancel a booking
curl -X PATCH -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" -H "Content-Type: application/json" \
  -d '{"status":"cancelled"}' $BASE/bookings/15
```
