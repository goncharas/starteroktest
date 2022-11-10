/* 
Скипты модификации БД для функционала "Партнерские склады"
*/

/* 07/07/2021 Sava */

-- Промежуточная таблица агрузки прайсов  
CREATE TABLE `oc_partner_price` 
(
`partnerprice_id` bigint(10) NOT NULL AUTO_INCREMENT,
`location_id` int(11) NOT NULL,
`product_name` varchar(255) NULL,
`vendor_code` varchar(100) NULL COMMENT 'очищенный артикул',
`vendor_code_raw` varchar(100) NULL COMMENT 'артикул из прайса',
`product_brand` varchar(100) NULL,
`oem_code` varchar(100) NULL COMMENT 'код оригинальной запчасти - если есть',
`oem_brand` varchar(100) NULL COMMENT 'бренд оригинальной запчасти - если есть',
`quantity` int(11) NOT NULL,
`price` decimal(15,4) NOT NULL,
`currency_code` varchar(3) not null,
`product_id`int(11) DEFAULT NULL,
`dateadd` datetime DEFAULT NULL,
`applied` tinyint default '0' COMMENT 'признак успешной переоценки',
`dateupdate` datetime DEFAULT NULL,
`user_id_add`  int(11) NOT NULL COMMENT 'пользователь импортировавший прайс',
 PRIMARY KEY (`partnerprice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




CREATE TABLE `oc_partner_extra_chart` (
`extrachart_id` int(11) NOT NULL AUTO_INCREMENT,
`extrachart_name` varchar(100) NOT NULL,
`extrachart_default` tinyint(1) NOT NULL Default '0',
PRIMARY KEY (`extrachart_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE `oc_partner_extra` (
`partnerextra_id` int(11) NOT NULL AUTO_INCREMENT,
`extrachart_id` int(11) NOT NULL,
`location_id` int(11) Default NULL,
`customer_group_id` int(11) NOT NULL,
`extra` decimal(3,1) NOT NULL COMMENT 'коэффициент умножения закупочной цены',
PRIMARY KEY (`partnerextra_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `oc_partner_brand` (
`partnerbrand_id` int(11) NOT NULL AUTO_INCREMENT,
`location_id` int(11) NOT NULL,
`manufacturer_id` int(11) NOT NULL,
`partner_brand` varchar(32) NOT NULL  COMMENT 'написание бренда в прайсе',
PRIMARY KEY (`partnerbrand_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `oc_partner_vendorcode` (
`partnervc_id` int(11) NOT NULL AUTO_INCREMENT,
`product_id` int(11) NOT NULL,
`location_id` int(11) NOT NULL,
`partner_vendorcode` varchar(32) NOT NULL COMMENT 'вариант написания артикула в прайсе',
PRIMARY KEY (`partnervc_id`)
)  ENGINE=MyISAM DEFAULT CHARSET=utf8;



ALTER TABLE `oc_location` ADD (
`currency_code` varchar(3) NOT NULL COMMENT 'валюта импортируемого прайса',
`stock_lot` int(11) Default '0'  COMMENT 'количество товара, если указано >',
`partner_module` varchar(32) Default NULL COMMENT 'PHP  модуль импорта прайса',
`is_partner` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'партнерский или свой адрес',
`shipping_term` varchar(20) Default NULL COMMENT 'срок отправки товара'
);


ALTER TABLE `oc_category` Add (
`extrachart_id` int(11) NULL COMMENT 'схема наценки товаров внутри категории'
);

-- Заполнение тестовых данных
INSERT INTO `oc_location` (`location_id`, `parent_id`, `name`, `status`, `currency_code`, `stock_lot`, `partner_module`, `is_partner`, `shipping_term`) 
VALUES ('1001', '0', 'Альтстар', '0', 'EUA', '5', 'altstar_xlsx_01', '1', '2-3');

INSERT INTO `oc_currency` (`currency_id`, `title`, `code`, `symbol_left`, `symbol_right`, `decimal_place`, `value`, `status`, `date_modified`) 
VALUES (NULL, 'Альтстар', 'EUA', '', '', '2', '0.02941176470588235294117647058824', '0', '2021-07-07 13:14:15');

INSERT INTO `oc_partner_extra_chart` (`extrachart_name`, `extrachart_default`) VALUES ('Общая', '1');
INSERT INTO `oc_partner_extra_chart` (`extrachart_name`) VALUES ('Стартеры');

INSERT INTO `oc_partner_extra` (`extrachart_id`, `customer_group_id`, `extra`) VALUES ('1', '17', '1.8');
INSERT INTO `oc_partner_extra` (`extrachart_id`, `customer_group_id`, `extra`) VALUES ('1', '18', '1.6');
INSERT INTO `oc_partner_extra` (`extrachart_id`, `customer_group_id`, `extra`) VALUES ('1', '19', '1.4');
INSERT INTO `oc_partner_extra` (`extrachart_id`, `customer_group_id`, `extra`) VALUES ('1', '20', '1.3');
INSERT INTO `oc_partner_extra` (`extrachart_id`, `customer_group_id`, `extra`) VALUES ('2', '17', '1.7');
INSERT INTO `oc_partner_extra` (`extrachart_id`, `customer_group_id`, `extra`) VALUES ('2', '18', '1.5');
INSERT INTO `oc_partner_extra` (`extrachart_id`, `customer_group_id`, `extra`) VALUES ('2', '19', '1.3');
INSERT INTO `oc_partner_extra` (`extrachart_id`, `customer_group_id`, `extra`) VALUES ('2', '20', '1.2');
INSERT INTO `oc_partner_extra` (`extrachart_id`, `location_id`, `customer_group_id`, `extra`) VALUES ('2', '1001', '17', '1.9');
INSERT INTO `oc_partner_extra` (`extrachart_id`, `location_id`, `customer_group_id`, `extra`) VALUES ('2', '1001', '18', '1.7');
INSERT INTO `oc_partner_extra` (`extrachart_id`, `location_id`, `customer_group_id`, `extra`) VALUES ('2', '1001', '19', '1.5');
INSERT INTO `oc_partner_extra` (`extrachart_id`, `location_id`, `customer_group_id`, `extra`) VALUES ('2', '1001', '20', '1.4');



/* 14/07/2021 Sava */
ALTER TABLE `oc_location` 
ADD COLUMN `category_id` INT NULL DEFAULT NULL AFTER `shipping_term`;


/* 16/07/2021 Sava */
ALTER TABLE `oc_product_price` 
DROP PRIMARY KEY,
ADD PRIMARY KEY (`product_id`, `customer_group_id`, `location_id`);
;


/* 18/07/2021 Sava */
ALTER TABLE `oc_manufacturer` 
ADD COLUMN `name_clear` VARCHAR(64) NULL DEFAULT NULL AFTER `noindex`,
ADD INDEX `i_man_clear_name` (`name_clear` ASC);


ALTER TABLE `oc_partner_brand` 
ADD COLUMN `partner_brand_clear` VARCHAR(64) NULL DEFAULT NULL AFTER `partner_brand`,
CHANGE COLUMN `partner_brand` `partner_brand` VARCHAR(64) NOT NULL COMMENT 'написание бренда в прайсе' ,
ADD INDEX `i_parnter_clear_brand` (`partner_brand_clear` ASC);
;

ALTER TABLE `oc_partner_price` 
 ADD INDEX `IDX_oc_partner_price#loc#brand#vendor` (`location_id`,`product_brand`,`vendor_code`);

/* 10/08/2021 */
ALTER TABLE `oc_partner_price` 
ADD COLUMN `manufacturer_id` INT(11) NULL DEFAULT NULL AFTER `user_id_add`;

ALTER TABLE `oc_product` 
ADD INDEX `i_product_manufact_sku` (`sku` ASC, `manufacturer_id` ASC);