/* 2021-05-24 */
ALTER TABLE `oc_location` 
ADD COLUMN `postcode` VARCHAR(15) NULL AFTER `status`;

ALTER TABLE `oc_location_description` 
ADD COLUMN `region` VARCHAR(45) NULL AFTER `email`;


UPDATE `oc_location` SET `postcode`='61000' WHERE `location_id`='1';
UPDATE `oc_location` SET `postcode`='61000' WHERE `location_id`='7';
UPDATE `oc_location` SET `postcode`='49000' WHERE `location_id`='11';
UPDATE `oc_location` SET `postcode`='69000' WHERE `location_id`='21';

UPDATE `oc_location_description` SET `region`='Харьковская область' WHERE `location_id`='1' and`language_id`='1';
UPDATE `oc_location_description` SET `region`='Харківська область' WHERE `location_id`='1' and`language_id`='3';
UPDATE `oc_location_description` SET `region`='Харьковская область' WHERE `location_id`='7' and`language_id`='1';
UPDATE `oc_location_description` SET `region`='Харківська область' WHERE `location_id`='7' and`language_id`='3';
UPDATE `oc_location_description` SET `region`='Днепропетровская область' WHERE `location_id`='11' and`language_id`='1';
UPDATE `oc_location_description` SET `region`='Дніпропетровська область' WHERE `location_id`='11' and`language_id`='3';
UPDATE `oc_location_description` SET `region`='Запорожская область' WHERE `location_id`='21' and`language_id`='1';
UPDATE `oc_location_description` SET `region`='Запорізька область' WHERE `location_id`='21' and`language_id`='3';
