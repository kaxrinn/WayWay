# WayWay Tourism Platform - System Testing Scenarios

## Overview
System testing validates the complete end-to-end workflows of the WayWay platform from a user perspective. These tests simulate real user journeys across all roles.

**Test Environment:** Local (http://127.0.0.1:8000)
**Browser:** Chrome/Firefox (latest)
**Test Type:** Manual + Automated (Postman Collection Runner)

---

## WORKFLOW 1: Tourist (Wisatawan) Complete Journey

### SYS-001: New Tourist Registration to First Booking

**Objective:** Verify complete tourist onboarding flow

**Preconditions:**
- Application running
- At least 5 active destinations seeded
- At least 3 categories seeded

**Steps:**

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Navigate to http://127.0.0.1:8000 | Homepage loads with navigation | |
| 2 | Click "Daftar" / Register button | Registration form displayed | |
| 3 | Fill: name="Siti Rahayu", email="siti@test.com", password="Password123" | Form accepts input | |
| 4 | Submit registration form | Redirect to /wisatawan/beranda | |
| 5 | Verify beranda shows destinations | Destination cards visible | |
| 6 | Search for "pantai" in search bar | Filtered results shown | |
| 7 | Click on a destination card | Destination detail page loads | |
| 8 | Verify photos, description, price shown | All details visible | |
| 9 | Click "Tambah Favorit" button | Destination added to favorites | |
| 10 | Navigate to /wisatawan/favorit | Favorited destination shown | |
| 11 | Submit a review: rating=5, comment="Indah!" | Review submitted successfully | |
| 12 | Verify review appears on destination page | Review visible | |
| 13 | Navigate to /itinerary | Itinerary planner loads | |
| 14 | Generate itinerary with budget=100000 | Itinerary generated | |
| 15 | Download itinerary PDF | PDF downloaded | |
| 16 | Logout | Redirected to homepage | |

**Expected Overall Result:** Tourist can complete full discovery and planning journey

---

### SYS-002: Tourist Uses Waybot AI Assistant

**Objective:** Verify AI chatbot provides relevant recommendations

**Steps:**

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Login as wisatawan | Redirected to beranda | |
| 2 | Navigate to Waybot chat | Chat interface loads | |
| 3 | Send: "Halo, saya ingin wisata ke pantai di Batam" | AI responds with greeting + recommendations | |
| 4 | Verify response mentions destinations | At least 1 destination mentioned | |
| 5 | Send: "Berapa harga masuknya?" | AI provides price information | |
| 6 | Send: "Rekomendasikan yang dekat dari Batam Center" | Location-aware response | |
| 7 | Enable GPS location | Location shared with chatbot | |
| 8 | Send: "Destinasi apa yang paling dekat?" | Nearest destinations listed | |
| 9 | Click "Reset Chat" | Conversation cleared | |
| 10 | Verify new session starts | Fresh conversation | |

---

## WORKFLOW 2: Tourism Owner (Pemilik Wisata) Complete Journey

### SYS-003: Pemilik Registration to Destination Management

**Objective:** Verify complete pemilik workflow from registration to managing destinations

**Preconditions:**
- Admin account exists
- Basic, Standard, Premium packages seeded

**Steps:**

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Register as pemilik_wisata | Account created | |
| 2 | Login → redirect to /pemilik/dashboard | Dashboard loads | |
| 3 | Navigate to /pemilik/destinasi | Empty destination list | |
| 4 | Click "Tambah Destinasi" | Create form loads | |
| 5 | Fill destination form with valid data | Form accepts input | |
| 6 | Upload 3 photos (max for Basic) | Photos uploaded | |
| 7 | Submit form | Destination created, redirect to list | |
| 8 | Verify destination appears in list | Destination visible | |
| 9 | Try to create 2nd destination (Basic limit=1) | Error: limit reached | |
| 10 | Navigate to /pemilik/paket | Package list shows Basic/Standard/Premium | |
| 11 | Click "Upgrade" on Standard package | Checkout page/Midtrans loads | |
| 12 | Complete payment (sandbox) | Payment processed | |
| 13 | Verify package upgraded to Standard | current_paket = Standard | |
| 14 | Create 2nd destination (now allowed) | Success | |
| 15 | Edit destination directly (Standard allows) | Edit form loads | |
| 16 | Update destination info | Changes saved | |
| 17 | Navigate to /pemilik/promosi | Promosi list loads | |
| 18 | Create a promotion for destination | Promotion created | |
| 19 | View reviews for destination | Reviews displayed | |
| 20 | Logout | Session ended | |

---

### SYS-004: Pemilik Edit Request Flow (Basic Package)

**Objective:** Verify Basic package pemilik must use edit request system

**Steps:**

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Login as pemilik with Basic package | Dashboard loads | |
| 2 | Navigate to destination edit page | Redirected to edit-request form | |
| 3 | Fill edit request: reason="Ingin update foto" | Form accepts input | |
| 4 | Submit edit request | Request submitted, pending admin review | |
| 5 | Login as admin | Admin dashboard | |
| 6 | Navigate to /admin/edit-requests | Edit request visible | |
| 7 | Approve the edit request | Request approved | |
| 8 | Login as pemilik again | Dashboard loads | |
| 9 | Navigate to destination edit (within 7 days) | Edit form loads (approved) | |
| 10 | Update destination | Changes saved | |

---

## WORKFLOW 3: Travel Agent Complete Journey

### SYS-005: Travel Agent Registration to Package Management

**Objective:** Verify complete travel agent workflow

**Steps:**

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Register as travel_agent | Account created | |
| 2 | Login → redirect to /travel-agent/dashboard | Dashboard loads | |
| 3 | Navigate to /travel-agent/packages | Empty package list | |
| 4 | Click "Buat Paket Baru" | Create form loads | |
| 5 | Fill: nama="Paket Batam 3D2N", harga=500000, durasi=3 | Form accepts input | |
| 6 | Set departure date (future) | Date accepted | |
| 7 | Add destinations, meeting point, contact | All fields filled | |
| 8 | Submit form | Package created | |
| 9 | Verify package in list | Package visible | |
| 10 | Try to create 2nd package (Basic limit=1) | Error: limit reached | |
| 11 | Navigate to /travel-agent/subscriptions | Subscription page loads | |
| 12 | Click "Upgrade" | Upgrade options shown | |
| 13 | Select higher tier subscription | Checkout initiated | |
| 14 | Complete payment | Subscription upgraded | |
| 15 | Create 2nd package (now allowed) | Success | |
| 16 | Edit existing package | Changes saved | |
| 17 | Delete a package | Package removed | |
| 18 | View package detail | Detail page loads | |

---

## WORKFLOW 4: Promotion Package Purchase Workflow

### SYS-006: Complete Midtrans Payment Flow

**Objective:** Verify end-to-end payment processing for promotion packages

**Steps:**

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Login as pemilik (Basic package) | Dashboard loads | |
| 2 | Navigate to /pemilik/paket | Package list shows 3 options | |
| 3 | Verify Basic=Rp0, Standard=Rp49.000, Premium=Rp149.000 | Prices correct | |
| 4 | Click "Beli" on Standard package | Checkout initiated | |
| 5 | Verify TransaksiPromosi created (status=pending) | DB record exists | |
| 6 | Midtrans Snap payment modal appears | Payment UI shown | |
| 7 | Complete payment with test card: 4811111111111114 | Payment processed | |
| 8 | Midtrans sends webhook to /api/midtrans/notification | Webhook received | |
| 9 | Verify signature validation passes | No 403 error | |
| 10 | Verify TransaksiPromosi status=paid | DB updated | |
| 11 | Verify user current_paket_id=Standard | Package activated | |
| 12 | Verify new limits applied (max_destinasi=3) | Limits updated | |
| 13 | Test expired payment scenario | Status=expired | |
| 14 | Test failed payment scenario | Status=failed | |

---

## WORKFLOW 5: AI Chatbot (Waybot) Complete Workflow

### SYS-007: Waybot Full Conversation Flow

**Objective:** Verify Waybot handles complete conversation scenarios

**Steps:**

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Login as wisatawan | Authenticated | |
| 2 | Open Waybot chat | Chat interface loads | |
| 3 | Send greeting message | AI responds appropriately | |
| 4 | Ask for destination recommendations | Relevant destinations listed | |
| 5 | Ask follow-up about specific destination | Contextual response | |
| 6 | Ask about prices | Price information provided | |
| 7 | Ask about opening hours | Hours information (if available) | |
| 8 | Enable GPS and ask for nearby destinations | Location-filtered results | |
| 9 | Ask Waybot to generate itinerary | Itinerary suggestion provided | |
| 10 | View chat history | All messages preserved | |
| 11 | Reset chat session | History cleared | |
| 12 | Start new conversation | Fresh session | |
| 13 | Test with very long message (>500 chars) | Handled gracefully | |
| 14 | Test with special characters | No errors | |
| 15 | Test rapid consecutive messages | All processed | |

---

## WORKFLOW 6: Itinerary Generation Workflow

### SYS-008: Complete Itinerary Planning Flow

**Objective:** Verify AI itinerary generation pipeline works end-to-end

**Steps:**

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Login as wisatawan | Authenticated | |
| 2 | Navigate to /itinerary | Planner form loads | |
| 3 | Select categories: Pantai, Budaya | Categories selected | |
| 4 | Set budget: Rp 200.000 | Budget set | |
| 5 | Set companion: keluarga | Companion type set | |
| 6 | Set date: 2026-12-25 | Date set | |
| 7 | Set origin: Batam Center (lat/lng) | Origin set | |
| 8 | Set max destinations: 4 | Limit set | |
| 9 | Set available hours: 8 | Hours set | |
| 10 | Click "Generate Itinerary" | Loading indicator shown | |
| 11 | Wait for AI processing | Response within 30 seconds | |
| 12 | Verify itinerary generated | Route with destinations shown | |
| 13 | Verify destinations within budget | All harga ≤ budget | |
| 14 | Verify route is optimized (nearest first) | Logical route order | |
| 15 | Verify time slots assigned | Each destination has time | |
| 16 | Click "Lihat Detail" | Detail page loads | |
| 17 | Click "Download PDF" | PDF downloaded | |
| 18 | Verify PDF contains itinerary | PDF readable | |
| 19 | Navigate to /itinerary/history | History shows this itinerary | |
| 20 | Delete itinerary from history | Removed from list | |

---

## WORKFLOW 7: Admin Management Workflow

### SYS-009: Admin Complete Management Flow

**Objective:** Verify admin can manage all platform entities

**Steps:**

| Step | Action | Expected Result | Pass/Fail |
|------|--------|-----------------|-----------|
| 1 | Login as admin | Redirect to /admin/dashboard | |
| 2 | View dashboard statistics | Stats displayed | |
| 3 | Navigate to /admin/wisatawan | User list loads | |
| 4 | Navigate to /admin/pemilik | Pemilik list loads | |
| 5 | Navigate to /admin/destinasi | All destinations listed | |
| 6 | Navigate to /admin/kategori | Categories listed | |
| 7 | Create new kategori: "Kuliner" | Category created | |
| 8 | Update kategori name | Name updated | |
| 9 | Navigate to /admin/transaksi | Transactions listed | |
| 10 | Approve a pending transaction | Status=paid, package activated | |
| 11 | Reject a pending transaction | Status=rejected | |
| 12 | Navigate to /admin/edit-requests | Edit requests listed | |
| 13 | Approve an edit request | Request approved | |
| 14 | Navigate to /admin/bantuan | Contact messages listed | |
| 15 | Navigate to /admin/travel-agents | Travel agents listed | |
| 16 | Navigate to /admin/travel-subscriptions | Subscriptions listed | |
| 17 | Logout | Session ended | |

---

## Performance Testing Scenarios

### SYS-010: Load Testing Key Endpoints

| Endpoint | Concurrent Users | Max Response Time | Pass Criteria |
|----------|-----------------|-------------------|---------------|
| GET /destinasi | 50 | 3000ms | 95% < 3s |
| GET /destinasi/{id} | 100 | 2000ms | 95% < 2s |
| POST /waybot/chat | 20 | 10000ms | 95% < 10s |
| POST /itinerary/generate | 10 | 30000ms | 95% < 30s |
| GET /wisatawan/beranda | 100 | 3000ms | 95% < 3s |

---

## Security Testing Scenarios

### SYS-011: Security Validation

| Test | Description | Expected Result |
|------|-------------|-----------------|
| CSRF Protection | Submit form without CSRF token | 419 Token Mismatch |
| SQL Injection | Input: `'; DROP TABLE users; --` | Input sanitized, no DB error |
| XSS Prevention | Input: `<script>alert('xss')</script>` | Script escaped in output |
| Auth Bypass | Access /admin without login | Redirect to /login |
| Role Escalation | Wisatawan access /admin/dashboard | 403 Forbidden |
| IDOR | Access other user's itinerary | 403/404 |
| Midtrans Signature | Invalid webhook signature | 403 Invalid signature |
| File Upload | Upload PHP file as image | Validation error |
| Mass Assignment | POST extra fields to user update | Extra fields ignored |

---

## Browser Compatibility Testing

| Browser | Version | Registration | Login | Destinasi | Waybot | Itinerary |
|---------|---------|-------------|-------|-----------|--------|-----------|
| Chrome | Latest | | | | | |
| Firefox | Latest | | | | | |
| Edge | Latest | | | | | |
| Safari | Latest | | | | | |
| Chrome Mobile | Latest | | | | | |

---

## Test Execution Checklist

- [ ] All seed data loaded
- [ ] Application running on port 8000
- [ ] Midtrans sandbox configured
- [ ] AI/Waybot service accessible
- [ ] OSRM service accessible (or mocked)
- [ ] Email service configured (or mocked)
- [ ] Storage permissions set correctly
- [ ] All test accounts created
