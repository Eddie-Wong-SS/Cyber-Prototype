-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2020 at 06:30 PM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 5.6.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbsecurity`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblformpass`
--

CREATE TABLE `tblformpass` (
  `fPassId` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `formPass` char(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblformpass`
--

INSERT INTO `tblformpass` (`fPassId`, `userId`, `formPass`) VALUES
(1, 2, '4198220be6d3bb16434e346c56ed287f9f0f2e7a'),
(2, 3, '4198220be6d3bb16434e346c56ed287f9f0f2e7a'),
(3, 3, '7aa98a42f1d2282ed98005c2eb3ba099fbf3e2a5');

-- --------------------------------------------------------

--
-- Table structure for table `tbllogin`
--

CREATE TABLE `tbllogin` (
  `userId` int(11) NOT NULL,
  `fullName` varchar(100) DEFAULT NULL,
  `Password` char(40) DEFAULT NULL,
  `veriCode` char(32) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Status` char(1) DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbllogin`
--

INSERT INTO `tbllogin` (`userId`, `fullName`, `Password`, `veriCode`, `Email`, `Status`) VALUES
(1, 'ADMIN', '516015ad3b1e82786c59fdcd21c87956c9a55dc1', NULL, '', 'A'),
(3, 'Decant Azure', '7aa98a42f1d2282ed98005c2eb3ba099fbf3e2a5', 'ffa393a29dd06983405ec100b965f921', 'decedentazure@gmail.com', 'A');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblformpass`
--
ALTER TABLE `tblformpass`
  ADD PRIMARY KEY (`fPassId`);

--
-- Indexes for table `tbllogin`
--
ALTER TABLE `tbllogin`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblformpass`
--
ALTER TABLE `tblformpass`
  MODIFY `fPassId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbllogin`
--
ALTER TABLE `tbllogin`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
