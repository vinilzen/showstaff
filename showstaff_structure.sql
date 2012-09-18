-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Сен 18 2012 г., 17:08
-- Версия сервера: 5.5.24
-- Версия PHP: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `showstaff`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Booking`
--

CREATE TABLE IF NOT EXISTS `Booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `booking_date` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `amount` double NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq` (`booking_date`,`company_id`,`restaurant_id`,`product_id`),
  KEY `IDX_73D03BB5979B1AD6` (`company_id`),
  KEY `IDX_73D03BB5B1E7706E` (`restaurant_id`),
  KEY `IDX_73D03BB52ADD6D8C` (`supplier_id`),
  KEY `IDX_73D03BB54584665A` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=44 ;

-- --------------------------------------------------------

--
-- Структура таблицы `Company`
--

CREATE TABLE IF NOT EXISTS `Company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `extended_name` longtext COLLATE utf8_unicode_ci,
  `inn` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Структура таблицы `Product`
--

CREATE TABLE IF NOT EXISTS `Product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unit` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1CF73D31979B1AD6` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=109 ;

-- --------------------------------------------------------

--
-- Структура таблицы `Restaurant`
--

CREATE TABLE IF NOT EXISTS `Restaurant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `director` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A4C811EF979B1AD6` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Структура таблицы `Supplier`
--

CREATE TABLE IF NOT EXISTS `Supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `IDX_625C0E28979B1AD6` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Структура таблицы `SupplierProducts`
--

CREATE TABLE IF NOT EXISTS `SupplierProducts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `price` double NOT NULL,
  `primary_supplier` tinyint(1) NOT NULL,
  `supplier_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A8DED6134584665A` (`product_id`),
  KEY `IDX_A8DED6132ADD6D8C` (`supplier_id`),
  KEY `IDX_A8DED613979B1AD6` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=70 ;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Booking`
--
ALTER TABLE `Booking`
  ADD CONSTRAINT `FK_2FB1D4424584665A` FOREIGN KEY (`product_id`) REFERENCES `Product` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_73D03BB52ADD6D8C` FOREIGN KEY (`supplier_id`) REFERENCES `Supplier` (`id`),
  ADD CONSTRAINT `FK_73D03BB5979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `Company` (`id`),
  ADD CONSTRAINT `FK_73D03BB5B1E7706E` FOREIGN KEY (`restaurant_id`) REFERENCES `Restaurant` (`id`);

--
-- Ограничения внешнего ключа таблицы `Product`
--
ALTER TABLE `Product`
  ADD CONSTRAINT `FK_1CF73D31979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `Company` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Restaurant`
--
ALTER TABLE `Restaurant`
  ADD CONSTRAINT `FK_A4C811EF979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `Company` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Supplier`
--
ALTER TABLE `Supplier`
  ADD CONSTRAINT `FK_625C0E28979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `Company` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `SupplierProducts`
--
ALTER TABLE `SupplierProducts`
  ADD CONSTRAINT `FK_A8DED6132ADD6D8C` FOREIGN KEY (`supplier_id`) REFERENCES `Supplier` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_A8DED6134584665A` FOREIGN KEY (`product_id`) REFERENCES `Product` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_A8DED613979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `Company` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
