-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 29, 2025 at 05:42 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `consumers`
--

DROP TABLE IF EXISTS `consumers`;
CREATE TABLE IF NOT EXISTS `consumers` (
  `email` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL,
  `fullname` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL,
  `city` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL,
  `district` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `market_users`
--

DROP TABLE IF EXISTS `market_users`;
CREATE TABLE IF NOT EXISTS `market_users` (
  `email` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL,
  `name` varchar(25) COLLATE utf8mb4_turkish_ci NOT NULL,
  `password` varchar(25) COLLATE utf8mb4_turkish_ci NOT NULL,
  `city` varchar(25) COLLATE utf8mb4_turkish_ci NOT NULL,
  `district` varchar(25) COLLATE utf8mb4_turkish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `title` varchar(75) COLLATE utf8mb4_turkish_ci NOT NULL,
  `stock` int NOT NULL,
  `normalPrice` decimal(10,2) NOT NULL,
  `discounted` decimal(10,2) NOT NULL,
  `expDate` date NOT NULL,
  `img` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
COMMIT;
