CREATE DEFINER=`savamaster`@`%` PROCEDURE `set_product_seo_url_single`(
  a_product_id int, 
    a_product_state int, 
    a_product_title varchar(255))
BEGIN
  DECLARE l_url varchar(1000);

  SELECT
    CONCAT(a_product_id, '_', translit(LOWER(a_product_title))) INTO l_url
  FROM dual;

  INSERT INTO oc_seo_url (store_id, language_id, query, keyword)
    SELECT
      0,
      language_id,
      CONCAT('product_id=', a_product_id),
      CASE 
    WHEN a_product_state = 1 THEN CONCAT(l_url, '.html') 
        WHEN a_product_state = 2 THEN CONCAT(l_url, '_r.html') 
        WHEN a_product_state = 3 THEN CONCAT(l_url, '_rmfd.html') 
        END
    FROM oc_language;
END