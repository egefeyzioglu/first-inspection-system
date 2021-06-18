-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 18, 2021 at 12:08 PM
-- Server version: 5.7.11
-- PHP Version: 7.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `first`
--

-- --------------------------------------------------------

--
-- Table structure for table `inspected_items`
--

CREATE TABLE `inspected_items` (
  `inspected_item_id` int(11) NOT NULL,
  `item_id` text NOT NULL,
  `value` int(11) NOT NULL,
  `inspector_id` int(11) NOT NULL,
  `team_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inspections`
--

CREATE TABLE `inspections` (
  `inspection_id` int(11) NOT NULL,
  `team_number` int(11) NOT NULL,
  `assigned_inspector` int(11) NOT NULL,
  `inspection_completed` timestamp NULL DEFAULT NULL,
  `captain_signature` text NOT NULL,
  `mentour_signature` text NOT NULL,
  `initial_inspection_signature` text NOT NULL,
  `reinspection_signature` text NOT NULL,
  `final_inspection_signature` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inspectors`
--

CREATE TABLE `inspectors` (
  `inspector_id` int(11) NOT NULL,
  `username` text NOT NULL,
  `name` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `lastname` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `team_affiliation` text NOT NULL,
  `salt` varchar(64) NOT NULL,
  `password` text NOT NULL,
  `is_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `team_number` int(11) NOT NULL,
  `team_name` text NOT NULL,
  `team_captain` text NOT NULL,
  `team_mentour` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inspected_items`
--
ALTER TABLE `inspected_items`
  ADD PRIMARY KEY (`inspected_item_id`);

--
-- Indexes for table `inspections`
--
ALTER TABLE `inspections`
  ADD PRIMARY KEY (`inspection_id`);

--
-- Indexes for table `inspectors`
--
ALTER TABLE `inspectors`
  ADD PRIMARY KEY (`inspector_id`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`team_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inspected_items`
--
ALTER TABLE `inspected_items`
  MODIFY `inspected_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `inspections`
--
ALTER TABLE `inspections`
  MODIFY `inspection_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `inspectors`
--
ALTER TABLE `inspectors`
  MODIFY `inspector_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
