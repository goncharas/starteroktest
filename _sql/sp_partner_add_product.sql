CREATE DEFINER=`savamaster`@`%` PROCEDURE `partner_add_product`(
	p_product_name varchar(255), 
    p_vendor_code varchar(100), 
    p_manufacturer_id int, 
    p_category_id int, 
    p_location_id int,
    INOUT p_product_id int)
proc_label: BEGIN

	
	DECLARE p_manufacturer_name varchar(64);
    DECLARE p_state varchar(3);
    
    IF p_manufacturer_id = 0 THEN
		SET p_product_id = -1;
       LEAVE proc_label;
	END IF;
  
	SELECT `name` 
		INTO p_manufacturer_name
		FROM oc_manufacturer
		WHERE manufacturer_id = p_manufacturer_id;
  
	SET p_state = 'NEW';
  
   SET p_product_name = CONCAT_WS(' ', p_vendor_code, p_manufacturer_name, p_product_name);
    
    -- добавляем продукт
   INSERT INTO oc_product (
		manufacturer_id,
		model,
		sku,
		status,
		date_added,
		date_modified,
		upc, ean, jan, isbn, mpn,
		location,
		stock_status_id,
		tax_class_id,
		state)
	VALUES (
      p_manufacturer_id,
      CONCAT_WS('_', p_vendor_code, p_manufacturer_name),
      p_vendor_code,
      1,
      NOW(),
      NOW(),
      '', '', '', '', '',
      1,
      7, 9, 1);

	SELECT LAST_INSERT_ID() into p_product_id;

    INSERT INTO oc_product_description (
    product_id,
    language_id,
    `name`,
    `description`,
    `meta_title`,
    `meta_description`,
    `meta_keyword`,
    `meta_h1`,
    `tag`)
      SELECT
        p_product_id,
        language_id,
        p_product_name,
        p_product_name,
        p_product_name,
        p_product_name,
        p_product_name,
        p_product_name,
        ''
      FROM oc_language
      WHERE language_id IN (1, 3);

    INSERT IGNORE oc_product_to_category (product_id, category_id, main_category)
      VALUES (p_product_id, p_category_id, 1);
    INSERT  IGNORE oc_product_to_store (product_id, store_id)
      VALUES (p_product_id, 0);
  
	-- дописываем метатеги
    CALL set_meta_pattern_product_single(p_product_id, 'product');
    
    -- создаем URL
    CALL set_product_seo_url_single(p_product_id, 1, p_product_name);
    
END