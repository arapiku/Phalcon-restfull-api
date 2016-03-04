-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1:3306
-- Generation Time: 2016-03-04 20:44:28
-- 服务器版本： 5.6.28-0ubuntu0.15.10.1
-- PHP Version: 5.6.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `onlyou`
--

-- --------------------------------------------------------

--
-- 表的结构 `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `fromUser` varchar(200) NOT NULL,
  `toUser` varchar(200) NOT NULL,
  `time` int(14) NOT NULL,
  `contents` text NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `message`
--

INSERT INTO `message` (`id`, `fromUser`, `toUser`, `time`, `contents`, `status`) VALUES
(2, 'fu', 'tu', 123, 'hello world', 1),
(3, 'fu', 'tu', 1455196622, 'hello', 1),
(4, 'fu', 'tu', 1455196641, 'hello', 1),
(5, 'fu', 'tu', 1455196732, 'hello', 1),
(6, 'fu', 'tu', 1455197098, 'hello', 1),
(7, 'fu', 'tu', 1455197138, 'hello', 1),
(8, 'fu', 'tu', 1455200259, 'hello', 1),
(9, 'fu', 'tu', 1455200277, 'hello', 1),
(10, 'fu', 'tu', 1455200307, 'hello', 1),
(11, 'fu', 'tu', 1455200360, 'hello', 1),
(12, 'fu', 'tu', 1455200361, 'hello', 1),
(13, 'fu', 'tu', 1455201171, 'hello', 1),
(14, 'fu', 'tu', 1455201266, 'hello', 1),
(15, 'fu', 'tu', 1455201288, 'hello', 1),
(16, 'fu', 'tu', 1455201289, 'hello', 1),
(17, 'fu', 'tu', 1456716108, 'hello', 1),
(18, 'fu', 'tu', 1456716112, 'hello', 1),
(19, 'fu', 'tu', 1456716113, 'hello', 1),
(20, 'fu', 'tu', 1456716114, 'hello', 1),
(21, 'fu', 'tu', 1456725525, 'hello', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fromUser` (`fromUser`),
  ADD KEY `toUser` (`toUser`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
