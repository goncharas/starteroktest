delimiter $$

CREATE DEFINER=`savamaster`@`%` PROCEDURE `partner_set_extra`(IN `p_location_id` int)
BEGIN
  DECLARE done int DEFAULT 0;
    DECLARE p_extrachart_id int default 0;
    DECLARE p_base_extrachart_id int default 0;
  DECLARE p_partnerprice_id int;
  DECLARE p_product_id int;
  DECLARE p_quantity int;
  DECLARE p_price decimal(15,4);
  DECLARE p_currency_code varchar(3) DEFAULT '';
    DECLARE p_currency_code_new varchar(3);
  DECLARE p_rate double(15,8);
    DECLARE p_def_currency_code varchar(3);
    
 --  DECLARE PS_insertprice varchar(1000);
    
    DECLARE c_part_price CURSOR FOR
    SELECT
      partnerprice_id,
            product_id,
            quantity,
            price,
            currency_code
    FROM oc_partner_price
        WHERE location_id = p_location_id
      AND (product_id is not null AND product_id > 0);
    -- limit 1000;

  DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
 
  SET @ps_insertprice = 'INSERT IGNORE INTO oc_product_price (product_id, customer_group_id, price, dateupdate,code,location_id,partnerextra_id) 
      SELECT ?, pe.customer_group_id, CEIL(?/?*pe.extra/10)*10, now(),?,?,pe.partnerextra_id
        FROM oc_partner_extra pe
        WHERE extrachart_id = ? and 
        ((location_id = ?) or (location_id is null and  
        not exists (select 1 from oc_partner_extra where location_id = ? and extrachart_id = ?)))';
    
    PREPARE pps_ps_insertprice FROM @ps_insertprice;
   
    -- clear stock quantity
    DELETE FROM oc_product_stock WHERE location_id =p_location_id;
    
    -- clear price
    DELETE FROM oc_product_price WHERE location_id =p_location_id;
    
   -- UPDATE oc_partner_price SET applied = 1 where location_id = p_location_id;
    
    -- get default cuttency code
    SELECT `value` 
    INTO p_def_currency_code 
        FROM oc_setting 
        WHERE `key` = 'config_currency' 
        LIMIT 1;
    
-- get default extra scheme to p_extrachart_id
  Select extrachart_id 
  into p_base_extrachart_id 
    from oc_partner_extra_chart 
    WHERE  extrachart_default = 1 
    Limit 1;
    IF p_base_extrachart_id = 0 or p_base_extrachart_id is null THEN
    SELECT min(extrachart_id) 
    INTO p_base_extrachart_id 
    FROM oc_partner_extra_chart 
    WHERE  extrachart_default = 1 
    LIMIT 1;
    END IF;
    
    
  -- main products cursor
  OPEN c_part_price;
    REPEAT
      FETCH c_part_price 
      INTO p_partnerprice_id,
      p_product_id,
      p_quantity,
            p_price,
            p_currency_code_new;
    IF NOT done THEN
        
      -- get extrachart for product
            CALL partner_get_product_extrachart( 
            p_product_id, 
                        p_base_extrachart_id,
            p_extrachart_id);
            
         --   Select p_extrachart_id, p_product_id,p_base_extrachart_id, p_extrachart_id from dual;            
      -- get current currency rate into  p_rate
         
           IF p_currency_code <> p_currency_code_new THEN
       
        SET p_rate = partner_get_exchangerate(p_currency_code_new);
                SET p_currency_code=p_currency_code_new;
      END IF;
           
            -- set extra price
       SET @pp_product_id = p_product_id;
       SET @pp_price = p_price; 
       SET @pp_rate = p_rate; 
       SET @pp_def_currency_code = p_def_currency_code; 
       SET @pp_location_id = p_location_id; 
       SET @pp_extrachart_id = p_extrachart_id; 
        
       EXECUTE pps_PS_insertprice USING @pp_product_id, @pp_price, @pp_rate, @pp_def_currency_code, @pp_location_id, @pp_extrachart_id, @pp_location_id, @pp_location_id, @pp_extrachart_id;
      /* INSERT IGNORE INTO oc_product_price (
        product_id,
                customer_group_id,
        price, 
        dateupdate, 
        code, 
        location_id,
                partnerextra_id) 
      SELECT p_product_id, 
        pe.customer_group_id,
                 CEIL(p_price/p_rate*pe.extra/10)*10,
                now(),
                p_def_currency_code,
                p_location_id,
                pe.partnerextra_id
        FROM oc_partner_extra pe
        where extrachart_id = p_extrachart_id and 
        ((location_id = p_location_id) or (location_id is null and  
        not exists (select 1 from oc_partner_extra where location_id = p_location_id and extrachart_id = p_extrachart_id)));
*/

        END IF;
        UNTIL done END REPEAT;
  CLOSE c_part_price;

  DEALLOCATE PREPARE pps_PS_insertprice;
  -- set quantity
    INSERT IGNORE INTO oc_product_stock (product_id, location_id, quantity, dateupdate)
    SELECT product_id, p_location_id, quantity, now() 
    FROM oc_partner_price 
        WHERE location_id = p_location_id
        AND product_id>0;
        -- AND applied=3;

END$$

