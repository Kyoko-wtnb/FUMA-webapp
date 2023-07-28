-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Jul 13, 2023 at 03:29 PM
-- Server version: 8.0.33
-- PHP Version: 8.1.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fuma_new_tmp`
--
CREATE DATABASE fuma_new_tmp;
USE fuma_new_tmp;
-- --------------------------------------------------------

--
-- Table structure for table `celltype`
--

CREATE TABLE `celltype` (
  `jobID` int NOT NULL,
  `title` text COLLATE utf8mb4_general_ci,
  `email` text COLLATE utf8mb4_general_ci NOT NULL,
  `snp2gene` int DEFAULT NULL,
  `snp2geneTitle` text COLLATE utf8mb4_general_ci,
  `status` text COLLATE utf8mb4_general_ci,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` int NOT NULL,
  `connection` text COLLATE utf8mb4_general_ci NOT NULL,
  `queue` text COLLATE utf8mb4_general_ci NOT NULL,
  `payload` text COLLATE utf8mb4_general_ci NOT NULL,
  `failed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `exception` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gene2func`
--

CREATE TABLE `gene2func` (
  `jobID` int NOT NULL,
  `title` text COLLATE utf8mb4_general_ci,
  `email` text COLLATE utf8mb4_general_ci NOT NULL,
  `snp2gene` int DEFAULT NULL,
  `snp2geneTitle` text COLLATE utf8mb4_general_ci,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `JobMonitor`
--

CREATE TABLE `JobMonitor` (
  `jobID` int NOT NULL,
  `created_at` datetime NOT NULL,
  `started_at` datetime NOT NULL,
  `completed_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `PublicResults`
--

CREATE TABLE `PublicResults` (
  `id` int NOT NULL,
  `jobID` int DEFAULT NULL,
  `g2f_jobID` int DEFAULT NULL,
  `title` text COLLATE utf8mb4_general_ci NOT NULL,
  `author` text COLLATE utf8mb4_general_ci NOT NULL,
  `email` text COLLATE utf8mb4_general_ci NOT NULL,
  `phenotype` text COLLATE utf8mb4_general_ci,
  `publication` text COLLATE utf8mb4_general_ci,
  `sumstats_link` text COLLATE utf8mb4_general_ci,
  `sumstats_ref` text COLLATE utf8mb4_general_ci,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` datetime NOT NULL,
  `update_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SubmitJobs`
--

CREATE TABLE `SubmitJobs` (
  `jobID` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Not set',
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Not set',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'NEW',
  `remove_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `remember_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `celltype`
--
ALTER TABLE `celltype`
  ADD PRIMARY KEY (`jobID`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gene2func`
--
ALTER TABLE `gene2func`
  ADD PRIMARY KEY (`jobID`);

--
-- Indexes for table `JobMonitor`
--
ALTER TABLE `JobMonitor`
  ADD PRIMARY KEY (`jobID`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_token_index` (`token`),
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `PublicResults`
--
ALTER TABLE `PublicResults`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `SubmitJobs`
--
ALTER TABLE `SubmitJobs`
  ADD PRIMARY KEY (`jobID`),
  ADD KEY `submitjobs_email_index` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `celltype`
--
ALTER TABLE `celltype`
  MODIFY `jobID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gene2func`
--
ALTER TABLE `gene2func`
  MODIFY `jobID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `JobMonitor`
--
ALTER TABLE `JobMonitor`
  MODIFY `jobID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `PublicResults`
--
ALTER TABLE `PublicResults`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `SubmitJobs`
--
ALTER TABLE `SubmitJobs`
  MODIFY `jobID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
