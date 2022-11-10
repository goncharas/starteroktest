CREATE DEFINER=`savamaster`@`%` FUNCTION `partner_get_exchangerate`(p_curr_code varchar(3)) RETURNS double(15,8)
BEGIN
  DECLARE result double(15,8);
  Select value into result FROM oc_currency WHERE code = p_curr_code limit 1;
RETURN result;
END