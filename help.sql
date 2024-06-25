-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2024 at 11:57 AM
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
-- Database: `help`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_station`
--

CREATE TABLE `tbl_station` (
  `id` int(11) NOT NULL,
  `station_id` varchar(255) DEFAULT NULL,
  `station_name` varchar(255) DEFAULT NULL,
  `station_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_station`
--

INSERT INTO `tbl_station` (`id`, `station_id`, `station_name`, `station_type`) VALUES
(1, '168', 'nun', 'KoKo'),
(2, '007', 'zionp', 'DoDo');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ticket`
--

CREATE TABLE `tbl_ticket` (
  `id` int(11) NOT NULL,
  `ticket_id` varchar(255) DEFAULT NULL,
  `station_id` varchar(255) DEFAULT NULL,
  `station_name` varchar(255) DEFAULT NULL,
  `station_type` varchar(255) DEFAULT NULL,
  `issue_description` longtext DEFAULT NULL,
  `issue_image` varchar(255) DEFAULT NULL,
  `issue_type` varchar(255) DEFAULT NULL,
  `priority` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `users_id` varchar(200) DEFAULT NULL,
  `ticket_open` datetime DEFAULT NULL,
  `ticket_close` datetime DEFAULT NULL,
  `comment` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_ticket`
--

INSERT INTO `tbl_ticket` (`id`, `ticket_id`, `station_id`, `station_name`, `station_type`, `issue_description`, `issue_image`, `issue_type`, `priority`, `status`, `users_id`, `ticket_open`, `ticket_close`, `comment`) VALUES
(1, 'POS2406000001', '168', 'nun', 'KoKo', '123', 'uploads/1.png', 'Software, Hardware, Network', 'Medium', 'in_progress', '3,4', '2024-06-12 11:49:49', NULL, '123'),
(2, 'POS2406000002', '168', 'nun', 'KoKo', '123', 'uploads/Screenshot 2024-01-30 155757.png', 'Hardware, Network, Dispenser', 'Medium', 'in_progress', '3,4', '2024-06-12 11:55:04', NULL, '123'),
(3, 'POS2406000003', '168', 'nun', 'KoKo', '123', 'uploads/Screenshot 2023-08-02 214129.png', 'Software,Network', 'Medium', 'open', '3,4', '2024-06-12 13:13:28', NULL, '123'),
(4, 'POS2406000004', '168', 'nun', 'KoKo', '123', 'uploads/Screenshot 2024-02-02 140413.png', 'NetworkUnassigned', 'High', 'on_hold', '3, 4', '2024-06-12 13:14:54', NULL, '123'),
(5, 'POS2406000005', '168', 'nun', 'KoKo', '168', 'uploads/Screenshot 2023-08-02 214129.png', 'SoftwareHardware', 'High', 'on_hold', '3, 4', '2024-06-12 13:15:54', NULL, '123'),
(6, 'POS2406000006', '168', 'nun', 'KoKo', '133', 'uploads/Screenshot 2024-03-19 001556.png', 'Software , Hardware', 'Medium', 'pending_vender', '3, 4', '2024-06-12 13:16:39', NULL, '123'),
(9, 'POS2406000009', '168', 'nun', 'KoKo', '123', 'uploads/Screenshot 2023-08-02 214129.png', 'Software,Hardware', 'Medium', 'in_progress', '3,4', '2024-06-12 13:25:23', NULL, '123'),
(11, 'POS2406000010', '168', 'nun', 'KoKo', '123', 'uploads/Screenshot 2024-02-02 134014.png', 'Network', 'Medium', 'close', '3, 4', '2024-06-12 13:34:24', '2024-06-12 13:34:24', '123'),
(12, 'POS2406000011', '168', 'nun', 'KoKo', '123', 'uploads/Screenshot 2024-01-30 155757.png', 'Hardware, Network, Dispenser, Unassigned', 'Medium', 'pending_vender', '3, 4', '2024-06-12 13:36:31', NULL, '123'),
(13, 'POS2406000012', '168', 'nun', 'KoKo', '123', 'uploads/Screenshot 2023-08-02 214129.png', 'Hardware, Network', 'Medium', 'pending_vender', '3, 4', '2024-06-12 13:38:12', NULL, '123'),
(14, 'POS2406000013', '168', 'nun', 'KoKo', '123', 'uploads/Screenshot 2024-02-02 134306.png', 'Hardware', 'Low', 'pending_vender', '3, 4', '2024-06-12 13:39:56', NULL, '123'),
(15, 'POS2406000014', '168', 'nun', 'KoKo', '123', 'uploads/Screenshot 2024-02-02 134014.png', 'Software', 'High', 'pending_vender', '3, 4', '2024-06-12 13:40:38', NULL, '123'),
(16, 'POS2406000015', '168', 'nun', 'KoKo', '123', 'uploads/Screenshot 2024-02-02 134159.png', 'Software, Hardware', 'Low', 'pending_vender', '3, 4', '2024-06-12 13:44:01', NULL, '123'),
(17, 'POS2406000016', '168', 'nun', 'KoKo', '123', 'uploads/1.png', 'Network', 'Low', 'in_progress', '3', '2024-06-12 13:46:57', NULL, '123'),
(18, 'POS2406000017', '168', 'nun', 'KoKo', '123', 'uploads/Screenshot 2023-08-02 214129.png', 'Network', 'Low', 'open', '3', '2024-06-12 14:07:56', NULL, '123'),
(19, 'POS2406000018', '168', 'nun', 'KoKo', '១២៣', 'uploads/Screenshot 2024-02-02 134014.png', 'Software', 'Low', 'open', '3,4', '2024-06-12 14:09:25', NULL, '១២៣'),
(20, 'POS2407000001', '168', 'nun', 'KoKo', '213', '', 'Hardware', 'High', 'pending_vender', '3,4', '2024-07-12 14:24:51', NULL, '123'),
(21, 'POS2406000019', '168', 'nun', 'KoKo', '123', 'uploads/Screenshot 2024-02-02 134014.png', 'Software,Hardware,Network', 'High', 'in_progress', '3,4', '2024-06-12 14:26:01', NULL, '123'),
(22, 'POS2406000020', '168', 'nun', 'KoKo', '123', 'uploads/Screenshot 2024-02-02 134159.png', 'Software,Hardware', 'High', 'pending_vender', '3,4', '2024-06-12 14:26:51', NULL, '123'),
(23, 'POS2406000021', '168', 'nun', 'KoKo', '123', 'uploads/Screenshot 2024-02-02 134014.png', 'Software', 'Low', 'on_hold', '3,4', '2024-06-12 14:27:52', NULL, '123');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `users_id` int(11) NOT NULL,
  `users_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `code` mediumint(50) NOT NULL,
  `status` text NOT NULL,
  `rules_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`users_id`, `users_name`, `email`, `password`, `code`, `status`, `rules_id`) VALUES
(3, 'zion', 'broakzinll29@gmail.com', '$2y$10$9pOYGJb4.FAK40rYHRs1YOcpEN/pbjeZvMpwOTniyxf82r0aUYYZi', 0, '1', 1421),
(4, 'nun', 'amazonkh4@gmail.com', '$2y$10$9vP5DcM1S0sOuvZ1CBvUs.5Q2oKG6JIl.wA8Z/XihwYCz1KrYDoUK', 0, '1', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users_rules`
--

CREATE TABLE `tbl_users_rules` (
  `rules_id` int(11) NOT NULL,
  `users_id` int(11) DEFAULT NULL,
  `rules_name` varchar(255) DEFAULT NULL,
  `add_status` tinyint(4) DEFAULT NULL,
  `edit_status` tinyint(4) DEFAULT NULL,
  `delete_status` tinyint(4) DEFAULT NULL,
  `menu_status` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_users_rules`
--

INSERT INTO `tbl_users_rules` (`rules_id`, `users_id`, `rules_name`, `add_status`, `edit_status`, `delete_status`, `menu_status`, `status`) VALUES
(1421, 1, 'nn', 1, 1, 1, '1', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_station`
--
ALTER TABLE `tbl_station`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `station_id` (`station_id`);

--
-- Indexes for table `tbl_ticket`
--
ALTER TABLE `tbl_ticket`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_id` (`ticket_id`),
  ADD KEY `station_id` (`station_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`users_id`),
  ADD KEY `rules_id` (`rules_id`);

--
-- Indexes for table `tbl_users_rules`
--
ALTER TABLE `tbl_users_rules`
  ADD PRIMARY KEY (`rules_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_station`
--
ALTER TABLE `tbl_station`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_ticket`
--
ALTER TABLE `tbl_ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_users_rules`
--
ALTER TABLE `tbl_users_rules`
  MODIFY `rules_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1422;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_ticket`
--
ALTER TABLE `tbl_ticket`
  ADD CONSTRAINT `tbl_ticket_ibfk_1` FOREIGN KEY (`station_id`) REFERENCES `tbl_station` (`station_id`);

--
-- Constraints for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD CONSTRAINT `tbl_users_ibfk_1` FOREIGN KEY (`rules_id`) REFERENCES `tbl_users_rules` (`rules_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
