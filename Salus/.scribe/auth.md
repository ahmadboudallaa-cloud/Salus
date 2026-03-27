# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer Bearer {token}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Recuperez un token via /api/login puis utilisez-le dans le header Authorization.
