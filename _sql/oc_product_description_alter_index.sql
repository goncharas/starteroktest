--
-- ������ ������������ Devart dbForge Studio 2020 for MySQL, ������ 9.0.567.0
-- �������� �������� ��������: http://www.devart.com/ru/dbforge/mysql/studio
-- ���� �������: 10.12.2021 0:17:27
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
-- ������� ������ `i_pd_tag` �� ������� ���� ������� `oc_product_description`
--
ALTER TABLE oc_product_description 
   DROP INDEX i_pd_tag;

--
-- ������� ������ `i_pd_tag` ��� ������� ���� ������� `oc_product_description`
--
ALTER TABLE oc_product_description 
  ADD FULLTEXT INDEX i_pd_tag(tag(333));