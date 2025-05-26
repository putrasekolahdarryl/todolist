-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2025 at 01:56 AM
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
-- Database: `todolist_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `todos_tabel`
--

CREATE TABLE `todos_tabel` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `task` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','expired') NOT NULL DEFAULT 'pending',
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `todos_tabel`
--

INSERT INTO `todos_tabel` (`id`, `user_id`, `task`, `status`, `deadline`, `created_at`, `completed`) VALUES
(64, 2, 'Vaksin Anjing', 'completed', '2025-05-24', '2025-05-22 00:46:06', 0),
(66, 2, 'Makan', 'completed', '2025-05-22', '2025-05-22 00:47:23', 0),
(67, 7, 'reksa berak', 'pending', '2025-05-23', '2025-05-22 00:49:42', 0),
(68, 8, 'ngoding', 'completed', '2025-05-23', '2025-05-22 22:53:29', 0),
(70, 9, 'acara lingkungan', 'pending', '2025-05-27', '2025-05-25 09:05:09', 0),
(71, 2, 'sasaaaaa', 'pending', '2025-05-29', '2025-05-26 16:25:56', 0),
(72, 2, 'sasa', 'pending', '2025-05-31', '2025-05-26 22:56:58', 0),
(73, 2, 'jajajaj', 'pending', '2025-05-29', '2025-05-26 23:24:46', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `full_name` varchar(255) NOT NULL,
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`full_name`, `id`, `email`, `password`, `profile_picture`) VALUES
('Carlos Darryl Xavier Immanuel', 1, 'putrasekolahdarryl@gmail.com', '$2y$10$AVL7MDGez6m5noR3T3BG1er/Z8snPQchDev2lUeZOqYzGM6/zkxwC', NULL),
('Martha Esa Manusa', 2, 'sasa@gmail.com', '$2y$10$UpEzj74WH4r2bT3S1hzIIuQjPC7gZEpR50NtkKrdi0sBpYWDwWz/S', '6834f1d07e28a_ultra.png'),
('vier', 3, 'vier@gmail.com', '$2y$10$euwHdu.0//abCelKX61kLO0lhVzNsMVP9vlzFEmIOUnjugMOhm/fC', NULL),
('xavier', 5, 'xavier@gmail.com', '$2y$10$L9kusofikJlpLCLzgtYIfODdUXMxbONEzmBGu6OmxmytgBjVmj1O6', NULL),
('rama anjing', 6, 'rama@gmail.com', '$2y$10$/SOdm5ADaZD0d7n6SGlEqO6qAYB0FEwEp.qA5b0zke5a4neM45eBG', NULL),
('Ryan Dana Putra', 7, 'ryan@gmail.com', '$2y$10$3F8JB6ZInR1NrEBvLKB7Vuir1GyVmHmp5OxVNw9NhpM8eecIY7/2W', NULL),
('Vier Darryl', 8, 'vierdarryl@gmail.com', '$2y$10$e/kIU7MzOehONlMYD5fY.OuPon42D7Kkur2etazuwcUpoIfaIT.xS', NULL),
('Herman Fernandez', 9, 'opa@gmail.com', '$2y$10$lehk1sJOsDNUNLrbs6jfe.oHaWe2T61ZlUspnpNzvy4fozoK49CMO', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `todos_tabel`
--
ALTER TABLE `todos_tabel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `todos_tabel`
--
ALTER TABLE `todos_tabel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
