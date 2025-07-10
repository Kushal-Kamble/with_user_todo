-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3309
-- Generation Time: Jul 10, 2025 at 01:29 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `with_user_todo`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `priority` enum('Low','Medium','High') DEFAULT 'Medium',
  `completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `title`, `description`, `start_date`, `deadline`, `priority`, `completed`) VALUES
(1, 5, 'API', 'Test kr rha hooo bhai', '2025-07-08', '2025-07-08', 'Medium', 1),
(5, 9, 'News Project with the help of ai', 'React', '2025-07-08', '2025-07-10', 'High', 1),
(9, 11, 'Meditation every day I is good for helth', 'It is good for helth', '2025-07-08', '2025-07-10', 'High', 1),
(10, 14, 'React api  project', 'test', '2025-07-09', '2025-07-11', 'High', 1),
(11, 5, 'React Js', 'zzz', '2025-07-09', '2025-07-11', 'High', 1),
(13, 5, 'Bhai', 'sss', '2025-07-10', '2025-07-12', 'High', 1),
(14, 5, 'Newsletter Projects', 'MITSDE', '2025-07-10', '2025-07-12', 'High', 1),
(15, 5, 'Node js', 'hero', '2025-07-10', '2025-07-12', 'High', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','manager','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(5, 'Kushal Kamble', 'kushal.kamble@mitsde.com', '$2y$10$IVH4p4zBlg5ywn.DuBkrLeXFq60WASyM7zghwNsmeBzq26HlhehWm', 'user'),
(9, 'vishal', 'kushal.kamble1806@gmail.com', '$2y$10$hl6uUCEGoCWEmYq7torK9.euclHWIPpHpmQ40b5XsEJHrLygshirG', 'user'),
(11, 'Ashwini', 'ashwini@gmail.com', '$2y$10$oa76krBtfOwREVEXMyGuuebT2lOwBD4q.nLM33w4k84jpejJY1FAe', 'user'),
(14, 'Salman Khan', 'salman@gmail.com', '$2y$10$j5g4LVMT6VQkINUrW/8kIOsa3.in8GWcFr6mMazU7Jg6CojLfu.Tq', 'manager'),
(16, 'ranu', 'ranu@gmail.com', '$2y$10$3VrxDSmvlYBiQH1qx/v37ufi29OY4zO0K6YWoO.J62f7FsNRcKUzG', 'user'),
(17, 'Nitin Zadpe', 'nitin.zadpe@mitsde.com', '$2y$10$7FU3GOvsoIZRqhFahEKCiO.xN3FqgoqpOZ.BfFbdNFz6EBinqNPvC', 'admin'),
(18, 'Kushal Kamble', 'admin@gmail.com', '$2y$10$RCDjbiIPsaVXZzismJblgO3R710buhiA.FNz6WNkqCyxWEnncoauO', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
