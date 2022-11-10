ALTER TABLE `oc_information` 
ADD COLUMN `is_landing` TINYINT(1) NULL DEFAULT 0 COMMENT 'Применчется шаблон вывода landing.twig' AFTER `noindex`;
