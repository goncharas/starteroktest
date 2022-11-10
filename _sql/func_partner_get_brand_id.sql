CREATE DEFINER=`savamaster`@`%` FUNCTION `partner_get_brand_id`(a_brandname varchar(255), a_location_id int(11)) RETURNS int(11)
BEGIN
  DECLARE result int(11) DEFAULT '0';
    Set a_brandname =  
    REPLACE(
      REPLACE(
        REPLACE(
          REPLACE(
            REPLACE(trim(LOWER(a_brandname)),
                        ' ', '')
          ,'-','')
        ,'_','')
      ,'"','')
        ,"'",'');
        
  SELECT manufacturer_id 
    INTO result 
        FROM oc_manufacturer 
    WHERE name_clear like a_brandname 
        LIMIT 1;
 --  RETURN result;
  If result is null or result=0 then 
    SELECT manufacturer_id 
    INTO result 
        FROM oc_partner_brand 
        WHERE location_id = a_location_id 
      and 
      partner_brand_clear like a_brandname 
    LIMIT 1;
    If result is null then 
      SET result = 0;
    end if;        
    end if;
  RETURN result;
END