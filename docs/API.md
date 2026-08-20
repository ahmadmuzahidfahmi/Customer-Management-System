# Mini CRM API Reference

Base URL: `https://your-domain.com/api`

All endpoints return JSON. Authenticated endpoints require a Sanctum bearer token.

Rate limit: **60 requests/minute** per authenticated user (or per IP if unauthenticated).

---

## Authentication

### Login

```
POST /api/login
```

**Body**

| Field       | Type    | Required | Notes |
|-------------|---------|----------|-------|
| `User_Name` | string  | yes      | |
| `password`  | string  | yes      | |
| `read_only` | boolean | no       | If `true`, issues a token that can only `GET` — any write attempt returns `403`. Defaults to `false` (full read/write access). |

**Response `200`**

```json
{
  "success": true,
  "token": "1|abcdef123456...",
  "abilities": ["read", "write"],
  "user": {
    "id": 1,
    "name": "jane",
    "role": "Admin"
  }
}
```

**Response `401`** — invalid credentials

```json
{ "success": false, "message": "Invalid credentials" }
```

Save the `token` and send it on every subsequent request:

```
Authorization: Bearer 1|abcdef123456...
```

To get a read-only token for a partner integration you don't fully trust:

```bash
curl -X POST https://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"User_Name":"jane","password":"secret","read_only":true}'
```

That token's `abilities` will be `["read"]` — it can call any `GET` endpoint, but `POST`/`PUT`/`DELETE` requests will return:

```json
{ "message": "Invalid ability provided." }
```
with HTTP status `403`.

### Logout

```
POST /api/logout
Authorization: Bearer <token>
```

Revokes the token used to make the request.

### Current user

```
GET /api/user
Authorization: Bearer <token>
```

Returns the authenticated `User` model.

---

## Customers

Backed by the `company` table.

| Method | Endpoint              | Description       |
|--------|------------------------|--------------------|
| GET    | `/api/customers`       | List (paginated, 15/page) |
| POST   | `/api/customers`       | Create             |
| GET    | `/api/customers/{id}`  | Show one           |
| PUT    | `/api/customers/{id}`  | Update             |
| DELETE | `/api/customers/{id}`  | Delete (soft)      |

**Create/Update body**

| Field            | Type   | Required (create) |
|------------------|--------|--------------------|
| `Company_Name`   | string | yes                |
| `Company_Email`  | email, unique | yes         |
| `Company_No`     | string | no                 |
| `Status`         | `Active` \| `Lead` \| `Inactive` | yes |

**Resource shape**

```json
{
  "id": 1,
  "name": "Acme Inc",
  "email": "hello@acme.com",
  "phone": "0123456789",
  "status": "Active",
  "closed_at": null,
  "created_at": "2026-07-01T09:00:00+00:00",
  "updated_at": "2026-07-01T09:00:00+00:00"
}
```

---

## Leads

| Method | Endpoint          | Description       |
|--------|--------------------|--------------------|
| GET    | `/api/leads`       | List (paginated)   |
| POST   | `/api/leads`       | Create             |
| GET    | `/api/leads/{id}`  | Show one           |
| PUT    | `/api/leads/{id}`  | Update             |
| DELETE | `/api/leads/{id}`  | Delete (soft)      |

**Create/Update body**

| Field              | Type    | Required (create) |
|--------------------|---------|--------------------|
| `Lead_Name`        | string  | yes                |
| `Company_ID`       | int, must exist in `company` | yes |
| `Estimated_Value`  | numeric | no                 |
| `Source`           | string  | no                 |
| `Status`           | `New` \| `Contacted` \| `Qualified` \| `Won` \| `Lost` | yes |

**Resource shape**

```json
{
  "id": 5,
  "name": "Website redesign",
  "source": "Referral",
  "status": "Qualified",
  "estimated_value": 15000,
  "position": 2,
  "company_id": 1,
  "contact_id": null,
  "assigned_to": 3,
  "status_changed_at": "2026-07-20T10:00:00+00:00",
  "created_at": "2026-07-01T09:00:00+00:00",
  "updated_at": "2026-07-20T10:00:00+00:00"
}
```

---

## Contacts

| Method | Endpoint             | Description       |
|--------|-----------------------|--------------------|
| GET    | `/api/contacts`       | List (paginated)   |
| POST   | `/api/contacts`       | Create             |
| GET    | `/api/contacts/{id}`  | Show one           |
| PUT    | `/api/contacts/{id}`  | Update             |
| DELETE | `/api/contacts/{id}`  | Delete (soft)      |

**Create/Update body**

| Field            | Type   | Required (create) |
|------------------|--------|--------------------|
| `Contact_Name`   | string | yes                |
| `Contact_Email`  | email  | no                 |
| `Contact_No`     | string | no                 |
| `Country_Code`   | string | no                 |
| `Contact_Role`   | string | no                 |
| `Contact_Note`   | string | no                 |
| `Company_ID`     | int, must exist in `company` | yes |

**Resource shape**

```json
{
  "id": 8,
  "name": "John Tan",
  "email": "john@acme.com",
  "phone": "0198765432",
  "country_code": "+60",
  "role": "Procurement Manager",
  "note": null,
  "company_id": 1,
  "created_at": "2026-07-01T09:00:00+00:00",
  "updated_at": "2026-07-01T09:00:00+00:00"
}
```

---

## Activities

| Method | Endpoint                | Description       |
|--------|---------------------------|--------------------|
| GET    | `/api/activities`         | List (paginated, filterable) |
| POST   | `/api/activities`         | Create             |
| GET    | `/api/activities/{id}`    | Show one           |
| PUT    | `/api/activities/{id}`    | Update             |
| DELETE | `/api/activities/{id}`    | Delete             |

**List filters** (query params, all optional)

| Param        | Description                       |
|--------------|------------------------------------|
| `status`     | `Pending` \| `Completed` \| `Cancelled` |
| `type`       | `Call` \| `Email` \| `Meeting` \| `Follow-Up` \| `Other` |
| `contact_id` | filter by linked contact           |
| `lead_id`    | filter by linked lead              |

**Create body**

| Field              | Type   | Required |
|--------------------|--------|----------|
| `Activity_Type`    | `Call` \| `Email` \| `Meeting` \| `Follow-Up` \| `Other` | yes |
| `Subject`          | string | yes      |
| `Activity_Detail`  | string | no       |
| `Dead_Line`        | date (`Y-m-d` or `Y-m-d\TH:i`) | no |
| `Contact_ID`       | int, must exist in `contacts` | one of `Contact_ID`/`Lead_ID` required |
| `Lead_ID`          | int, must exist in `leads`    | one of `Contact_ID`/`Lead_ID` required |
| `Assigned_To`      | int, must exist in `users`. Defaults to the authenticated user. | no |

An activity must be linked to at least a Lead or a Contact — omitting both returns a `422`:

```json
{ "success": false, "message": "An activity must be linked to a Lead or a Contact." }
```

**Update body** — same fields as create, all optional, plus:

| Field    | Type   |
|----------|--------|
| `Status` | `Pending` \| `Completed` \| `Cancelled` |

**Resource shape**

```json
{
  "id": 42,
  "type": "Call",
  "subject": "Follow up on proposal",
  "detail": null,
  "status": "Pending",
  "is_overdue": false,
  "dead_line": "2026-07-28T14:30:00+00:00",
  "completed_at": null,
  "company_id": null,
  "contact_id": 8,
  "lead_id": null,
  "assigned_to": 3,
  "created_at": "2026-07-27T09:00:00+00:00",
  "updated_at": "2026-07-27T09:00:00+00:00"
}
```

---

## Errors

- **`401 Unauthorized`** — missing/invalid/expired token.
- **`403 Forbidden`** — the token's abilities don't permit this action (e.g. a read-only token attempting a write). `{ "message": "Invalid ability provided." }`
- **`404 Not Found`** — record doesn't exist: `{ "success": false, "message": "... not found" }`
- **`422 Unprocessable Entity`** — validation failure. Standard Laravel shape:

```json
{
  "message": "The Company Name field is required.",
  "errors": {
    "Company_Name": ["The Company Name field is required."]
  }
}
```

- **`429 Too Many Requests`** — rate limit exceeded (60/min).

---

## Example: full flow with `curl`

```bash
# 1. Log in
TOKEN=$(curl -s -X POST https://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"User_Name":"jane","password":"secret"}' | jq -r '.token')

# 2. List leads
curl https://your-domain.com/api/leads \
  -H "Authorization: Bearer $TOKEN"

# 3. Create an activity
curl -X POST https://your-domain.com/api/activities \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "Activity_Type": "Call",
    "Subject": "Intro call",
    "Contact_ID": 8,
    "Dead_Line": "2026-08-01T10:00"
  }'
```

---

## Known limitations (as of this doc)

- Abilities are coarse-grained: `read` or `write`, applied uniformly across all four resources. There's no per-resource scoping yet (e.g. a token that can write Activities but only read Customers).
- No webhook/event support — external apps must poll for changes.
- Pagination is fixed at 15 per page (not configurable via query param yet).
