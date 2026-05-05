# Introduction

**KaziTrust** is a B2B SaaS Trust Hub that abstracts the complexity of Nokia CAMARA
network APIs behind a unified, AI-powered interface.
Integrate telecom-grade fraud detection and identity verification
into your application in just a few lines of code — no direct operator integration required.

<aside>
    <strong>Base URL</strong>: <code>http://127.0.0.1:8000</code>
</aside>

<p style="text-align:right">
    🌐 <a href="/docs"><strong>Lire en Français</strong></a>
</p>

## Welcome to the KaziTrust API Documentation

KaziTrust democratizes access to advanced telecom network capabilities across Sub-Saharan Africa.
Our platform simultaneously orchestrates multiple Nokia CAMARA signals — **SIM Swap**,
**Number Verification**, **KYC Match**, and **Location Verification** — and returns
a **clear, actionable business decision** in real time.

### What you can do with this API

- 🔐 **Anti-Fraud & SIM Swap Detection**: Detect suspicious SIM card swaps before approving
  a transaction or loan. Receive a trust score and a precise rejection reason.
- 📍 **Location Verification**: Silently confirm a user's geographic consistency
  via the network — no GPS, no intrusive consent required.
- 🪪 **KYC & Identity Matching**: Verify that a phone number matches
  the identity declared by the user (name, date of birth, etc.).
- 📲 **Number Verification (Silent Auth)**: Replace costly, vulnerable SMS OTPs
  with silent phone number verification via the operator's network.
- 📊 **Global Trust Score**: Our AI agent analyzes all these signals in parallel
  and returns a unified decision (e.g., *"Score 85% — Approved"* or *"Rejected — SIM Swap detected 2h ago"*).

### Typical Use Cases

| Sector | Use Case |
|---|---|
| Micro-lending / MFIs | Instant loan approval with SIM + location verification |
| E-commerce & Payments | Automatic blocking of high-risk transactions |
| Digital Onboarding | Replacing SMS OTPs with silent authentication |
| Mobile Money | Protecting transfers against identity theft |

### Authentication

All requests must include your KaziTrust API key in the `Authorization` header:

```
Authorization: Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

Generate or rotate your keys from your **Tenant Dashboard → My API Services → API Keys**.

<aside class="notice">
Each tenant has isolated API keys. Never share your <code>kz_</code> key.
If compromised, revoke it immediately from your dashboard.
</aside>

<aside class="success">
You can test every endpoint directly on this page using the
<strong>Try it out</strong> button. Make sure your API key is set at the top of the page.
</aside>

