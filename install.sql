-- Generation Time: Jul 19, 2022 at 09:57 AM
-- Server version: 8.0.29-0ubuntu0.20.04.3
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clever`
--

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `id` int NOT NULL,
  `type` int NOT NULL,
  `token` varchar(8) NOT NULL,
  `datetime_start` datetime NOT NULL,
  `status` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_players`
--

CREATE TABLE `game_players` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `id_game` int NOT NULL,
  `ip_on_submit` varchar(255) NOT NULL,
  `status` int NOT NULL,
  `score` int NOT NULL,
  `data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempt`
--

CREATE TABLE `login_attempt` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `date_time` datetime NOT NULL,
  `failed_login_count` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `persistent_login`
--

CREATE TABLE `persistent_login` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `serial` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `status` int NOT NULL,
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `mail_hash` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `protected_key` text NOT NULL,
  `mail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `failed_login_count` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `game_players`
--
ALTER TABLE `game_players`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempt`
--
ALTER TABLE `login_attempt`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `persistent_login`
--
ALTER TABLE `persistent_login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `game`
--
ALTER TABLE `game`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `game_players`
--
ALTER TABLE `game_players`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `login_attempt`
--
ALTER TABLE `login_attempt`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `persistent_login`
--
ALTER TABLE `persistent_login`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
