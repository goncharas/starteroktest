--
-- Скрипт сгенерирован Devart dbForge Studio 2020 for MySQL, Версия 9.0.567.0
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 10.08.2021 1:30:59
-- Версия сервера: 10.1.48
-- Версия клиента: 4.1
-- Пожалуйста, сохраните резервную копию вашей базы перед запуском этого скрипта
--


SET NAMES 'utf8';

--
-- Установка базы данных по умолчанию
--
USE starterok_uadb;

--
-- Создать индекс `IDX_oc_partner_price#loc#brand#vendor` для объекта типа таблица `oc_partner_price`
--
ALTER TABLE oc_partner_price 
  ADD INDEX `IDX_oc_partner_price#loc#brand#vendor`(location_id, product_brand, vendor_code);