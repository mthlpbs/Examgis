-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2024 at 11:20 AM
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
-- Database: `examgis`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmark`
--

CREATE TABLE `bookmark` (
  `user_id` varchar(20) NOT NULL,
  `course_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookmark`
--

INSERT INTO `bookmark` (`user_id`, `course_id`) VALUES
('9JK7FUzNMNLXOO0DIkBR', '\r\nWarning:  Undefine');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` varchar(20) NOT NULL,
  `paper_id` varchar(20) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `comment` varchar(1000) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `number` int(10) NOT NULL,
  `message` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `id` varchar(20) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `thumb` varchar(100) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'deactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `tutor_id`, `title`, `description`, `thumb`, `date`, `status`) VALUES
('A3ckw6mKwGnyB6i3l3Ub', 'XN8nhaHT3HiGCj0e93rx', 'Computer Science', 'Computer science is a fascinating field that encompasses the study of computation, information, and automation.', 'LdycrAUMnYEfwte30HKa.svg', '2024-07-07', 'active'),
('mQ3V7WtHjpnpVPw6Ls4i', 'Tm1D6EsN7MYlQJ8KENlk', 'Software Eng', 'the branch of computer science that deals with the design, development, testing, and maintenance of software applications.', 'zH9M8yJFqjNJZGd9gZJ1.jpg', '2024-07-12', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `user_id` varchar(20) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `paper_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paper`
--

CREATE TABLE `paper` (
  `id` varchar(20) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `course_id` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `pdf` varchar(100) NOT NULL,
  `thumb` varchar(100) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'deactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paper`
--

INSERT INTO `paper` (`id`, `tutor_id`, `course_id`, `title`, `description`, `pdf`, `thumb`, `date`, `status`) VALUES
('mvKxZzkgMhtWBDdJtjE4', 'Tm1D6EsN7MYlQJ8KENlk', 'mQ3V7WtHjpnpVPw6Ls4i', 'Batch 01 Sem 02 - Software Engineering', 'lerum,50', 'nrMrwUrrUXCWNgHVhuSr.pdf', 'c1SxVwayqHRQfOhulnu0.jpeg', '2024-07-12', 'active'),
('RVkoug0X19fTRPZPAEnw', 'XN8nhaHT3HiGCj0e93rx', 'A3ckw6mKwGnyB6i3l3Ub', 'dedd', 'ddd', 'ecRSDHYaQqgSjTMvLHeP.pdf', 'q4KV9oTwbjIT1VFPsxV0.png', '2024-07-28', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `tutors`
--

CREATE TABLE `tutors` (
  `id` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `profession` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutors`
--

INSERT INTO `tutors` (`id`, `name`, `profession`, `email`, `password`, `image`) VALUES
('Tm1D6EsN7MYlQJ8KENlk', 'Chamod Abeywickramage', 'engineer', 'chamodtest@gmail.com', '7288edd0fc3ffcbe93a0cf06e3568e28521687bc', 'ydbfunaG4rdy5r5Pg840.jpg'),
('XN8nhaHT3HiGCj0e93rx', 'Mithila Prabashwara', 'teacher', 'mithila@gmail.com', '441ebf65b5cb62cb6e5f661713ca97a76e8e83a3', 'vtWovB7GlDAyquJ5AT5f.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `image`) VALUES,
('QNgtoJ8H7SfDHZMag6hc', 'avatar', 'mithila1@gmail.com', '441ebf65b5cb62cb6e5f661713ca97a76e8e83a3', 'WmucM3CS5DKNtxkm3tpT.jpg'),
('26AeKFVZl05ZsFso431g', 'avatar 11', 'mithi55la@gmail.com', '9b65d4edc669347a0d84229a27f58f4b8ff40c53', '732oyZsdeKKGmkxtwvT9.jpg'),
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
