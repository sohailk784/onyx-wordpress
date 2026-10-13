# Crypto payment gateway (onyx-crypto-gateway)

Offline gateway "Crypto (USDT / USDC)".
- Wallets are configured in WooCommerce → Settings → Payments, one per line: `Label | address`.
  **They are stored in the database only, never in this repo.** Verify every address before going live.
- At checkout the customer chooses a network; the order goes **on-hold** and stock is reduced.
- The thank-you page and email show the amount, the address, a QR code and a copy button.
- The customer can upload a payment proof (JPG/PNG/WEBP/PDF ≤ 5 MB) plus TXID. Files go to
  `uploads/onyx-payment-proofs/` under random names; deny direct web access (an `.htaccess` is written for
  Apache, add an equivalent `location` deny rule for nginx). Admins view proofs from the order screen.
- Mark the order *Processing* manually once the payment is confirmed on chain.
