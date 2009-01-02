CREATE USER 'template'@'localhost' IDENTIFIED BY  'T3mpl@t3';

GRANT USAGE ON * . * TO  'template'@'localhost' IDENTIFIED BY  'T3mpl@t3' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Dec 24, 2008 at 11:52 AM
-- Server version: 5.0.41
-- PHP Version: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `template`
-- 
DROP DATABASE IF EXISTS `template`;
CREATE DATABASE `template` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT ALL PRIVILEGES ON  `template` . * TO  'template'@'localhost';
USE `template`;

-- --------------------------------------------------------

-- 
-- Table structure for table `fgsafety_groups`
-- 

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `deleted_date` datetime default NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `parent_id` int(11) NOT NULL default '0',
  `lft` int(11) NOT NULL default '0',
  `rght` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `fgsafety_groups`
-- 

INSERT INTO `groups` VALUES (1, NULL, NULL, NULL, 0, 'Administration', 'Our first all-powerful group.', 0, 1, 2);

-- --------------------------------------------------------

-- 
-- Table structure for table `fgsafety_users`
-- 

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `deleted_date` datetime default NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  `group_id` smallint(6) NOT NULL default '0',
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `fgsafety_users`
-- 

INSERT INTO `users` VALUES (1, NULL, NULL, NULL, 0, 1, 'Wouter', 'Verweirder', 'wouter@aboutme.be', '43b7de38afeb9375c654a73ca88b3fbc2245d0e3', 1);
