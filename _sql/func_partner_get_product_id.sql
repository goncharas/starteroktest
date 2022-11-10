CREATE DEFINER=`savamaster`@`%` FUNCTION `partner_get_product_id`(p_product_vendor_code varchar(100),
	p_brand_name varchar(100),
    p_location_id int(11)
    ) RETURNS int(11)
BEGIN
	DECLARE result int(11) DEFAULT '0';
	DECLARE p_product_id int;
    DECLARE p_manufacturer_id int(11);
    DECLARE p_brandname varchar(100);
	-- вычисляем код производителя
   -- Select partner_get_brand_id(p_brand_name,p_location_id) INTO p_manufacturer_id from dual;
   
   Set p_brandname =  
		REPLACE(
			REPLACE(
				REPLACE(
					REPLACE(
						REPLACE(trim(LOWER(p_brand_name)),
                        ' ', '')
					,'-','')
				,'_','')
			,'"','')
        ,"'",'');
	Select m.manufacturer_id 
	INTO p_manufacturer_id
	FROM
	oc_manufacturer m
	LEFT JOIN (Select * from oc_partner_brand Where location_id = p_location_id) pb ON pb.manufacturer_id = m.manufacturer_id  
	WHERE m.name_clear like p_brandname or pb.partner_brand_clear like p_brandname LIMIT 1; 
 
	-- если производитель не найден
	If p_manufacturer_id is null or p_manufacturer_id = 0 then 
		SET result = -1;
	ELSE
			SELECT product_id 
				INTO p_product_id 
				FROM oc_product 
				WHERE  REPLACE(
					REPLACE(
						REPLACE(
							REPLACE(
								REPLACE(trim(LOWER(SKU)),' ', '')
							,'-','')
						,'_','')
					,'"','')
				,"'",'') 
				LIKE TRIM(LOWER(p_product_vendor_code)) 
					AND
				manufacturer_id = p_manufacturer_id
                LIMIT 1;
			
		IF p_product_id IS NULL or p_product_id = 0 THEN
			SET result = 0;
		ELSE 
			SET result = p_product_id;
		END IF;
    END IF;
	RETURN result;
END