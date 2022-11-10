ALTER TABLE `td1q2018`.`prd` 
ADD COLUMN `status` TINYINT NULL DEFAULT 0 AFTER `usagedescription`;

ALTER TABLE `td1q2018`.`prd` 
ADD COLUMN `date_modified` DATETIME NULL AFTER `status`;

ALTER TABLE `td1q2018`.`suppliers` 
ADD COLUMN `description_changed` VARCHAR(70) NULL AFTER `hasnewversionarticles`;

ALTER TABLE `td1q2018`.`suppliers` 
ADD COLUMN `date_modified` DATETIME NULL AFTER `description_changed`;

/* таблица для промежуточных выборок списка товаров при поиске */
CREATE TABLE `td1q2018`.`session_search` (
  `part_number` varchar(50) DEFAULT NULL,
  `supplier_name` varchar(100) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `session_id` varchar(50) DEFAULT NULL,
  `date_inserted` datetime
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `td1q2018`.`models` 
ADD COLUMN `model_group` VARCHAR(128) NULL AFTER `manufacturerid`;
ALTER TABLE `td1q2018`.`models` 
ADD COLUMN `grouped` TINYINT(1) NULL DEFAULT 0 AFTER `model_group`;

-- !!!
-- Не хватает DDL добавления поля models.model_group_url


/* `savamaster_ua_com_entechit_start`. */

ALTER TABLE `ete_meta_pattern` 
CHANGE COLUMN `mp_datatype` `mp_datatype` VARCHAR(30) NULL DEFAULT NULL COMMENT 'Сущность, к которой применяетсмя: product' ;

INSERT INTO `ete_meta_pattern` (`mp_datatype`, `mp_language_id`, `mp_title`, `mp_description`) VALUES ('mark_models', '1', '{manufacturer} - стартер, генератор и их запчасти оптом и в розницу каталог с фото и характеристиками', 'Каталог стартеров и генераторов на {manufacturer} оптом и в розницу. Продажа и ремонт запчастей для стартера и генератора автомобиля в Харькове Днепропетровске Одессе и Киеве с доставкой по всей Украине.');
INSERT INTO `ete_meta_pattern` (`mp_datatype`, `mp_language_id`, `mp_title`, `mp_description`) VALUES ('mark_models', '3', '{manufacturer} - стартер, генератор и их запчасти оптом и в розницу каталог с фото и характеристиками', 'Каталог стартеров и генераторов на {manufacturer} оптом и в розницу. Продажа и ремонт запчастей для стартера и генератора автомобиля в Харькове Днепропетровске Одессе и Киеве с доставкой по всей Украине.');
INSERT INTO `ete_meta_pattern` (`mp_datatype`, `mp_language_id`, `mp_title`, `mp_description`) VALUES ('mark_modifications', '1', '{manufacturer} {model} - стартер, генератор и их запчасти оптом и в розницу каталог с фото и характеристиками', 'Каталог стартеров и генераторов на {manufacturer} {model} оптом и в розницу. Продажа и ремонт запчастей для стартера и генератора автомобиля в Харькове Днепропетровске Одессе и Киеве с доставкой по всей Украине.');
INSERT INTO `ete_meta_pattern` (`mp_datatype`, `mp_language_id`, `mp_title`, `mp_description`) VALUES ('mark_modifications', '3', '{manufacturer} {model} - стартер, генератор и их запчасти оптом и в розницу каталог с фото и характеристиками', 'Каталог стартеров и генераторов на {manufacturer} {model} оптом и в розницу. Продажа и ремонт запчастей для стартера и генератора автомобиля в Харькове Днепропетровске Одессе и Киеве с доставкой по всей Украине.');
INSERT INTO `ete_meta_pattern` (`mp_datatype`, `mp_language_id`, `mp_title`, `mp_description`) VALUES ('mark_products', '1', '{manufacturer} {model} {modification} - стартер, генератор и их запчасти оптом и в розницу каталог с фото и характеристиками', 'Каталог стартеров и генераторов на {manufacturer} {model} {modification} оптом и в розницу. Продажа и ремонт запчастей для стартера и генератора автомобиля в Харькове Днепропетровске Одессе и Киеве с доставкой по всей Украине.');
INSERT INTO `ete_meta_pattern` (`mp_datatype`, `mp_language_id`, `mp_title`, `mp_description`) VALUES ('mark_products', '3', '{manufacturer} {model} {modification} - стартер, генератор и их запчасти оптом и в розницу каталог с фото и характеристиками', 'Каталог стартеров и генераторов на {manufacturer} {model} {modification} оптом и в розницу. Продажа и ремонт запчастей для стартера и генератора автомобиля в Харькове Днепропетровске Одессе и Киеве с доставкой по всей Украине.');


CREATE TABLE `ete_markpage` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `date_inserted` DATETIME NULL,
  `date_updated` DATETIME NULL,
  `datatype` VARCHAR(20) NULL COMMENT 'brandlist/brand/model',
  `tecdoc_manufacturer_id` INT NULL,
  `tecdoc_model_id` INT NULL,
  `url` VARCHAR(254) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE `ete_markpage_description` (
  `markpage_id` int(11) NOT NULL,
  `language_id` int(11) DEFAULT NULL,
  `meta_title` varchar(254) DEFAULT NULL,
  `meta_description` varchar(254) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `meta_keyword` varchar(254) DEFAULT NULL,
  KEY `fk_ete_markpage_description_1_idx` (`markpage_id`),
  CONSTRAINT `fk_ete_markpage_description_1` FOREIGN KEY (`markpage_id`) REFERENCES `ete_markpage` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

