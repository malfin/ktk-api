-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:8889
-- Время создания: Янв 28 2025 г., 13:52
-- Версия сервера: 5.7.39
-- Версия PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `ktk-api`
--

-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `file_type` enum('assignment','submission') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','reviewed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `files`
--

INSERT INTO `files` (`id`, `user_id`, `session_id`, `file_type`, `file_path`, `uploaded_at`, `status`) VALUES
(1, 1, 1, 'assignment', '/path/to/file1.pdf', '2025-01-26 11:51:31', 'pending'),
(2, 2, 2, 'submission', '/path/to/file2.docx', '2025-01-26 11:51:31', 'reviewed'),
(3, 3, 3, 'assignment', '/path/to/file3.xlsx', '2025-01-26 11:51:31', 'pending'),
(4, 1, 1, 'assignment', '/uploads/homework/Знакомство с фреймворком.pdf', '2025-01-26 12:53:46', 'pending');

-- --------------------------------------------------------

--
-- Структура таблицы `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action`, `details`, `created_at`, `ip_address`) VALUES
(1, 1, 'Login', 'User logged in', '2025-01-26 11:51:35', '192.168.1.1'),
(2, 2, 'Logout', 'User logged out', '2025-01-26 11:51:35', '192.168.1.2'),
(3, 3, 'Upload File', 'User uploaded file3.xlsx', '2025-01-26 11:51:35', '192.168.1.3'),
(4, 1, 'Создал занятие', '11', '2025-01-26 13:51:58', '::1'),
(5, 1, 'Создал занятие', '11', '2025-01-26 13:52:42', '::1'),
(6, 1, 'Создал занятие', '11', '2025-01-26 13:53:32', NULL),
(7, 1, 'Создал занятие', '11', '2025-01-26 13:54:15', NULL),
(8, 1, 'Занятие успешно обновлено!', '12313123123', '2025-01-28 13:39:02', '::1'),
(9, 1, 'Занятие успешно удалено!', '12313123123', '2025-01-28 13:39:18', '::1');

-- --------------------------------------------------------

--
-- Структура таблицы `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1737892237),
('m250126_105012_create_roles_table', 1737892238),
('m250126_105044_create_session_types_table', 1737892238),
('m250126_105106_create_users_table', 1737892238),
('m250126_105240_create_sessions_table', 1737892238),
('m250126_105309_create_user_sessions_table', 1737892238),
('m250126_105415_create_files_table', 1737892238),
('m250126_105448_create_logs_table', 1737892238);

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'user');

-- --------------------------------------------------------

--
-- Структура таблицы `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `type_id` int(11) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `max_participants` int(11) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `sessions`
--

INSERT INTO `sessions` (`id`, `title`, `description`, `type_id`, `start_date`, `end_date`, `max_participants`, `created_at`, `updated_at`) VALUES
(1, 'Основы программирования', 'Description for session 1', 1, '2025-01-26 16:51:18', '2025-01-27 16:51:18', 50, '2025-01-26 11:51:18', '2025-01-26 12:06:10'),
(2, 'Мастер-класс по веб-дизайну', 'Description for session 2', 2, '2025-01-27 16:51:18', '2025-01-28 16:51:18', 30, '2025-01-26 11:51:18', '2025-01-26 12:06:20'),
(3, 'Индвидуальное обучение программированию на Python', 'Description for session 3', 3, '2025-01-28 16:51:18', '2025-01-29 16:51:18', 20, '2025-01-26 11:51:18', '2025-01-26 12:06:43'),
(4, 'Основы программирования', 'Курс для начинающих', 1, '2025-02-01 10:00:00', '2025-02-01 12:00:00', 30, '2025-01-26 13:03:14', '2025-01-26 13:03:14'),
(6, 'Основы веб-разработки', 'Обновленный курс для начинающих', 1, '2025-02-01 10:00:00', '2025-02-01 12:00:00', 45, '2025-01-26 13:04:56', '2025-01-26 13:20:43'),
(7, 'Основы программирования', 'Курс для начинающих', 1, '2025-02-01 10:00:00', '2025-02-01 12:00:00', 30, '2025-01-26 13:08:05', '2025-01-26 13:08:05'),
(8, 'Основы веб-разработки', 'Обновленный курс для начинающих', 3, '2025-02-01 10:00:00', '2025-02-01 12:00:00', 30, '2025-01-26 13:08:24', '2025-01-26 13:33:28'),
(10, '11', 'Курс для начинающих', 3, '2025-02-01 10:00:00', '2025-02-01 12:00:00', 30, '2025-01-26 13:51:40', '2025-01-26 13:51:40'),
(11, '11', 'Курс для начинающих', 3, '2025-02-01 10:00:00', '2025-02-01 12:00:00', 30, '2025-01-26 13:51:58', '2025-01-26 13:51:58'),
(12, '11', 'Курс для начинающих', 3, '2025-02-01 10:00:00', '2025-02-01 12:00:00', 30, '2025-01-26 13:52:42', '2025-01-26 13:52:42'),
(13, '11', 'Курс для начинающих', 3, '2025-02-01 10:00:00', '2025-02-01 12:00:00', 30, '2025-01-26 13:53:32', '2025-01-26 13:53:32');

-- --------------------------------------------------------

--
-- Структура таблицы `session_types`
--

CREATE TABLE `session_types` (
  `id` int(11) NOT NULL,
  `name` enum('circle','master_class','individual') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `session_types`
--

INSERT INTO `session_types` (`id`, `name`) VALUES
(1, 'circle'),
(2, 'master_class'),
(3, 'individual');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `patronymic` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `auth_key` varchar(32) DEFAULT NULL,
  `access_token` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `patronymic`, `email`, `password_hash`, `role_id`, `birth_date`, `auth_key`, `access_token`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Alexey', 'Smirnov', 'Ivanovich', 'user@prof.ru', '$2y$13$GueY.FTNRABz9EXjmexKveUNXK/0ss1TqwNzetzOWTUMVr6X1OTwe', 1, '2001-02-15', NULL, '1GzbeWOCDYEbzFxegbNvx3N940dLRVMP', 'active', '2025-01-26 11:50:52', '2025-01-28 13:26:54'),
(2, 'Alexey', 'Smirnov', 'Ivanovich', 'user1@prof.ru', '$2y$13$/7vSDdCtRzJWJV1d/MpQo.Xx0zXsgtPWWnr89lzoSoF40Mdddv3lW', 1, '2001-02-15', NULL, 'fdG4eGFLVDMUntNgsK8r4pyoDBQIWK5q', 'active', '2025-01-26 11:51:01', '2025-01-26 13:35:15'),
(3, 'Alexey', 'Smirnov', 'Ivanovich', 'user2@prof.ru', '$2y$13$MVPI.8x32NqOlcp93dAsHOl5PIT1Go59hivHjLfycca8xQ/njzg6q', 2, '2001-02-15', NULL, 'RWltdTUat7JjPYEK7-WoYyObssI65sYX', 'active', '2025-01-26 11:51:05', '2025-01-26 11:51:05');

-- --------------------------------------------------------

--
-- Структура таблицы `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `status` enum('pending','approved','canceled') DEFAULT 'pending',
  `registered_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `session_id`, `status`, `registered_at`) VALUES
(1, 1, 1, 'approved', '2025-01-26 11:51:20'),
(2, 2, 2, 'pending', '2025-01-26 11:51:20'),
(3, 3, 3, 'canceled', '2025-01-26 11:51:20');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk-files-user_id` (`user_id`),
  ADD KEY `fk-files-session_id` (`session_id`);

--
-- Индексы таблицы `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk-logs-user_id` (`user_id`);

--
-- Индексы таблицы `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk-sessions-type_id` (`type_id`);

--
-- Индексы таблицы `session_types`
--
ALTER TABLE `session_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk-users-role_id` (`role_id`);

--
-- Индексы таблицы `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk-user_sessions-user_id` (`user_id`),
  ADD KEY `fk-user_sessions-session_id` (`session_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `session_types`
--
ALTER TABLE `session_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `fk-files-session_id` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk-files-user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `fk-logs-user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `fk-sessions-type_id` FOREIGN KEY (`type_id`) REFERENCES `session_types` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk-users-role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `fk-user_sessions-session_id` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk-user_sessions-user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
