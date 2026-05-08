<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>KaziTrust — API Reference</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe_en/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe_en/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
                    body .content .php-example code { display: none; }
                    body .content .python-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://127.0.0.1:8000";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe_en/js/tryitout-5.9.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe_en/js/theme-default-5.9.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;,&quot;php&quot;,&quot;python&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe_en/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
            <img src="/img/kazitrust-logo.png" alt="logo" class="logo" style="padding-top: 10px;" width="100%"/>
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                                            <button type="button" class="lang-button" data-language-name="php">php</button>
                                            <button type="button" class="lang-button" data-language-name="python">python</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                                    <ul id="tocify-subheader-introduction" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="welcome-to-the-kazitrust-api-documentation">
                                <a href="#welcome-to-the-kazitrust-api-documentation">Welcome to the KaziTrust API Documentation</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-analyse-de-confiance" class="tocify-header">
                <li class="tocify-item level-1" data-unique="analyse-de-confiance">
                    <a href="#analyse-de-confiance">Analyse de confiance</a>
                </li>
                                    <ul id="tocify-subheader-analyse-de-confiance" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="analyse-de-confiance-POSTapi-v1-trust-analyze">
                                <a href="#analyse-de-confiance-POSTapi-v1-trust-analyze">Analyser un numéro</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="analyse-de-confiance-GETapi-v1-trust-logs">
                                <a href="#analyse-de-confiance-GETapi-v1-trust-logs">Historique des analyses</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="analyse-de-confiance-GETapi-v1-trust-logs--requestId-">
                                <a href="#analyse-de-confiance-GETapi-v1-trust-logs--requestId-">Détail d'une analyse</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="analyse-de-confiance-GETapi-v1-trust-quota">
                                <a href="#analyse-de-confiance-GETapi-v1-trust-quota">Quota de l'application</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-statut" class="tocify-header">
                <li class="tocify-item level-1" data-unique="statut">
                    <a href="#statut">Statut</a>
                </li>
                                    <ul id="tocify-subheader-statut" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="statut-GETapi-v1-status">
                                <a href="#statut-GETapi-v1-status">Statut de l'API</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe_en.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe_en.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: May 7, 2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<p><strong>KaziTrust</strong> is a B2B SaaS Trust Hub that abstracts the complexity of Nokia CAMARA
network APIs behind a unified, AI-powered interface.
Integrate telecom-grade fraud detection and identity verification
into your application in just a few lines of code — no direct operator integration required.</p>
<aside>
    <strong>Base URL</strong>: <code>http://127.0.0.1:8000</code>
</aside>
<p style="text-align:right">
    🌐 <a href="/docs"><strong>Lire en Français</strong></a>
</p>
<h2 id="welcome-to-the-kazitrust-api-documentation">Welcome to the KaziTrust API Documentation</h2>
<p>KaziTrust democratizes access to advanced telecom network capabilities across Sub-Saharan Africa.
Our platform simultaneously orchestrates multiple Nokia CAMARA signals — <strong>SIM Swap</strong>,
<strong>Number Verification</strong>, <strong>KYC Match</strong>, and <strong>Location Verification</strong> — and returns
a <strong>clear, actionable business decision</strong> in real time.</p>
<h3 id="what-you-can-do-with-this-api">What you can do with this API</h3>
<ul>
<li>🔐 <strong>Anti-Fraud &amp; SIM Swap Detection</strong>: Detect suspicious SIM card swaps before approving
a transaction or loan. Receive a trust score and a precise rejection reason.</li>
<li>📍 <strong>Location Verification</strong>: Silently confirm a user's geographic consistency
via the network — no GPS, no intrusive consent required.</li>
<li>🪪 <strong>KYC &amp; Identity Matching</strong>: Verify that a phone number matches
the identity declared by the user (name, date of birth, etc.).</li>
<li>📲 <strong>Number Verification (Silent Auth)</strong>: Replace costly, vulnerable SMS OTPs
with silent phone number verification via the operator's network.</li>
<li>📊 <strong>Global Trust Score</strong>: Our AI agent analyzes all these signals in parallel
and returns a unified decision (e.g., <em>"Score 85% — Approved"</em> or <em>"Rejected — SIM Swap detected 2h ago"</em>).</li>
</ul>
<h3 id="typical-use-cases">Typical Use Cases</h3>
<table>
<thead>
<tr>
<th>Sector</th>
<th>Use Case</th>
</tr>
</thead>
<tbody>
<tr>
<td>Micro-lending / MFIs</td>
<td>Instant loan approval with SIM + location verification</td>
</tr>
<tr>
<td>E-commerce &amp; Payments</td>
<td>Automatic blocking of high-risk transactions</td>
</tr>
<tr>
<td>Digital Onboarding</td>
<td>Replacing SMS OTPs with silent authentication</td>
</tr>
<tr>
<td>Mobile Money</td>
<td>Protecting transfers against identity theft</td>
</tr>
</tbody>
</table>
<h3 id="authentication">Authentication</h3>
<p>All requests must include your KaziTrust API key in the <code>Authorization</code> header:</p>
<pre><code>Authorization: Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</code></pre>
<p>Generate or rotate your keys from your <strong>Tenant Dashboard → My API Services → API Keys</strong>.</p>
<aside class="notice">
Each tenant has isolated API keys. Never share your <code>kz_</code> key.
If compromised, revoke it immediately from your dashboard.
</aside>
<aside class="success">
You can test every endpoint directly on this page using the
<strong>Try it out</strong> button. Make sure your API key is set at the top of the page.
</aside>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>To authenticate requests, include an <strong><code>Authorization</code></strong> header with the value <strong><code>"Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"</code></strong>.</p>
<p>All authenticated endpoints are marked with a <code>requires authentication</code> badge in the documentation below.</p>
<p>Get your API key from the <strong>Tenant Dashboard → Management → My API Services → API Keys</strong>.</p>
<p>All KaziTrust keys are prefixed with <code>kz_</code> followed by 32 alphanumeric characters.
Each key is tied to an isolated tenant (your company) and only grants access to your data.</p>
<p><strong>Available environments:</strong>
| Environment | Key prefix | Base URL |
|---|---|---|
| Sandbox | <code>kz_test_</code> | <code>https://sandbox.kazitrust.io</code> |
| Production | <code>kz_live_</code> | <code>https://api.kazitrust.io</code> |</p>

        <h1 id="analyse-de-confiance">Analyse de confiance</h1>

    <p>Analysez la fiabilité d'un numéro de téléphone mobile via les signaux réseau
Nokia CAMARA et l'intelligence artificielle configurée sur votre application.</p>

                                <h2 id="analyse-de-confiance-POSTapi-v1-trust-analyze">Analyser un numéro</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Lance une analyse complète d'un numéro de téléphone :
collecte des signaux réseau Nokia CAMARA (SIM Swap, localisation, statut réseau),
puis analyse par le moteur IA configuré sur votre application.</p>

<span id="example-requests-POSTapi-v1-trust-analyze">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://127.0.0.1:8000/api/v1/trust/analyze" \
    --header "Authorization: Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --header "X-KaziTrust-Version: v1" \
    --data "{
    \"phone_number\": \"+22961000000\",
    \"context\": {
        \"transaction_amount\": 150000,
        \"transaction_currency\": \"XOF\",
        \"ip_address\": \"197.234.10.1\",
        \"user_agent\": \"Mozilla\\/5.0...\"
    }
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://127.0.0.1:8000/api/v1/trust/analyze"
);

const headers = {
    "Authorization": "Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "Content-Type": "application/json",
    "Accept": "application/json",
    "X-KaziTrust-Version": "v1",
};

let body = {
    "phone_number": "+22961000000",
    "context": {
        "transaction_amount": 150000,
        "transaction_currency": "XOF",
        "ip_address": "197.234.10.1",
        "user_agent": "Mozilla\/5.0..."
    }
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://127.0.0.1:8000/api/v1/trust/analyze';
$response = $client-&gt;post(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
            'X-KaziTrust-Version' =&gt; 'v1',
        ],
        'json' =&gt; [
            'phone_number' =&gt; '+22961000000',
            'context' =&gt; [
                'transaction_amount' =&gt; 150000.0,
                'transaction_currency' =&gt; 'XOF',
                'ip_address' =&gt; '197.234.10.1',
                'user_agent' =&gt; 'Mozilla/5.0...',
            ],
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://127.0.0.1:8000/api/v1/trust/analyze'
payload = {
    "phone_number": "+22961000000",
    "context": {
        "transaction_amount": 150000,
        "transaction_currency": "XOF",
        "ip_address": "197.234.10.1",
        "user_agent": "Mozilla\/5.0..."
    }
}
headers = {
  'Authorization': 'Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
  'Content-Type': 'application/json',
  'Accept': 'application/json',
  'X-KaziTrust-Version': 'v1'
}

response = requests.request('POST', url, headers=headers, json=payload)
response.json()</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-trust-analyze">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;request_id&quot;: &quot;uuid-v4&quot;,
    &quot;phone_number&quot;: &quot;+22961000000&quot;,
    &quot;decision&quot;: &quot;approve&quot;,
    &quot;score&quot;: 87,
    &quot;reasoning&quot;: &quot;Aucun swap SIM d&eacute;tect&eacute;. Num&eacute;ro actif depuis 18 mois...&quot;,
    &quot;nokia_signals&quot;: {
        &quot;sim_swap_detected&quot;: false,
        &quot;sim_change_days_ago&quot;: null,
        &quot;is_roaming&quot;: false,
        &quot;network_status&quot;: &quot;active&quot;,
        &quot;location_country&quot;: &quot;BJ&quot;
    },
    &quot;latency_ms&quot;: 1243,
    &quot;token_count&quot;: 387,
    &quot;cost_estimate&quot;: 0.000193,
    &quot;analyzed_at&quot;: &quot;2026-05-03T12:00:00Z&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;validation_failed&quot;,
    &quot;message&quot;: &quot;Le num&eacute;ro doit &ecirc;tre au format E.164.&quot;,
    &quot;errors&quot;: {
        &quot;phone_number&quot;: [
            &quot;Format invalide.&quot;
        ]
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (429):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;quota_exceeded&quot;,
    &quot;message&quot;: &quot;Quota mensuel atteint (500 requ&ecirc;tes).&quot;,
    &quot;used&quot;: 500,
    &quot;limit&quot;: 500
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-trust-analyze" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-trust-analyze"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-trust-analyze"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-trust-analyze" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-trust-analyze">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-trust-analyze" data-method="POST"
      data-path="api/v1/trust/analyze"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-trust-analyze', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-trust-analyze"
                    onclick="tryItOut('POSTapi-v1-trust-analyze');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-trust-analyze"
                    onclick="cancelTryOut('POSTapi-v1-trust-analyze');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-trust-analyze"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/trust/analyze</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-trust-analyze"
               value="Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
               data-component="header">
    <br>
<p>Example: <code>Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-trust-analyze"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-trust-analyze"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-KaziTrust-Version</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-KaziTrust-Version"                data-endpoint="POSTapi-v1-trust-analyze"
               value="v1"
               data-component="header">
    <br>
<p>Example: <code>v1</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone_number"                data-endpoint="POSTapi-v1-trust-analyze"
               value="+22961000000"
               data-component="body">
    <br>
<p>Le numéro au format E.164. Example: <code>+22961000000</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>context</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
<br>
<p>Contexte métier optionnel pour affiner l'analyse.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>transaction_amount</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="context.transaction_amount"                data-endpoint="POSTapi-v1-trust-analyze"
               value="150000"
               data-component="body">
    <br>
<p>Montant de la transaction en cours. Example: <code>150000</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>transaction_currency</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="context.transaction_currency"                data-endpoint="POSTapi-v1-trust-analyze"
               value="XOF"
               data-component="body">
    <br>
<p>Devise. Example: <code>XOF</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>ip_address</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="context.ip_address"                data-endpoint="POSTapi-v1-trust-analyze"
               value="197.234.10.1"
               data-component="body">
    <br>
<p>IP de l'utilisateur final. Example: <code>197.234.10.1</code></p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>user_agent</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="context.user_agent"                data-endpoint="POSTapi-v1-trust-analyze"
               value="Mozilla/5.0..."
               data-component="body">
    <br>
<p>User-Agent du device. Example: <code>Mozilla/5.0...</code></p>
                    </div>
                                    </details>
        </div>
        </form>

                    <h2 id="analyse-de-confiance-GETapi-v1-trust-logs">Historique des analyses</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retourne les dernières analyses effectuées par cette application (max 100).</p>

<span id="example-requests-GETapi-v1-trust-logs">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://127.0.0.1:8000/api/v1/trust/logs?per_page=20&amp;decision=reject&amp;from=2026-05-01&amp;until=2026-05-31" \
    --header "Authorization: Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --header "X-KaziTrust-Version: v1"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://127.0.0.1:8000/api/v1/trust/logs"
);

const params = {
    "per_page": "20",
    "decision": "reject",
    "from": "2026-05-01",
    "until": "2026-05-31",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "Content-Type": "application/json",
    "Accept": "application/json",
    "X-KaziTrust-Version": "v1",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://127.0.0.1:8000/api/v1/trust/logs';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
            'X-KaziTrust-Version' =&gt; 'v1',
        ],
        'query' =&gt; [
            'per_page' =&gt; '20',
            'decision' =&gt; 'reject',
            'from' =&gt; '2026-05-01',
            'until' =&gt; '2026-05-31',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://127.0.0.1:8000/api/v1/trust/logs'
params = {
  'per_page': '20',
  'decision': 'reject',
  'from': '2026-05-01',
  'until': '2026-05-31',
}
headers = {
  'Authorization': 'Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
  'Content-Type': 'application/json',
  'Accept': 'application/json',
  'X-KaziTrust-Version': 'v1'
}

response = requests.request('GET', url, headers=headers, params=params)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-trust-logs">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
  &quot;data&quot;: [...],
  &quot;meta&quot;: { &quot;total&quot;: 42, &quot;per_page&quot;: 20, &quot;current_page&quot;: 1 }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-trust-logs" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-trust-logs"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-trust-logs"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-trust-logs" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-trust-logs">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-trust-logs" data-method="GET"
      data-path="api/v1/trust/logs"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-trust-logs', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-trust-logs"
                    onclick="tryItOut('GETapi-v1-trust-logs');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-trust-logs"
                    onclick="cancelTryOut('GETapi-v1-trust-logs');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-trust-logs"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/trust/logs</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-trust-logs"
               value="Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
               data-component="header">
    <br>
<p>Example: <code>Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-trust-logs"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-trust-logs"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-KaziTrust-Version</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-KaziTrust-Version"                data-endpoint="GETapi-v1-trust-logs"
               value="v1"
               data-component="header">
    <br>
<p>Example: <code>v1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-v1-trust-logs"
               value="20"
               data-component="query">
    <br>
<p>Résultats par page (max 100). Example: <code>20</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>decision</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="decision"                data-endpoint="GETapi-v1-trust-logs"
               value="reject"
               data-component="query">
    <br>
<p>Filtrer par décision (approve/reject/manual_review). Example: <code>reject</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>from</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="from"                data-endpoint="GETapi-v1-trust-logs"
               value="2026-05-01"
               data-component="query">
    <br>
<p>Date de début (Y-m-d). Example: <code>2026-05-01</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>until</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="until"                data-endpoint="GETapi-v1-trust-logs"
               value="2026-05-31"
               data-component="query">
    <br>
<p>Date de fin (Y-m-d). Example: <code>2026-05-31</code></p>
            </div>
                </form>

                    <h2 id="analyse-de-confiance-GETapi-v1-trust-logs--requestId-">Détail d&#039;une analyse</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-v1-trust-logs--requestId-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://127.0.0.1:8000/api/v1/trust/logs/uuid-v4" \
    --header "Authorization: Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --header "X-KaziTrust-Version: v1"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://127.0.0.1:8000/api/v1/trust/logs/uuid-v4"
);

const headers = {
    "Authorization": "Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "Content-Type": "application/json",
    "Accept": "application/json",
    "X-KaziTrust-Version": "v1",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://127.0.0.1:8000/api/v1/trust/logs/uuid-v4';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
            'X-KaziTrust-Version' =&gt; 'v1',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://127.0.0.1:8000/api/v1/trust/logs/uuid-v4'
headers = {
  'Authorization': 'Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
  'Content-Type': 'application/json',
  'Accept': 'application/json',
  'X-KaziTrust-Version': 'v1'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-trust-logs--requestId-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;unauthorized&quot;,
    &quot;message&quot;: &quot;Cl&eacute; API introuvable ou r&eacute;voqu&eacute;e.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-trust-logs--requestId-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-trust-logs--requestId-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-trust-logs--requestId-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-trust-logs--requestId-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-trust-logs--requestId-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-trust-logs--requestId-" data-method="GET"
      data-path="api/v1/trust/logs/{requestId}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-trust-logs--requestId-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-trust-logs--requestId-"
                    onclick="tryItOut('GETapi-v1-trust-logs--requestId-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-trust-logs--requestId-"
                    onclick="cancelTryOut('GETapi-v1-trust-logs--requestId-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-trust-logs--requestId-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/trust/logs/{requestId}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-trust-logs--requestId-"
               value="Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
               data-component="header">
    <br>
<p>Example: <code>Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-trust-logs--requestId-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-trust-logs--requestId-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-KaziTrust-Version</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-KaziTrust-Version"                data-endpoint="GETapi-v1-trust-logs--requestId-"
               value="v1"
               data-component="header">
    <br>
<p>Example: <code>v1</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>requestId</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="requestId"                data-endpoint="GETapi-v1-trust-logs--requestId-"
               value="uuid-v4"
               data-component="url">
    <br>
<p>UUID de la requête. Example: <code>uuid-v4</code></p>
            </div>
                    </form>

                    <h2 id="analyse-de-confiance-GETapi-v1-trust-quota">Quota de l&#039;application</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retourne le quota mensuel et la consommation actuelle.</p>

<span id="example-requests-GETapi-v1-trust-quota">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://127.0.0.1:8000/api/v1/trust/quota" \
    --header "Authorization: Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --header "X-KaziTrust-Version: v1"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://127.0.0.1:8000/api/v1/trust/quota"
);

const headers = {
    "Authorization": "Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "Content-Type": "application/json",
    "Accept": "application/json",
    "X-KaziTrust-Version": "v1",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://127.0.0.1:8000/api/v1/trust/quota';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Authorization' =&gt; 'Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
            'X-KaziTrust-Version' =&gt; 'v1',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://127.0.0.1:8000/api/v1/trust/quota'
headers = {
  'Authorization': 'Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
  'Content-Type': 'application/json',
  'Accept': 'application/json',
  'X-KaziTrust-Version': 'v1'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-trust-quota">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;plan&quot;: &quot;Starter&quot;,
    &quot;limit&quot;: 2000,
    &quot;used&quot;: 147,
    &quot;remaining&quot;: 1853,
    &quot;resets_at&quot;: &quot;2026-06-01&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-trust-quota" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-trust-quota"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-trust-quota"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-trust-quota" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-trust-quota">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-trust-quota" data-method="GET"
      data-path="api/v1/trust/quota"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-trust-quota', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-trust-quota"
                    onclick="tryItOut('GETapi-v1-trust-quota');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-trust-quota"
                    onclick="cancelTryOut('GETapi-v1-trust-quota');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-trust-quota"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/trust/quota</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-trust-quota"
               value="Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
               data-component="header">
    <br>
<p>Example: <code>Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-trust-quota"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-trust-quota"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-KaziTrust-Version</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-KaziTrust-Version"                data-endpoint="GETapi-v1-trust-quota"
               value="v1"
               data-component="header">
    <br>
<p>Example: <code>v1</code></p>
            </div>
                        </form>

                <h1 id="statut">Statut</h1>

    <p>Vérifier l'état opérationnel de l'API.</p>

                                <h2 id="statut-GETapi-v1-status">Statut de l&#039;API</h2>

<p>
</p>

<p>Retourne l'état de l'API et la version courante.
Aucune authentification requise.</p>

<span id="example-requests-GETapi-v1-status">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://127.0.0.1:8000/api/v1/status" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --header "X-KaziTrust-Version: v1"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://127.0.0.1:8000/api/v1/status"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
    "X-KaziTrust-Version": "v1",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>


<div class="php-example">
    <pre><code class="language-php">$client = new \GuzzleHttp\Client();
$url = 'http://127.0.0.1:8000/api/v1/status';
$response = $client-&gt;get(
    $url,
    [
        'headers' =&gt; [
            'Content-Type' =&gt; 'application/json',
            'Accept' =&gt; 'application/json',
            'X-KaziTrust-Version' =&gt; 'v1',
        ],
    ]
);
$body = $response-&gt;getBody();
print_r(json_decode((string) $body));</code></pre></div>


<div class="python-example">
    <pre><code class="language-python">import requests
import json

url = 'http://127.0.0.1:8000/api/v1/status'
headers = {
  'Content-Type': 'application/json',
  'Accept': 'application/json',
  'X-KaziTrust-Version': 'v1'
}

response = requests.request('GET', url, headers=headers)
response.json()</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-status">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;operational&quot;,
    &quot;version&quot;: &quot;1.0.0&quot;,
    &quot;timestamp&quot;: &quot;2026-05-03T12:00:00Z&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-status" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-status"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-status"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-status" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-status">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-status" data-method="GET"
      data-path="api/v1/status"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-status', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-status"
                    onclick="tryItOut('GETapi-v1-status');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-status"
                    onclick="cancelTryOut('GETapi-v1-status');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-status"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/status</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-status"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-status"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>X-KaziTrust-Version</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="X-KaziTrust-Version"                data-endpoint="GETapi-v1-status"
               value="v1"
               data-component="header">
    <br>
<p>Example: <code>v1</code></p>
            </div>
                        </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                                                        <button type="button" class="lang-button" data-language-name="php">php</button>
                                                        <button type="button" class="lang-button" data-language-name="python">python</button>
                            </div>
            </div>
</div>
</body>
</html>
