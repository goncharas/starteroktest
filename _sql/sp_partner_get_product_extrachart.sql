CREATE DEFINER=`savamaster`@`%` PROCEDURE `partner_get_product_extrachart`( 
p_product_id int, p_base_extrachart_id int,
inout p_extrachart_id int
)
BEGIN
  DECLARE p_extrachart_id int;
    
    DECLARE done int DEFAULT 0;
    DECLARE p_category_id INT;
    DECLARE p_parent_category_id INT;
    DECLARE p_cat_level INT;
    
    DECLARE c_categories CURSOR FOR
    select category_id, parent_category_id, cat_level, extrachart_id
    from t_categories;
    
    DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
    
    DROP TEMPORARY TABLE IF EXISTS t_categories;
    
  CREATE TEMPORARY table t_categories (
    tc_id int(11) NOT NULL AUTO_INCREMENT,
        category_id int,
    parent_category_id int,
        cat_level int,
    extrachart_id int,
        PRIMARY KEY (`tc_id`)
  ) ;
    CREATE UNIQUE  INDEX t_unic_index_name
      ON t_categories (category_id, parent_category_id);
    -- первоначально заполняем категорией товара
    INSERT IGNORE INTO t_categories (category_id, parent_category_id, cat_level, extrachart_id)
    SELECT ptc.category_id,
      cp.path_id,
            cp.level,
            c.extrachart_id
    FROM oc_product_to_category ptc
    left join oc_category_path cp on cp.category_id = ptc.category_id
    left join oc_category c on c.category_id = ptc.category_id
    WHERE ptc.product_id = p_product_id;
    
    -- курсором дописываем все родительские категории
    
  OPEN c_categories;
  REPEAT
    FETCH c_categories 
    INTO p_category_id,
    p_parent_category_id,
    p_cat_level,
    p_extrachart_id;
    IF NOT done THEN
    -- дописываем все родительские категории и их параметры
        INSERT IGNORE INTO t_categories (category_id, parent_category_id, cat_level, extrachart_id)
      SELECT cp.category_id,
      cp.path_id,
            cp.level,
            c.extrachart_id
      FROM oc_category_path cp 
      left join oc_category c on c.category_id = cp.category_id
      WHERE cp.category_id = p_parent_category_id;
    END IF;
  UNTIL done END REPEAT;
  CLOSE c_categories;
  
  -- Теперь нужно выбрать самую главную.
  SELECT extrachart_id 
  INTO p_extrachart_id 
  FROM t_categories 
  WHERE extrachart_id is not null
  ORDER BY cat_level desc
    LIMIT 1;
  
  IF p_extrachart_id is null or p_extrachart_id = 0 THEN
  SET p_extrachart_id = p_base_extrachart_id;
  END IF;

  DROP table t_categories;
END