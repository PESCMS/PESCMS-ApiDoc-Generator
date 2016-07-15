-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-07-15 09:24:17
-- 服务器版本： 5.5.16
-- PHP Version: 5.6.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `apigenerate`
--

-- --------------------------------------------------------

--
-- 表的结构 `pes_class`
--

CREATE TABLE IF NOT EXISTS `pes_class` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_type` varchar(16) NOT NULL COMMENT '类的类型',
  `class_path` varchar(128) NOT NULL COMMENT '类的目录',
  `class_name` varchar(128) NOT NULL COMMENT '名称',
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `pes_class_restful`
--

CREATE TABLE IF NOT EXISTS `pes_class_restful` (
  `class_restful_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `class_restful` tinyint(1) NOT NULL COMMENT '0 GET 1 POST 2 PUT 3 DELETE 4 NONE',
  `class_restful_comment` text NOT NULL,
  `class_restful_code` text NOT NULL,
  PRIMARY KEY (`class_restful_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `pes_method`
--

CREATE TABLE IF NOT EXISTS `pes_method` (
  `method_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `class_restful` tinyint(1) NOT NULL COMMENT '0 GET 1 POST 2 PUT 3 DELETE 4 NONE',
  `method_name` varchar(128) NOT NULL COMMENT '方法名称',
  `method_comment` text NOT NULL COMMENT '方法注释',
  `method_code` text NOT NULL COMMENT '方法代码',
  PRIMARY KEY (`method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
