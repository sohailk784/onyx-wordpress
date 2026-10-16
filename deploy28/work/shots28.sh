#!/bin/bash
D=/workspace/onyx/deploy28/crops; B=http://localhost:8088; S=/workspace/onyx/homev2/shot28.sh
for s in what-are-peptides faq payment-delivery product-returns contact wholesale-application independent-doctor-consultation terms-conditions privacy-policy refund-policy shipping-policy cookies-policy accessibility-policy product-catalogue check-code analytical-reference-calculator peptide-plans wishlist b2b-login my-account; do $S $B/$s-2/ $D $s-2; done
STATE=/workspace/onyx/tmp/state-cart.json $S $B/cart-2/ $D cart-2
STATE=/workspace/onyx/tmp/state-cart.json $S $B/checkout-2/ $D checkout-2
STATE=/workspace/onyx/tmp/state-cart.json $S "$B/product/5amino1mq/?v2=1" $D product-v2
STATE=/workspace/onyx/tmp/state-order.json $S "$B/checkout-2/order-received/734/?key=wc_order_JMZLJtwvzeCv1" $D thankyou-v2
echo ALLDONE
