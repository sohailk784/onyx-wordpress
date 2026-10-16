-- deploy28f rollback: republish the three -2 pages. Idempotent.
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_520_ci;
UPDATE wp_posts SET post_status='publish'
 WHERE post_type='page' AND post_status='draft'
   AND post_name IN ('independent-doctor-consultation-2','product-catalogue-2','analytical-reference-calculator-2');
SELECT post_name, post_status FROM wp_posts WHERE post_type='page' AND post_name IN ('independent-doctor-consultation-2','product-catalogue-2','analytical-reference-calculator-2');
