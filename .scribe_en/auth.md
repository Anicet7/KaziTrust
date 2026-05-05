# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer kz_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Get your API key from the **Tenant Dashboard → Management → My API Services → API Keys**.

All KaziTrust keys are prefixed with `kz_` followed by 32 alphanumeric characters.
Each key is tied to an isolated tenant (your company) and only grants access to your data.

**Available environments:**
| Environment | Key prefix | Base URL |
|---|---|---|
| Sandbox | `kz_test_` | `https://sandbox.kazitrust.io` |
| Production | `kz_live_` | `https://api.kazitrust.io` |
