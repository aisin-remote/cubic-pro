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


-- Dumping structure for procedure cubic-pro.mysql_insert_temporary_material_product
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `mysql_insert_temporary_material_product`()
BEGIN
	DECLARE has_error int default 0;
	DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET has_error = 1;
	TRUNCATE temporary_material_product;
		set @sql = concat("INSERT into temporary_material_products (product_code,product_name,fiscal_year,apr_amount,may_amount,jun_amount,jul_amount,aug_amount,sep_amount,oct_amount,nov_amount,dec_amount,jan_amount,feb_amount,mar_amount) ","(SELECT a.*,b.fiscal_year,b.apr_amount,b.may_amount,b.jun_amount,b.jul_amount,b.aug_amount,b.sep_amount,b.oct_amount,b.nov_amount,b.dec_amount,b.jan_amount,b.feb_amount,b.mar_amount,b.total from (","SELECT '", replace(replace(( select system_val from systems where system_code = 'product_code' and system_type='config_multiply'), ";", " ' as product_name UNION SELECT '"),",","' as product_code,'"),"' as product_name",") a left join v_material_product b on a.product_code = b.product_code and b.fiscal_year = '2019')");
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
