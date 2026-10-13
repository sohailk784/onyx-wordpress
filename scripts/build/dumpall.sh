D="${ONYX_WORK:-build-work}/dump"
node dump.mjs https://onyxhealth.net/ $D/header.json header &
node dump.mjs https://onyxhealth.net/ $D/footer.json footer &
wait
for r in what-are-peptides faq payment-delivery product-returns contact wholesale-application independent-doctor-consultation terms-conditions privacy-policy refund-policy shipping-policy cookies-policy accessibility-policy product-catalogue dual-chamber-auto-mixing-pen check-code analytical-reference-calculator peptide-plans peptides; do
  node dump.mjs https://onyxhealth.net/$r $D/$r.json main &
  [ $(jobs -r | wc -l) -ge 4 ] && wait -n
done; wait; echo DONE
