CREATE DEFINER=`savamaster`@`%` PROCEDURE `partner_compare_product`(p_location_id int)
BEGIN
    -- идет по прайсу поставщика и проставляет соотвествие товаров или добавляет товар в каталог    
    DECLARE p_category_id int;
    DECLARE p_partnerprice_id int;
    DECLARE p_manufacturer_id int;
    DECLARE p_product_id int;
    DECLARE p_product_name varchar(255);
    DECLARE p_vendor_code varchar(100);
    DECLARE p_product_brand varchar(100);
    DECLARE p_apply_status int DEFAULT 0;
       
    DECLARE done int DEFAULT 0;
	
    DECLARE c_part_price CURSOR FOR
		SELECT
			partnerprice_id,
            product_name,
            vendor_code,
            manufacturer_id
		FROM oc_partner_price
        WHERE location_id = p_location_id
			AND (product_id is null OR product_id<=0) 
            AND manufacturer_id is not null;

	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

SET @pp_getbrand = concat("UPDATE oc_partner_price opp 
		INNER JOIN (
			Select ocm.manufacturer_id,  ocm.name_clear, opb.partner_brand_clear from oc_manufacturer ocm
			left join (Select * from oc_partner_brand  WHERE location_id = '",p_location_id,"' ) opb 
				on ocm.manufacturer_id = opb.manufacturer_id 
		) m
		SET opp.manufacturer_id = m.manufacturer_id
		WHERE 
			REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(trim(LOWER(opp.product_brand)),' ', ''),'-',''),'_',''),CHAR(34),''), char(39),'') 
		in (m.name_clear, m.partner_brand_clear)
			AND opp.manufacturer_id is null AND opp.location_id = '",p_location_id,"'
		LIMIT 10000");
    
    
    PREPARE pps_pp_getbrand FROM @pp_getbrand;


	-- обновляем справочник написания чистых имен бренда
    Update oc_manufacturer SET name_clear = REPLACE(
			REPLACE(
				REPLACE(
					REPLACE(
						REPLACE(trim(LOWER(name)),
                        ' ', '')
					,'-','')
				,'_','')
			,'"','')
        ,"'",'')
    WHERE name_clear is null;
    
    Update oc_partner_brand SET partner_brand_clear = REPLACE(
			REPLACE(
				REPLACE(
					REPLACE(
						REPLACE(trim(LOWER(partner_brand)),
                        ' ', '')
					,'-','')
				,'_','')
			,'"','')
        ,"'",'')
    WHERE partner_brand_clear is null or partner_brand_clear = '';
    
    
    -- заполняем Id бренда
    REPEAT       
		EXECUTE pps_pp_getbrand;
    UNTIL ROW_COUNT() = 0 END REPEAT;
        
  	
    -- сопостовлдяем существующие продукты

	OPEN c_part_price;
	REPEAT
		FETCH c_part_price 
			INTO p_partnerprice_id, 
				p_product_name,
				p_vendor_code,
				p_manufacturer_id;
		IF NOT done THEN
			Select product_id 
            INTO p_product_id 
			FROM oc_product
            WHERE TRIM(LOWER(sku)) = p_vendor_code 
				AND manufacturer_id = p_manufacturer_id 
                and State = 1 
                limit 1;

            Update oc_partner_price
            set product_id = p_product_id
            WHERE partnerprice_id=p_partnerprice_id;
        
        END IF;
	UNTIL done END REPEAT;
	CLOSE c_part_price;

	SET done = false;

    
    -- определяем категорию для новых продуктов
    Select category_id 
		INTO p_category_id 
		FROM oc_location
        WHERE location_id  = p_location_id;
	IF (p_category_id is null or p_category_id=0) THEN
		Select min(category_id) 
			INTO p_category_id 
			FROM oc_category;
    END IF;


	OPEN c_part_price;
	REPEAT
		FETCH c_part_price 
			INTO p_partnerprice_id, 
				p_product_name,
				p_vendor_code,
				p_manufacturer_id;
		IF NOT done THEN
			-- 1. проверяем соотвествие существующим товарам
		/*	SELECT partner_get_product_id(p_vendor_code, p_manufacturer_id,p_location_id) 
				INTO  p_product_id
				FROM dual;*/
        -- 2. Если товар новый но бренд найден - добавляем в каталог
			-- товар существует либо не может быть добавлен из за отсуствия бренда
          /*  SET p_apply_status = 1;
            IF (p_product_id = 0) THEN */
				CALL partner_add_product (
					p_product_name, 
                    p_vendor_code, 
                    p_manufacturer_id, 
                    p_category_id, 
                    p_location_id,
                    p_product_id);
				SET p_apply_status = 2;
       /*     END IF; */
            
			UPDATE oc_partner_price
				SET product_id = p_product_id, dateupdate = now()
				WHERE partnerprice_id = p_partnerprice_id;
                
		END IF;
	UNTIL done END REPEAT;

	CLOSE c_part_price;
END