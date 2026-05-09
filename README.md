# KaziTrust 🛡️

![alt text](<Capture d’écran 2026-05-09 à 18.32.12.png>)

**The next-generation anti-fraud shield for African Microfinance Institutions (MFIs) and SMEs.**

KaziTrust is a multi-tenant B2B SaaS platform that secures financial inclusion by combining the reliability of telecom network APIs (Nokia Open Gateway / CAMARA) and the power of Artificial Intelligence (behavioral analysis).

---

## 🚀 Key Features

* **Multi-Tenant SaaS Architecture:** Each SME or MFI has its own totally isolated workspace (Tenant) to manage its applications, API keys, and analysis logs.
* **Bring Your Own AI (BYO-AI):** Total flexibility. Tenants can connect their own AI API keys. Keys are encrypted in the database (AES-256).
* **Silent Network Verification:** Integration with Nokia Network-as-Code to detect SIM card fraud (SIM Swap, Number Verification, Device Status) before the transaction even occurs.
* **Real-Time Scoring:** AI analyzes the context and network payload to return an immediate decision (`Approve`, `Reject`, `Manual Review`) accompanied by a detailed reasoning.

![alt text](<Capture d’écran 2026-05-09 à 18.17.23.png>) ![alt text](<Capture d’écran 2026-05-09 à 18.17.13.png>)

![alt text](<Capture d’écran 2026-05-09 à 18.15.03.png>) ![alt text](<Capture d’écran 2026-05-09 à 18.15.36.png>)


## 🧠 Supported Artificial Intelligence Models

KaziTrust is AI-agnostic and natively supports a wide range of LLM providers to fit every company's budget and needs:

* **Google Gemini**
* **Anthropic Claude**
* **ChatGPT (OpenAI)**
* **Mistral AI**
* **Groq** (For ultra-fast inference)
* **Cerebras**
* **OpenRouter**


## 📚 API Documentation (Multi-language)

Our API documentation has been entirely generated using **Scribe**. It is interactive, available in multiple languages, and allows developers to test endpoints directly from the browser.

![alt text](<Capture d’écran 2026-05-09 à 18.33.02.png>) ![alt text](<Capture d’écran 2026-05-09 à 18.32.51.png>)

👉 **http://kazitrust.digitalconceptcenter.com/docs/en**

## 🧪 Test Data (Nokia Sandbox Test Matrix)

To test the integration of Nokia Open Gateway APIs and observe the AI's behavior against different fraud scenarios, please use the following phone numbers during your API calls:

```
<div class="nk-row nk-v-red">
  <div class="nk-v-badge">🔴 Reject (Score 2)</div>
  <div class="nk-v-phone">+99999901000</div>
  <div class="nk-v-desc">Recent SIM Swap detected (&lt; 24h)</div>
</div>

<div class="nk-row nk-v-red">
  <div class="nk-v-badge">🔴 Reject (Score 2)</div>
  <div class="nk-v-phone">+99999992000</div>
  <div class="nk-v-desc">Inactive or Ported phone number</div>
</div>

<div class="nk-row nk-v-orange">
  <div class="nk-v-badge">🟠 Manual Review</div>
  <div class="nk-v-phone">+99999991000</div>
  <div class="nk-v-desc">International Roaming (Hungary / Non-ECOWAS)</div>
</div>

<div class="nk-row nk-v-green">
  <div class="nk-v-badge">🟢 Approved</div>
  <div class="nk-v-phone">+99999991001</div>
  <div class="nk-v-desc">Clean history (Stable device signals)</div>
</div>

```

* **Framework:** Laravel (PHP)
* **Admin Panel:** FilamentPHP (Multi-tenant SaaS)
* **Database:** MySQL
* **Docs Generator:** Scribe
* **Styling:** Tailwind CSS

## ⚙️ Local Installation

1. Clone the repository:

```bash
git clone https://github.com/Anicet7/KaziTrust.git
cd kazitrust

```

2. Install dependencies:

```bash
composer install
npm install && npm run build

```

3. Configure the environment:

```bash
cp .env.example .env
php artisan key:generate

```

*Configure your database credentials in the `.env` file.*

4. Run migrations and seeding:

```bash
php artisan migrate --seed

```

5. Start the development server:

```bash
php artisan serve

```

---

*This project was designed to address financial inclusion and security challenges in Benin and across the African continent.*