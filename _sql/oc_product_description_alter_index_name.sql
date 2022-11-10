--
-- Скрипт сгенерирован Devart dbForge Studio 2020 for MySQL, Версия 9.0.567.0
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 13.12.2021 1:49:19
-- Версия сервера: 5.7.12
-- Версия клиента: 4.1
-- Пожалуйста, сохраните резервную копию вашей базы перед запуском этого скрипта
--


SET NAMES 'utf8';

--
-- Установка базы данных по умолчанию
--
USE starterok_uadb;

--
-- Удалить индекс `i_pd_name` из объекта типа таблица `oc_product_description`
--
ALTER TABLE oc_product_description 
   DROP INDEX i_pd_name;

--
-- Создать индекс `i_pd_name` для объекта типа таблица `oc_product_description`
--
ALTER TABLE oc_product_description 
  ADD FULLTEXT INDEX i_pd_name(name);