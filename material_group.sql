-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.37-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             9.2.0.4947
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping database structure for cubic-pro
CREATE DATABASE IF NOT EXISTS `cubic-pro` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `cubic-pro`;


-- Dumping structure for procedure cubic-pro.mysql_insert_temporary_material_group
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `mysql_insert_temporary_material_group`()
BEGIN
	DECLARE has_error int default 0;
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET has_error = 1;
	TRUNCATE temporary_material_group;
		set @sql =concat("INSERT INTO temporary_material_groups (group_material,product_code,product_name,fiscal_year,apr_amount,may_amount,jun_amount,jul_amount,aug_amount,sep_amount,oct_amount,nov_amount,dec_amount,jan_amount,feb_amount,mar_amount,total) ","SELECT a.group_material,b.product_code,b.product_name,c.fiscal_year,c.apr_amount,c.may_amount,c.jun_amount,c.jul_amount,c.aug_amount,c.sep_amount,c.oct_amount,c.nov_amount,c.dec_amount,c.jan_amount,c.feb_amount,c.mar_amount,c.total from ","(SELECT '", replace(( select system_val from systems where system_code = 'group_material' and system_type='config_other'), ";", " ' as group_material UNION SELECT '"),"'",") a right join ","(","SELECT '", replace(replace(( select system_val from systems where system_code = 'product_code' and system_type='config_multiply'), ";", " ' as product_name UNION SELECT '"),",","' as product_code,'"),"'",") b on b.product_code = b.product_code"," left join v_material_group c on a.group_material = c.group_material and b.product_code = c.product_code");
			prepare stmt
		from @sql;
		execute stmt;
	DEALLOCATE PREPARE stmt;
	SELECT has_error;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
