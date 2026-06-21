# WayWay Tourism Platform - Integration Testing Scenarios

## Overview

| Field | Value |
|-------|-------|
| Application | WayWay Tourism Platform |
| Type | Web Application (Laravel 11) |
| Testing Type | Integration Testing |
| Base URL | http://127.0.0.1:8000 |
| Roles | wisatawan (tourist), pemilik_wisata (tourism owner), travel_agent, admin |

---

## SCENARIO 1: User Registration

### TC-INT-001: Successful Wisatawan Registration

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-001 |
| **Description** | Verify that a new user can successfully register as wisatawan and is automatically assigned the Basic package |
| **Preconditions** | Application is running, email address is not already registered |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /register` | Response 200, registration form loads |
| 2 | `POST /register` with body: `name="Budi Santoso"`, `email="budi@test.com"`, `password="Password123"`, `password_confirmation="Password123"` | Request accepted |
| 3 | Follow redirect to `/wisatawan/beranda` | Response 302 → 200 |
| 4 | Query `users` table for new record | Record exists with `role='wisatawan'` |
| 5 | Check `current_paket_id` on new user record | Value is not null (Basic package assigned) |

**Expected Results:**
- User record created in `users` table with `role='wisatawan'`
- User redirected to `/wisatawan/beranda`
- `current_paket_id` is set to the Basic package ID

**Assertions:**
- Response code: `302` redirect
- DB `users` table: new record with `role='wisatawan'`
- DB `users.current_paket_id`: not null

---

### TC-INT-002: Registration with Duplicate Email

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-002 |
| **Description** | Verify that registration fails when using an already-registered email address |
| **Preconditions** | A user with the target email already exists in the database |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /register` with an email that already exists in the `users` table | Validation error returned |
| 2 | Inspect response body | Error message references the `email` field |

**Expected Results:**
- HTTP `422 Unprocessable Entity`
- Response body contains validation errors for the `email` field

**Negative Test Cases:**
- Attempt registration with the same email in different casing (e.g., `BUDI@test.com`) — should still be rejected

---

### TC-INT-003: Registration with Mismatched Passwords

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-003 |
| **Description** | Verify that registration fails when `password` and `password_confirmation` do not match |
| **Preconditions** | Application is running |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /register` with `password="Password123"`, `password_confirmation="Different123"` | Validation error returned |

**Expected Results:**
- HTTP `422 Unprocessable Entity`
- Response body contains validation error for `password_confirmation`

---

### TC-INT-004: Registration with Invalid Email Format

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-004 |
| **Description** | Verify that registration fails when the email field contains an invalid format |
| **Preconditions** | Application is running |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /register` with `email="notanemail"` | Validation error returned |

**Expected Results:**
- HTTP `422 Unprocessable Entity`
- Response body contains validation error for `email`

**Negative Test Cases:**
- `email="missing@domain"` — should fail
- `email="@nodomain.com"` — should fail
- `email="spaces in@email.com"` — should fail

---

### TC-INT-005: Registration with Short Password

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-005 |
| **Description** | Verify that registration fails when the password does not meet the minimum length requirement |
| **Preconditions** | Application is running |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /register` with `password="abc"` | Validation error returned |

**Expected Results:**
- HTTP `422 Unprocessable Entity`
- Response body contains validation error indicating minimum password length

---

## SCENARIO 2: User Login

### TC-INT-006: Successful Wisatawan Login

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-006 |
| **Description** | Verify that a wisatawan can log in and is redirected to the correct dashboard |
| **Preconditions** | User exists in the database with `role='wisatawan'` and known credentials |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /login` | Response 200, login form loads |
| 2 | Extract CSRF token from response HTML | Token obtained |
| 3 | `POST /login` with `email="wisatawan@test.com"`, `password="Password123"`, `_token=<csrf_token>` | Request accepted |
| 4 | Follow redirect | Lands on `/wisatawan/beranda` (200) |
| 5 | Inspect response headers | Session cookie is set |

**Expected Results:**
- HTTP `302` redirect to `/wisatawan/beranda`
- Session cookie established in the browser

---

### TC-INT-007: Successful Admin Login

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-007 |
| **Description** | Verify that an admin user is redirected to the admin dashboard after login |
| **Preconditions** | Admin user exists with `role='admin'` |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /login` with admin credentials | Request accepted |
| 2 | Follow redirect | Lands on `/admin/dashboard` (200) |

**Expected Results:**
- HTTP `302` redirect to `/admin/dashboard`

---

### TC-INT-008: Successful Pemilik Wisata Login

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-008 |
| **Description** | Verify that a pemilik_wisata user is redirected to the pemilik dashboard after login |
| **Preconditions** | User exists with `role='pemilik_wisata'` |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /login` with pemilik credentials | Request accepted |
| 2 | Follow redirect | Lands on `/pemilik/dashboard` (200) |

**Expected Results:**
- HTTP `302` redirect to `/pemilik/dashboard`

---

### TC-INT-009: Successful Travel Agent Login

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-009 |
| **Description** | Verify that a travel_agent user is redirected to the travel agent dashboard after login |
| **Preconditions** | User exists with `role='travel_agent'` |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /login` with travel agent credentials | Request accepted |
| 2 | Follow redirect | Lands on `/travel-agent/dashboard` (200) |

**Expected Results:**
- HTTP `302` redirect to `/travel-agent/dashboard`

---

### TC-INT-010: Login with Wrong Password

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-010 |
| **Description** | Verify that login fails when the correct email is provided with an incorrect password |
| **Preconditions** | User exists with known email |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /login` with correct email and wrong password | Authentication fails |
| 2 | Follow redirect back to `/login` | Error message displayed |

**Expected Results:**
- HTTP `302` redirect back to login page
- Error message about invalid credentials shown

---

### TC-INT-011: Login with Non-existent Email

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-011 |
| **Description** | Verify that login fails when the email does not exist in the system |
| **Preconditions** | Email `nonexistent@test.com` is not registered |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /login` with `email="nonexistent@test.com"` and any password | Authentication fails |

**Expected Results:**
- Error response indicating invalid credentials
- No information leaked about whether the email exists

---

### TC-INT-012: Role-Based Redirect After Login

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-012 |
| **Description** | Verify that each user role is redirected to the correct dashboard upon successful login |
| **Preconditions** | One user per role exists with known credentials |

**Test Steps:**

| Step | Role | Login Credentials | Expected Redirect |
|------|------|-------------------|-------------------|
| 1 | wisatawan | `wisatawan@test.com` | `/wisatawan/beranda` |
| 2 | admin | `admin@wayway.com` | `/admin/dashboard` |
| 3 | pemilik_wisata | `pemilik@test.com` | `/pemilik/dashboard` |
| 4 | travel_agent | `agent@test.com` | `/travel-agent/dashboard` |

**Expected Results:**
- Each role is redirected to its designated dashboard
- No cross-role redirects occur

---

## SCENARIO 3: Profile Management

### TC-INT-013: Wisatawan Update Profile

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-013 |
| **Description** | Verify that a logged-in wisatawan can update their profile information |
| **Preconditions** | Authenticated as wisatawan |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /wisatawan/profil` | Response 200, profile page loads |
| 2 | `PUT /wisatawan/profile` with `name="Updated Name"`, `no_telepon="08123456789"` | Request accepted |
| 3 | Follow redirect | Success message displayed |
| 4 | `GET /wisatawan/profil` | Updated name and phone number shown |

**Expected Results:**
- Profile updated successfully in the database
- Success message displayed to the user
- Updated data reflected on the profile page

---

### TC-INT-014: Update Profile with Invalid Email

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-014 |
| **Description** | Verify that profile update fails when an invalid email format is submitted |
| **Preconditions** | Authenticated as wisatawan |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `PUT /wisatawan/profile` with `email="notanemail"` | Validation error returned |

**Expected Results:**
- HTTP `422 Unprocessable Entity`
- Validation error on the `email` field

---

### TC-INT-015: Admin Update Profile

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-015 |
| **Description** | Verify that an admin can update their own profile |
| **Preconditions** | Authenticated as admin |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `PUT /admin/profile` with valid updated data | Request accepted |
| 2 | Verify response | Success message or redirect |

**Expected Results:**
- Admin profile updated successfully in the database

---

### TC-INT-016: Pemilik Update Profile

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-016 |
| **Description** | Verify that a pemilik_wisata can update their own profile |
| **Preconditions** | Authenticated as pemilik_wisata |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `PUT /pemilik/profile` with valid updated data | Request accepted |
| 2 | Verify response | Success message or redirect |

**Expected Results:**
- Pemilik profile updated successfully in the database

---

### TC-INT-017: Unauthorized Profile Access

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-017 |
| **Description** | Verify that unauthenticated users cannot access the profile page |
| **Preconditions** | No active session (not logged in) |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /wisatawan/profil` without authentication | Redirect to login |

**Expected Results:**
- HTTP `302` redirect to `/login`
- Profile page not accessible without authentication

**Negative Test Cases:**
- Attempt to access `/pemilik/dashboard` without login — should redirect to `/login`
- Attempt to access `/admin/dashboard` without login — should redirect to `/login`

---

## SCENARIO 4: Tourism Destination Management

### TC-INT-018: Pemilik Create Destinasi (Basic Package)

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-018 |
| **Description** | Verify that a pemilik with a Basic package can create one destination |
| **Preconditions** | Authenticated as pemilik_wisata with Basic package (0 existing destinations) |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /pemilik/destinasi` | Response 200, destination list loads |
| 2 | `POST /pemilik/destinasi` with: `nama_destinasi="Pantai Nongsa"`, `latitude=1.15`, `longitude=104.12`, `deskripsi="Pantai indah"`, `harga=25000`, `kategori_id=1` | Request accepted |
| 3 | Follow redirect | Success message displayed |
| 4 | Query `destinasi` table | New record exists with correct data |

**Expected Results:**
- Destination created with `status='active'`
- Record visible in the pemilik's destination list

---

### TC-INT-019: Pemilik Exceeds Destination Limit

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-019 |
| **Description** | Verify that a pemilik with a Basic package cannot create more than 1 destination |
| **Preconditions** | Authenticated as pemilik_wisata with Basic package, already has 1 active destination |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /pemilik/destinasi/create` | Redirect with error message |

**Expected Results:**
- HTTP `302` redirect
- Error message: "Batas destinasi sudah tercapai" (or equivalent)
- No new destination created

---

### TC-INT-020: Pemilik Edit Destinasi (Basic Package - No Direct Edit)

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-020 |
| **Description** | Verify that a pemilik with a Basic package is redirected to the edit-request form instead of direct editing |
| **Preconditions** | Authenticated as pemilik_wisata with Basic package, owns at least one destination |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /pemilik/destinasi/{id}/edit` | Redirect to edit-request form |

**Expected Results:**
- HTTP `302` redirect to the edit request form
- Informational message explaining that direct editing requires a higher package

---

### TC-INT-021: Pemilik Edit Destinasi (Standard/Premium Package)

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-021 |
| **Description** | Verify that a pemilik with Standard or Premium package can directly edit their destination |
| **Preconditions** | Authenticated as pemilik_wisata with Standard or Premium package, owns at least one destination |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /pemilik/destinasi/{id}/edit` | Response 200, edit form loads |
| 2 | `PUT /pemilik/destinasi/{id}` with updated destination data | Request accepted |
| 3 | Follow redirect | Success message displayed |

**Expected Results:**
- Destination updated successfully in the database
- Changes reflected on the destination detail page

---

### TC-INT-022: Pemilik Delete Destinasi (Soft Delete)

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-022 |
| **Description** | Verify that deleting a destination performs a soft delete (status change) rather than a hard delete |
| **Preconditions** | Authenticated as pemilik_wisata, owns at least one destination |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `DELETE /pemilik/destinasi/{id}` | Request accepted |
| 2 | Follow redirect | Success message displayed |
| 3 | Query `destinasi` table for the record | Record still exists with `status='inactive'` |

**Expected Results:**
- Destination `status` changed to `'inactive'`
- Record is **not** removed from the database (soft delete)
- Destination no longer appears in public listings

---

### TC-INT-023: Pemilik Cannot Edit Other's Destinasi

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-023 |
| **Description** | Verify that a pemilik cannot edit a destination owned by a different user |
| **Preconditions** | Authenticated as pemilik_wisata, a destination owned by a different pemilik exists |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /pemilik/destinasi/{other_user_destination_id}/edit` | 404 response |

**Expected Results:**
- HTTP `404 Not Found`
- No unauthorized access to another user's destination

**Negative Test Cases:**
- Attempt `PUT /pemilik/destinasi/{other_id}` directly — should return `404`
- Attempt `DELETE /pemilik/destinasi/{other_id}` — should return `404`

---

## SCENARIO 5: Travel Agent Management

### TC-INT-024: Travel Agent Create Package (Within Limit)

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-024 |
| **Description** | Verify that a travel agent can create a new tour package within their subscription limit |
| **Preconditions** | Authenticated as travel_agent with an active subscription and available package slots |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /travel-agent/packages/create` | Response 200, create form loads |
| 2 | `POST /travel-agent/packages` with: `nama_paket="Paket Batam 3D2N"`, `harga_per_orang=500000`, `durasi_hari=3`, `tanggal_keberangkatan="2026-12-01"`, `destinasi[]=["Pantai Nongsa"]`, `min_peserta=2`, `max_peserta=20`, `meeting_point="Batam Center"` | Request accepted |
| 3 | Follow redirect | Success message displayed |

**Expected Results:**
- Package created successfully in the database
- Package visible in the travel agent's package list

---

### TC-INT-025: Travel Agent Exceeds Package Limit

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-025 |
| **Description** | Verify that a travel agent cannot create packages beyond their subscription limit |
| **Preconditions** | Authenticated as travel_agent, already at the maximum number of packages for their subscription tier |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /travel-agent/packages/create` | Redirect with error message |

**Expected Results:**
- HTTP `302` redirect
- Error message: "Anda sudah mencapai limit paket" (or equivalent)
- No new package created

---

### TC-INT-026: Travel Agent Update Package

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-026 |
| **Description** | Verify that a travel agent can update their own package |
| **Preconditions** | Authenticated as travel_agent, owns at least one package |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `PUT /travel-agent/packages/{id}` with updated package data | Request accepted |
| 2 | Verify response | Success message or redirect |

**Expected Results:**
- Package updated successfully in the database

---

### TC-INT-027: Travel Agent Delete Package

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-027 |
| **Description** | Verify that a travel agent can delete their own package |
| **Preconditions** | Authenticated as travel_agent, owns at least one package |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `DELETE /travel-agent/packages/{id}` | Request accepted |
| 2 | Verify response | Success message |
| 3 | Query database | Package record removed or marked as deleted |

**Expected Results:**
- Package successfully deleted
- Package no longer appears in listings

---

### TC-INT-028: Travel Agent Cannot Edit Other's Package

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-028 |
| **Description** | Verify that a travel agent cannot edit a package owned by a different travel agent |
| **Preconditions** | Authenticated as travel_agent, a package owned by a different agent exists |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `PUT /travel-agent/packages/{other_agent_package_id}` with any data | 404 response |

**Expected Results:**
- HTTP `404 Not Found`
- No unauthorized modification of another agent's package

**Negative Test Cases:**
- Attempt `DELETE /travel-agent/packages/{other_id}` — should return `404`

---

## SCENARIO 6: Tourism Search and Discovery

### TC-INT-029: Search Destinasi by Keyword

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-029 |
| **Description** | Verify that the destination search returns relevant results when filtering by keyword |
| **Preconditions** | At least one active destination with "pantai" in its name exists |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /destinasi?q=pantai` | Response 200 |
| 2 | Inspect response body | Results contain "pantai" in destination names |

**Expected Results:**
- HTTP `200 OK`
- Search results filtered to destinations matching the keyword

---

### TC-INT-030: Filter Destinasi by Kategori

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-030 |
| **Description** | Verify that destinations can be filtered by category |
| **Preconditions** | At least one active destination with `kategori_id=1` exists |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /destinasi?kategori=1` | Response 200 |
| 2 | Inspect response body | All results belong to `kategori_id=1` |

**Expected Results:**
- HTTP `200 OK`
- All returned destinations belong to the specified category

---

### TC-INT-031: API - Get Destinasi by Kategori

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-031 |
| **Description** | Verify the API endpoint returns destinations filtered by category as a JSON array |
| **Preconditions** | At least one active destination with `kategori_id=1` exists |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /api/destinasi/kategori/1` | Response 200 with JSON body |
| 2 | Inspect JSON response | Response is an array of destination objects |

**Expected Results:**
- HTTP `200 OK`
- `Content-Type: application/json`
- Response body is a JSON array of destinations in the specified category

---

### TC-INT-032: Search with No Results

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-032 |
| **Description** | Verify that a search with no matching results returns a 200 response with an empty/no-results message |
| **Preconditions** | No destination with "xyznonexistent123" in its name exists |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /destinasi?q=xyznonexistent123` | Response 200 |
| 2 | Inspect response body | Empty results or "no results found" message shown |

**Expected Results:**
- HTTP `200 OK`
- Page renders without error
- Empty state or no-results message displayed

---

### TC-INT-033: Destinasi Detail Page

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-033 |
| **Description** | Verify that the destination detail page loads with all required information |
| **Preconditions** | An active destination with a known ID exists |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /destinasi/{id}` | Response 200 |
| 2 | Inspect response body | Page contains destination name, description, and price |

**Expected Results:**
- HTTP `200 OK`
- Destination name, description, and price are displayed
- Map/coordinates section rendered

---

### TC-INT-034: Destinasi Detail - Not Found

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-034 |
| **Description** | Verify that accessing a non-existent destination returns a 404 error |
| **Preconditions** | No destination with ID `99999` exists |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /destinasi/99999` | Response 404 |

**Expected Results:**
- HTTP `404 Not Found`
- Appropriate error page displayed

---

## SCENARIO 7: Package Promotion Purchase

### TC-INT-035: Pemilik View Available Packages

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-035 |
| **Description** | Verify that a pemilik can view all available promotion packages with correct pricing |
| **Preconditions** | Authenticated as pemilik_wisata |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /pemilik/paket` | Response 200 |
| 2 | Inspect response body | Basic (free), Standard (Rp 49.000), Premium (Rp 149.000) all displayed |

**Expected Results:**
- HTTP `200 OK`
- All 3 packages (Basic, Standard, Premium) displayed with correct pricing

---

### TC-INT-036: Pemilik Checkout Standard Package

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-036 |
| **Description** | Verify that a pemilik can initiate checkout for the Standard package and a pending transaction is created |
| **Preconditions** | Authenticated as pemilik_wisata currently on Basic package |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /pemilik/paket/{standard_id}/checkout` | Midtrans Snap token returned or redirect to payment page |
| 2 | Query `transaksi_promosi` table | New record exists with `status='pending'` |

**Expected Results:**
- Payment flow initiated (Midtrans Snap token or redirect)
- `TransaksiPromosi` record created with `status='pending'`

---

### TC-INT-037: Midtrans Webhook - Payment Settlement

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-037 |
| **Description** | Verify that a valid Midtrans settlement webhook activates the purchased package for the user |
| **Preconditions** | A pending `TransaksiPromosi` record exists |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /api/midtrans/notification` with: `order_id="TRX-{user_id}-{timestamp}"`, `transaction_status="settlement"`, `gross_amount="49000.00"`, valid `signature_key` | Response 200 |
| 2 | Inspect response body | `message="OK"` |
| 3 | Query `transaksi_promosi` table | `status` updated to `'paid'` |
| 4 | Query `users` table | `current_paket_id` updated to Standard package ID |

**Expected Results:**
- HTTP `200 OK` with `{"message": "OK"}`
- `TransaksiPromosi.status` = `'paid'`
- User's `current_paket_id` updated to the Standard package

---

### TC-INT-038: Midtrans Webhook - Invalid Signature

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-038 |
| **Description** | Verify that a Midtrans webhook with an invalid signature is rejected |
| **Preconditions** | Application is running |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /api/midtrans/notification` with an invalid `signature_key` | Response 403 |
| 2 | Inspect response body | `message="Invalid signature"` |

**Expected Results:**
- HTTP `403 Forbidden`
- Response body: `{"message": "Invalid signature"}`
- No database changes made

---

### TC-INT-039: Midtrans Webhook - Payment Expire

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-039 |
| **Description** | Verify that an expired payment webhook correctly marks the transaction as expired |
| **Preconditions** | A pending `TransaksiPromosi` record exists |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /api/midtrans/notification` with `transaction_status="expire"` and valid signature | Response 200 |
| 2 | Query `transaksi_promosi` table | `status` updated to `'expired'` |

**Expected Results:**
- `TransaksiPromosi.status` = `'expired'`
- User's `current_paket_id` remains unchanged

---

## SCENARIO 8: Package Promotion Activation

### TC-INT-040: Admin Approve Transaksi

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-040 |
| **Description** | Verify that an admin can approve a pending transaction, activating the package for the user |
| **Preconditions** | Authenticated as admin, at least one pending `TransaksiPromosi` exists |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /admin/transaksi` | Response 200, pending transaction visible in list |
| 2 | `POST /admin/transaksi/{id}/approve` | Request accepted |
| 3 | Follow redirect | Success message displayed |
| 4 | Query `transaksi_promosi` table | `status='paid'` |
| 5 | Query `users` table | `current_paket_id` updated to the purchased package |

**Expected Results:**
- `TransaksiPromosi.status` = `'paid'`
- User's package activated (correct `current_paket_id`)

---

### TC-INT-041: Admin Reject Transaksi

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-041 |
| **Description** | Verify that an admin can reject a pending transaction |
| **Preconditions** | Authenticated as admin, at least one pending `TransaksiPromosi` exists |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /admin/transaksi/{id}/reject` | Request accepted |
| 2 | Query `transaksi_promosi` table | `status='rejected'` |

**Expected Results:**
- `TransaksiPromosi.status` = `'rejected'`
- User's `current_paket_id` remains unchanged

**Negative Test Cases:**
- Attempt to approve an already-rejected transaction — should return an error
- Attempt to reject an already-paid transaction — should return an error

---

### TC-INT-042: Package Limits Enforced After Activation

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-042 |
| **Description** | Verify that the new package limits are correctly applied after a user upgrades to Standard |
| **Preconditions** | User has been upgraded to Standard package (via TC-INT-037 or TC-INT-040) |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | Attempt to create a 2nd and 3rd destination | Both succeed (Standard allows up to 3) |
| 2 | Attempt to edit a destination directly | Edit form accessible (no redirect to edit-request) |
| 3 | Check photo upload limit | Maximum 8 photos allowed |

**Expected Results:**
- User can create up to 3 destinations
- Direct editing is enabled
- Photo limit is 8 (`max_foto = 8`)

---

## SCENARIO 9: Itinerary Management

### TC-INT-043: Generate Itinerary

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-043 |
| **Description** | Verify that a wisatawan can generate a travel itinerary based on preferences |
| **Preconditions** | Authenticated as wisatawan, active destinations exist in the database |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /itinerary` | Response 200, itinerary page loads |
| 2 | `POST /itinerary/generate` with: `kategori_ids=[1,2]`, `budget=100000`, `companion="keluarga"`, `tanggal="2026-12-25"`, `origin_lat=1.1296758`, `origin_lon=104.0452254`, `origin_label="Batam Center"`, `max_destinations=4`, `available_hours=8` | Response 200 JSON |
| 3 | Inspect JSON response | `response.success = true` |
| 4 | Inspect JSON response | `response.data.history_id` is set |
| 5 | Save `history_id` for subsequent test steps | — |

**Expected Results:**
- HTTP `200 OK` with JSON body
- `success: true`
- `data.history_id` is a valid integer
- Itinerary contains a route with destinations

---

### TC-INT-044: View Generated Itinerary

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-044 |
| **Description** | Verify that a previously generated itinerary can be viewed by its history ID |
| **Preconditions** | Authenticated as wisatawan, `history_id` obtained from TC-INT-043 |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /itinerary/show/{history_id}` | Response 200 |
| 2 | Inspect response body | Itinerary details (destinations, route, budget) displayed |

**Expected Results:**
- HTTP `200 OK`
- Itinerary detail page renders with all relevant information

---

### TC-INT-045: Download Itinerary PDF

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-045 |
| **Description** | Verify that a generated itinerary can be downloaded as a PDF |
| **Preconditions** | Authenticated as wisatawan, `history_id` obtained from TC-INT-043 |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /itinerary/download/{history_id}` | Response 200 |
| 2 | Inspect response headers | `Content-Type: application/pdf` |

**Expected Results:**
- HTTP `200 OK`
- `Content-Type: application/pdf`
- PDF file downloaded successfully

---

### TC-INT-046: View Itinerary History

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-046 |
| **Description** | Verify that a wisatawan can view their list of past generated itineraries |
| **Preconditions** | Authenticated as wisatawan, at least one itinerary has been generated |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /itinerary/history` | Response 200 |
| 2 | Inspect response body | List of past itineraries displayed |

**Expected Results:**
- HTTP `200 OK`
- History list shows previously generated itineraries

---

### TC-INT-047: Delete Itinerary History

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-047 |
| **Description** | Verify that a wisatawan can delete a specific itinerary from their history |
| **Preconditions** | Authenticated as wisatawan, `history_id` obtained from TC-INT-043 |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `DELETE /itinerary/history/{history_id}` | Success response |
| 2 | Query database | Record removed from itinerary history table |

**Expected Results:**
- Itinerary history record deleted from the database
- Record no longer appears in history list

---

### TC-INT-048: Generate Itinerary - Unauthenticated

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-048 |
| **Description** | Verify that unauthenticated users cannot generate an itinerary |
| **Preconditions** | No active session (not logged in) |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /itinerary/generate` without authentication | Redirect to login |

**Expected Results:**
- HTTP `302` redirect to `/login`
- Itinerary not generated

---

### TC-INT-049: Generate Itinerary - Invalid Budget

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-049 |
| **Description** | Verify that itinerary generation fails with a negative budget value |
| **Preconditions** | Authenticated as wisatawan |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /itinerary/generate` with `budget=-1000` | Validation error returned |

**Expected Results:**
- HTTP `422 Unprocessable Entity`
- Validation error on the `budget` field

**Negative Test Cases:**
- `budget=0` — should fail or return empty results
- `budget="abc"` (non-numeric) — should fail with validation error
- Missing required fields (e.g., no `kategori_ids`) — should fail with validation error

---

## SCENARIO 10: AI Chatbot Interaction (Waybot)

### TC-INT-050: First Waybot Message (New Session)

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-050 |
| **Description** | Verify that a wisatawan can start a new Waybot chat session and receive an AI response |
| **Preconditions** | Authenticated as wisatawan |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /waybot/chat` with: `message="Halo, rekomendasikan destinasi wisata di Batam"`, `session_token=null`, `gps_lat=1.1296758`, `gps_lng=104.0452254` | Response 200 JSON |
| 2 | Inspect JSON response | `response.success = true` |
| 3 | Inspect JSON response | `response.message` is a non-empty string |
| 4 | Inspect JSON response | `response.session_token` is returned |
| 5 | Save `session_token` for subsequent steps | — |

**Expected Results:**
- HTTP `200 OK` with JSON body
- `success: true`
- Non-empty AI response message
- `session_token` returned for conversation continuity

---

### TC-INT-051: Continue Waybot Conversation

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-051 |
| **Description** | Verify that a follow-up message in the same session maintains conversation context |
| **Preconditions** | Authenticated as wisatawan, `session_token` obtained from TC-INT-050 |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /waybot/chat` with: `message="Berapa harga masuknya?"`, `session_token="{saved_token}"` | Response 200 JSON |
| 2 | Inspect JSON response | `response.session_token` matches the saved token |

**Expected Results:**
- HTTP `200 OK`
- Same `session_token` returned (session maintained)
- Response is contextually relevant to the previous message

---

### TC-INT-052: Waybot with GPS Location

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-052 |
| **Description** | Verify that Waybot uses GPS coordinates to provide location-aware recommendations |
| **Preconditions** | Authenticated as wisatawan, destinations with coordinates near Batam exist |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /waybot/chat` with a message and GPS coordinates (`gps_lat`, `gps_lng`) | Response 200 JSON |
| 2 | Inspect response | Nearby destinations referenced in the response |

**Expected Results:**
- HTTP `200 OK`
- Response includes location-aware destination recommendations

---

### TC-INT-053: Waybot Without GPS

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-053 |
| **Description** | Verify that Waybot still functions correctly when GPS coordinates are not provided |
| **Preconditions** | Authenticated as wisatawan |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /waybot/chat` with a message but without `gps_lat`/`gps_lng` | Response 200 JSON |

**Expected Results:**
- HTTP `200 OK`
- General recommendations returned without location-specific filtering
- No error due to missing GPS data

---

### TC-INT-054: Waybot History Retrieval

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-054 |
| **Description** | Verify that the chat history for a session can be retrieved |
| **Preconditions** | Authenticated as wisatawan, `session_token` with prior messages exists |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /waybot/history?session_token={token}` | Response 200 JSON |
| 2 | Inspect JSON response | `response.messages` is an array |
| 3 | Inspect messages array | Contains messages from the previous conversation |

**Expected Results:**
- HTTP `200 OK`
- `messages` array contains the conversation history in order

---

### TC-INT-055: Waybot Reset Session

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-055 |
| **Description** | Verify that a Waybot session can be reset, clearing all conversation history |
| **Preconditions** | Authenticated as wisatawan, active `session_token` with messages exists |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /waybot/reset` with `session_token` | Response 200 JSON |
| 2 | Inspect JSON response | `response.success = true` |
| 3 | `GET /waybot/history?session_token={token}` | Response 200 JSON with empty messages |

**Expected Results:**
- HTTP `200 OK` with `success: true`
- Session history cleared
- Subsequent history retrieval returns empty `messages` array

---

### TC-INT-056: Waybot Empty Message

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-056 |
| **Description** | Verify that sending an empty message to Waybot returns an appropriate error |
| **Preconditions** | Authenticated as wisatawan |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /waybot/chat` with `message=""` | Error response returned |

**Expected Results:**
- Error response (validation error or `success: false`)
- Descriptive error message about the empty input

**Negative Test Cases:**
- `message` field missing entirely — should return validation error
- `message` containing only whitespace — should return validation error

---

## SCENARIO 11: Review and Rating Features

### TC-INT-057: Wisatawan Submit Review

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-057 |
| **Description** | Verify that a wisatawan can submit a review and rating for a destination |
| **Preconditions** | Authenticated as wisatawan, destination with `id=1` exists |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /ulasan` with: `destinasi_id=1`, `rating=5`, `komentar="Tempat yang sangat indah dan bersih!"` | Request accepted |
| 2 | Follow redirect | Success message displayed |
| 3 | Query `ulasan` table | New record exists with `rating=5` and correct `komentar` |

**Expected Results:**
- Review record created in the `ulasan` table
- `rating=5` and `komentar` saved correctly
- Success message displayed to the user

---

### TC-INT-058: Submit Review - Invalid Rating

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-058 |
| **Description** | Verify that submitting a review with an out-of-range rating value fails validation |
| **Preconditions** | Authenticated as wisatawan |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /ulasan` with `rating=6` (valid range is 1–5) | Validation error returned |

**Expected Results:**
- HTTP `422 Unprocessable Entity`
- Validation error on the `rating` field

**Negative Test Cases:**
- `rating=0` — should fail (below minimum)
- `rating=-1` — should fail
- `rating="abc"` (non-numeric) — should fail

---

### TC-INT-059: Submit Review - Missing Required Fields

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-059 |
| **Description** | Verify that submitting a review without the required `rating` field fails validation |
| **Preconditions** | Authenticated as wisatawan |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /ulasan` with only `destinasi_id=1` (no `rating`) | Validation error returned |

**Expected Results:**
- HTTP `422 Unprocessable Entity`
- Validation error on the `rating` field indicating it is required

---

### TC-INT-060: Pemilik View Reviews for Their Destinasi

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-060 |
| **Description** | Verify that a pemilik can view reviews, and only sees reviews for their own destinations |
| **Preconditions** | Authenticated as pemilik_wisata, reviews exist for their destination(s) |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /ulasan` | Response 200 |
| 2 | Inspect response body | Only reviews for the pemilik's own destinations are shown |

**Expected Results:**
- HTTP `200 OK`
- Reviews filtered to only show those belonging to the authenticated pemilik's destinations

---

### TC-INT-061: Review Appears on Destinasi Detail Page

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-061 |
| **Description** | Verify that a submitted review is publicly visible on the destination detail page |
| **Preconditions** | A review has been submitted for destination with known ID (from TC-INT-057) |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `GET /destinasi/{id}` | Response 200 |
| 2 | Inspect response body | Review section shows the submitted review |
| 3 | Inspect review section | Rating (5 stars) and comment text displayed |

**Expected Results:**
- HTTP `200 OK`
- Submitted review visible in the reviews section
- Rating and comment rendered correctly

---

### TC-INT-062: Unauthenticated Review Submission

| Field | Details |
|-------|---------|
| **Test Case ID** | TC-INT-062 |
| **Description** | Verify that unauthenticated users cannot submit a review |
| **Preconditions** | No active session (not logged in) |

**Test Steps:**

| Step | Action | Expected Result |
|------|--------|-----------------|
| 1 | `POST /ulasan` without authentication | Redirect to login |

**Expected Results:**
- HTTP `302` redirect to `/login`
- Review not created in the database

---

## Test Data Requirements

### Seed Data Needed

```
Users:
- admin@wayway.com        (role: admin)
- pemilik@test.com        (role: pemilik_wisata, Basic package)
- pemilik_standard@test.com (role: pemilik_wisata, Standard package)
- wisatawan@test.com      (role: wisatawan)
- agent@test.com          (role: travel_agent)

Kategori:
- Pantai
- Gunung
- Budaya
- Kuliner
- Hiburan

Destinasi:
- At least 5 active destinations with coordinates in the Batam area
- At least 1 destination named with "pantai" for search tests

PaketPromosi:
- Basic    (free,    max 1 destination,  max 3 photos,  no direct edit)
- Standard (Rp 49.000, max 3 destinations, max 8 photos,  direct edit enabled)
- Premium  (Rp 149.000, unlimited destinations, max 20 photos, all features)
```

---

## Test Execution Order

The recommended execution order ensures that dependencies between test cases are satisfied:

| Order | Scenario | Test Cases |
|-------|----------|------------|
| 1 | Authentication | TC-INT-006 to TC-INT-012 |
| 2 | Registration | TC-INT-001 to TC-INT-005 |
| 3 | Profile Management | TC-INT-013 to TC-INT-017 |
| 4 | Destination Management | TC-INT-018 to TC-INT-023 |
| 5 | Search & Discovery | TC-INT-029 to TC-INT-034 |
| 6 | Package Purchase | TC-INT-035 to TC-INT-039 |
| 7 | Package Activation | TC-INT-040 to TC-INT-042 |
| 8 | Travel Agent Management | TC-INT-024 to TC-INT-028 |
| 9 | Itinerary Management | TC-INT-043 to TC-INT-049 |
| 10 | AI Chatbot (Waybot) | TC-INT-050 to TC-INT-056 |
| 11 | Review & Rating | TC-INT-057 to TC-INT-062 |

---

## Summary

| Scenario | Test Cases | Positive | Negative |
|----------|-----------|----------|----------|
| 1. User Registration | TC-INT-001 to TC-INT-005 | 1 | 4 |
| 2. User Login | TC-INT-006 to TC-INT-012 | 5 | 2 |
| 3. Profile Management | TC-INT-013 to TC-INT-017 | 4 | 1 |
| 4. Destination Management | TC-INT-018 to TC-INT-023 | 4 | 2 |
| 5. Travel Agent Management | TC-INT-024 to TC-INT-028 | 3 | 2 |
| 6. Search & Discovery | TC-INT-029 to TC-INT-034 | 5 | 1 |
| 7. Package Promotion Purchase | TC-INT-035 to TC-INT-039 | 3 | 2 |
| 8. Package Activation | TC-INT-040 to TC-INT-042 | 3 | 0 |
| 9. Itinerary Management | TC-INT-043 to TC-INT-049 | 4 | 3 |
| 10. AI Chatbot (Waybot) | TC-INT-050 to TC-INT-056 | 5 | 2 |
| 11. Review & Rating | TC-INT-057 to TC-INT-062 | 3 | 3 |
| **Total** | **62 test cases** | **40** | **22** |
