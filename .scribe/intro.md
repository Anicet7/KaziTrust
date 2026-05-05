# Introduction

**KaziTrust** est un Hub de Confiance SaaS B2B qui abstrait la complexité des API réseau
Nokia CAMARA derrière une interface unifiée, propulsée par des agents IA.
Permettez à votre application de détecter la fraude mobile (SIM Swap, usurpation d'identité)
et de vérifier l'identité de vos utilisateurs en quelques lignes de code,
sans jamais gérer directement les opérateurs télécoms.

<aside>
    <strong>Base URL</strong>: <code>http://127.0.0.1:8000</code>
</aside>


<p style="text-align:right">
    🌐 <a href="/docs/en"><strong>Read in English</strong></a>
</p>

## Bienvenue sur la documentation de l'API KaziTrust

KaziTrust démocratise l'accès aux capacités avancées des réseaux télécoms en Afrique subsaharienne.
Notre plateforme orchestre simultanément plusieurs signaux CAMARA de Nokia — **SIM Swap**,
**Number Verification**, **KYC Match** et **Location Verification** — et vous renvoie
une **décision métier claire et actionnable** en temps réel.

### Ce que vous pouvez faire avec cette API

- 🔐 **Anti-Fraude & SIM Swap** : Détectez les échanges de carte SIM suspects avant d'approuver
  une transaction ou un prêt. Recevez un score de confiance et un motif de rejet précis.
- 📍 **Vérification de Localisation** : Confirmez silencieusement la cohérence géographique
  d'un utilisateur via le réseau, sans GPS ni consentement intrusif.
- 🪪 **KYC & Correspondance d'Identité** : Vérifiez qu'un numéro de téléphone correspond
  bien à l'identité déclarée par l'utilisateur (nom, date de naissance, etc.).
- 📲 **Vérification de Numéro (Silent Auth)** : Remplacez vos SMS OTP coûteux et vulnérables
  par une vérification silencieuse du numéro via le réseau de l'opérateur.
- 📊 **Score de Confiance Global** : Notre agent IA analyse tous ces signaux en parallèle
  et renvoie une décision unifiée (ex: *"Score 85 % — Approuvé"* ou *"Rejeté — SIM Swap détecté il y a 2 h"*).

### Cas d'usage typiques

| Secteur | Cas d'usage |
|---|---|
| Micro-crédit / IMF | Approbation de prêt instantanée avec vérification SIM + localisation |
| E-commerce & Paiements | Blocage automatique des transactions à haut risque |
| Onboarding digital | Remplacement des OTP SMS par une auth silencieuse |
| Mobile Money | Protection des transferts contre l'usurpation d'identité |

### Authentification

Toutes les requêtes doivent inclure votre clé API KaziTrust dans le header `Authorization` :

```
Authorization: Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

Obtenez ou régénérez vos clés depuis votre **Tableau de Bord Tenant → Mes Services API → Clés API**.

<aside class="notice">
Chaque tenant dispose de clés API isolées. Ne partagez jamais votre clé <code>kz_</code>.
En cas de compromission, révoquez-la immédiatement depuis votre tableau de bord.
</aside>

<aside class="success">
Vous pouvez tester chaque endpoint directement depuis cette page grâce au bouton
<strong>Try it out</strong>. Assurez-vous d'avoir renseigné votre clé API en haut de page.
</aside>

