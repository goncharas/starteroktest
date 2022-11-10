--
-- ������ ������������ Devart dbForge Studio 2020 for MySQL, ������ 9.0.567.0
-- �������� �������� ��������: http://www.devart.com/ru/dbforge/mysql/studio
-- ���� �������: 13.12.2021 1:49:19
-- ������ �������: 5.7.12
-- ������ �������: 4.1
-- ����������, ��������� ��������� ����� ����� ���� ����� �������� ����� �������
--


SET NAMES 'utf8';

--
-- ��������� ���� ������ �� ���������
--
USE starterok_uadb;

--
-- ������� ������ `i_pd_name` �� ������� ���� ������� `oc_product_description`
--
ALTER TABLE oc_product_description 
   DROP INDEX i_pd_name;

--
-- ������� ������ `i_pd_name` ��� ������� ���� ������� `oc_product_description`
--
ALTER TABLE oc_product_description 
  ADD FULLTEXT INDEX i_pd_name(name);