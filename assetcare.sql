-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2026 at 03:33 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `assetcare`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `tag` varchar(100) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial` varchar(100) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `purchaseDate` varchar(50) DEFAULT NULL,
  `repairCount` int(11) DEFAULT 0,
  `repairs_json` longtext DEFAULT NULL,
  `deliveryCount` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`tag`, `type`, `brand`, `model`, `serial`, `status`, `purchaseDate`, `repairCount`, `repairs_json`, `deliveryCount`) VALUES
('DP/QBL-1', 'Desktop', 'Dell', 'Vostro 3670', 'HHXZKQ2', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-10', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863513', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-11', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863760', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-12', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863577', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-13', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863218', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-14', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516862335', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-15', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863195', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-16', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863528', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-17', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863671', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-18', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863804', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-19', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863677', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-2', 'Desktop', 'Dell', 'Vostro 3670', 'HCM6LQ2', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-20', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863736', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-21', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863507', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-22', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863763', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-23', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516862686', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-24', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863250', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-25', 'Desktop', 'Dell', 'Vostro 3670', '2TNM8T2', 'N/A', '2/13/2019', 0, '[]', 0),
('DP/QBL-26', 'Desktop', 'Dell', 'Vostro 3670', '2BFN8T2', 'N/A', '2/13/2019', 0, '[]', 0),
('DP/QBL-27', 'Desktop', 'Dell', 'Vostro 3670', '2VJM8T2', 'N/A', '2/13/2019', 0, '[]', 0),
('DP/QBL-28', 'Desktop', 'Dell', 'Vostro 3670', '2WKW8T2', 'N/A', '2/13/2019', 0, '[]', 0),
('DP/QBL-29', 'Desktop', 'Dell', 'Vostro 3670', '2W9T8T2', 'N/A', '2/13/2019', 0, '[]', 0),
('DP/QBL-3', 'Desktop', 'Dell', 'Vostro 3670', 'HH57LQ2', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-30', 'Desktop', 'Dell', 'Vostro 3670', '2B6T8T2', 'N/A', '2/13/2019', 0, '[]', 0),
('DP/QBL-31', 'Desktop', 'Dell', 'Vostro 3670', '320Q8T2', 'N/A', '2/13/2019', 0, '[]', 0),
('DP/QBL-32', 'Desktop', 'Dell', 'Vostro 3670', '31LT8T2', 'N/A', '2/13/2019', 0, '[]', 0),
('DP/QBL-33', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG002472494', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-34', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG00247949A', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-35', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG002503497', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-36', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG002449498', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-37', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG00248449A', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-38', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG00245749B', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-39', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG002481499', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-40', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG00248549H', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-41', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG002502495', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-42', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG002487494', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-43', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG002537490', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-45', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG00248949D', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-46', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG002458497', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-47', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG00251049F', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-48', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG00249949G', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-49', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG00254149G', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-5', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863460', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-50', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG002469496', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-51', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG002473492', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-52', 'Desktop', 'Asus', 'ExpertCenter D5 SFF D500SA', 'LCPFCG002477496', 'N/A', '3/1/2021', 0, '[]', 0),
('DP/QBL-53', 'Desktop', 'Asus', 'Customize', '', 'N/A', '3/25/2024', 0, '[]', 0),
('DP/QBL-54', 'Desktop', 'Asus', 'Customize', '', 'N/A', '3/25/2024', 0, '[]', 0),
('DP/QBL-55', 'Desktop', 'Asus', 'Customize', '', 'N/A', '3/25/2024', 0, '[]', 0),
('DP/QBL-56', 'Desktop', 'Asus', 'Customize', '', 'N/A', '3/25/2024', 0, '[]', 0),
('DP/QBL-57', 'Desktop', 'Asus', 'Customize', '', 'N/A', '3/25/2024', 0, '[]', 0),
('DP/QBL-58', 'Desktop', 'Asus', 'Customize', '', 'N/A', '3/25/2024', 0, '[]', 0),
('DP/QBL-59', 'Desktop', 'Asus', 'Customize', '', 'N/A', '3/25/2024', 0, '[]', 0),
('DP/QBL-6', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863716', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-60', 'Desktop', 'Asus', 'Customize', '', 'N/A', '3/25/2024', 0, '[]', 0),
('DP/QBL-61', 'Desktop', 'Asus', 'Customize', '', 'N/A', '3/25/2024', 0, '[]', 0),
('DP/QBL-62', 'Desktop', 'Asus', 'Customize', '', 'N/A', '3/25/2024', 0, '[]', 0),
('DP/QBL-7', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863610', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-8', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863648', 'N/A', '2/12/2019', 0, '[]', 0),
('DP/QBL-9', 'Desktop', 'MSI', 'H310M Pro-VDH', 'I516863748', 'N/A', '2/12/2019', 0, '[]', 0);

-- --------------------------------------------------------

--
-- Table structure for table `slots`
--

CREATE TABLE `slots` (
  `id` int(11) NOT NULL,
  `sn` varchar(255) DEFAULT NULL,
  `date_val` varchar(255) DEFAULT NULL,
  `slotNo` varchar(255) DEFAULT NULL,
  `slotName` varchar(255) DEFAULT NULL,
  `totalAssets` int(11) DEFAULT 0,
  `returnToIT` int(11) DEFAULT 0,
  `eol` int(11) DEFAULT 0,
  `pending` int(11) DEFAULT 0,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slots`
--

INSERT INTO `slots` (`id`, `sn`, `date_val`, `slotNo`, `slotName`, `totalAssets`, `returnToIT`, `eol`, `pending`, `remarks`) VALUES
(1, '1', '2026-07-01', 'S-01', 'Laptop', 200, 0, 0, 0, ''),
(2, '2', '2026-08-12', 'S-01', 'Desktop', 60, 0, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passwordHash` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `requestDate` varchar(100) DEFAULT NULL,
  `lastSeen` bigint(20) DEFAULT 0,
  `mustResetPassword` int(11) DEFAULT 0,
  `reset_code` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `api_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `email`, `passwordHash`, `role`, `status`, `requestDate`, `lastSeen`, `mustResetPassword`, `reset_code`, `reset_expires`, `api_token`) VALUES
('admin', 'assetcare@quantanite.com', '$2y$10$TYohiKlYTGdOnpNi.1zcRuTFUljv7l6W7qcWBSGmJ23YW6zfMmTeG', 'admin', 'active', '', 1786627986000, 0, '$2y$10$oQelyhIdqMJOdyqjuvtNjOvveFclMc5gxBx2iwtlFFpRt8W91kIzS', '2026-08-13 11:25:39', '438f2b966f102ae8e06e63e7cb46ff1742b48fd325aedcdc9eca27505d3f7518');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`tag`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_tag` (`tag`);

--
-- Indexes for table `slots`
--
ALTER TABLE `slots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `slots`
--
ALTER TABLE `slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
