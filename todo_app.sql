-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 15, 2025 lúc 06:40 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `todo_app`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `task_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(50) NOT NULL COMMENT 'created, updated, deleted, completed, etc.',
  `description` text DEFAULT NULL,
  `old_value` text DEFAULT NULL COMMENT 'Giá trị cũ (JSON)',
  `new_value` text DEFAULT NULL COMMENT 'Giá trị mới (JSON)',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `task_id`, `action`, `description`, `old_value`, `new_value`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 3, 6, 'created', 'Created task: TLCN', NULL, '{\"title\": \"TLCN\", \"status\": \"in_progress\", \"priority\": \"high\"}', NULL, NULL, '2025-11-14 12:11:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `color` varchar(7) DEFAULT '#3B82F6' COMMENT 'Mã màu hex (ví dụ: #3B82F6)',
  `icon` varchar(50) DEFAULT NULL COMMENT 'Tên icon (nếu dùng icon library)',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `user_id`, `name`, `color`, `icon`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Công việc', '#3B82F6', '💼', 1, '2025-11-14 05:57:21', '2025-11-15 05:27:21'),
(2, 1, 'Cá nhân', '#10B981', '👤', 2, '2025-11-14 05:57:21', '2025-11-15 05:27:21'),
(3, 1, 'Học tập', '#8B5CF6', '📚', 3, '2025-11-14 05:57:21', '2025-11-15 05:27:21'),
(4, 1, 'Sức khỏe', '#EF4444', '❤️', 4, '2025-11-14 05:57:21', '2025-11-15 05:27:21'),
(5, 1, 'Mua sắm', '#F59E0B', '🛒', 5, '2025-11-14 05:57:21', '2025-11-15 05:27:21'),
(6, 3, 'Bai tap PHP', '#3bf751', '💻', 0, '2025-11-15 05:16:57', '2025-11-15 05:27:21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `task_id` int(10) UNSIGNED DEFAULT NULL,
  `type` varchar(50) NOT NULL COMMENT 'task_due_soon, task_overdue, task_completed, etc.',
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `subtasks`
--

CREATE TABLE `subtasks` (
  `id` int(10) UNSIGNED NOT NULL,
  `task_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tags`
--

CREATE TABLE `tags` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `color` varchar(7) DEFAULT '#6B7280' COMMENT 'Mã màu hex',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tags`
--

INSERT INTO `tags` (`id`, `user_id`, `name`, `color`, `created_at`) VALUES
(1, 1, 'Khẩn cấp', '#DC2626', '2025-11-14 05:57:21'),
(2, 1, 'Dự án', '#2563EB', '2025-11-14 05:57:21'),
(3, 1, 'Họp', '#7C3AED', '2025-11-14 05:57:21'),
(4, 1, 'Viết báo cáo', '#059669', '2025-11-14 05:57:21'),
(5, 3, 'Tạm thời', '#6b7280', '2025-11-15 05:18:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tasks`
--

CREATE TABLE `tasks` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `due_time` time DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `is_important` tinyint(1) DEFAULT 0 COMMENT 'Đánh dấu quan trọng (starred)',
  `completed_at` datetime DEFAULT NULL,
  `display_order` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị (cho drag & drop)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `category_id`, `title`, `description`, `due_date`, `due_time`, `priority`, `status`, `is_important`, `completed_at`, `display_order`, `created_at`, `updated_at`) VALUES
(2, 1, 1, 'Họp team dự án website', 'Thảo luận tiến độ và phân công công việc tuần tới', '2025-11-15', NULL, 'medium', 'completed', 0, '2025-11-15 12:29:09', 0, '2025-11-14 05:57:21', '2025-11-15 05:29:09'),
(3, 1, 2, 'Đi siêu thị mua đồ', 'Mua thực phẩm cho cả tuần', '2025-11-16', NULL, 'low', 'pending', 0, NULL, 0, '2025-11-14 05:57:21', '2025-11-14 05:57:21'),
(5, 1, 4, 'Tập gym', 'Buổi tập cardio và tạ', '2025-11-15', NULL, 'medium', 'cancelled', 0, NULL, 0, '2025-11-14 05:57:21', '2025-11-15 05:30:48'),
(6, 3, NULL, 'TLCN', 'Web eCo', '2025-12-13', '09:59:00', 'high', 'in_progress', 1, NULL, 0, '2025-11-14 12:11:55', '2025-11-15 05:16:07'),
(7, 3, 6, 'Haha 15-11', 'Haha 15-11', '2025-11-18', '02:20:00', 'high', 'in_progress', 1, NULL, 0, '2025-11-15 05:18:45', '2025-11-15 05:18:45');

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `tasks_full_info`
-- (See below for the actual view)
--
CREATE TABLE `tasks_full_info` (
`id` int(10) unsigned
,`user_id` int(10) unsigned
,`category_id` int(10) unsigned
,`title` varchar(255)
,`description` text
,`due_date` date
,`due_time` time
,`priority` enum('low','medium','high','urgent')
,`status` enum('pending','in_progress','completed','cancelled')
,`is_important` tinyint(1)
,`completed_at` datetime
,`display_order` int(11)
,`created_at` timestamp
,`updated_at` timestamp
,`username` varchar(50)
,`category_name` varchar(50)
,`category_color` varchar(7)
,`tags` mediumtext
,`subtask_count` bigint(21)
,`completed_subtask_count` decimal(22,0)
);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `task_attachments`
--

CREATE TABLE `task_attachments` (
  `id` int(10) UNSIGNED NOT NULL,
  `task_id` int(10) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(10) UNSIGNED DEFAULT 0 COMMENT 'Kích thước file (bytes)',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'MIME type',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `task_tags`
--

CREATE TABLE `task_tags` (
  `id` int(10) UNSIGNED NOT NULL,
  `task_id` int(10) UNSIGNED NOT NULL,
  `tag_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `task_tags`
--

INSERT INTO `task_tags` (`id`, `task_id`, `tag_id`, `created_at`) VALUES
(3, 2, 2, '2025-11-14 05:57:21'),
(4, 2, 3, '2025-11-14 05:57:21'),
(6, 7, 5, '2025-11-15 05:18:45');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Mật khẩu đã băm bằng password_hash()',
  `full_name` varchar(100) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL COMMENT 'Đường dẫn ảnh đại diện',
  `theme_preference` enum('light','dark','auto') DEFAULT 'light',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `avatar`, `theme_preference`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@todoapp.com', '$2y$10$FXo584G3LtOPxJJjkFzHGuAPOE1j9zdXahHi9PtX1lw9LRtkY7W8K', 'Administrator', NULL, 'light', 1, NULL, '2025-11-14 05:57:21', '2025-11-14 06:47:45'),
(2, 'john_doe', 'john@example.com', '$2y$10$FXo584G3LtOPxJJjkFzHGuAPOE1j9zdXahHi9PtX1lw9LRtkY7W8K', 'John Doe', NULL, 'dark', 1, NULL, '2025-11-14 05:57:21', '2025-11-14 06:49:11'),
(3, 'ngochuy_hya', 'nguyenhuypm1@gmail.com', '$2y$10$FXo584G3LtOPxJJjkFzHGuAPOE1j9zdXahHi9PtX1lw9LRtkY7W8K', NULL, NULL, 'light', 1, NULL, '2025-11-14 06:44:39', '2025-11-14 06:44:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `user_task_statistics`
-- (See below for the actual view)
--
CREATE TABLE `user_task_statistics` (
`user_id` int(10) unsigned
,`username` varchar(50)
,`total_tasks` bigint(21)
,`completed_tasks` decimal(22,0)
,`pending_tasks` decimal(22,0)
,`in_progress_tasks` decimal(22,0)
,`overdue_tasks` decimal(22,0)
,`important_tasks` decimal(22,0)
);

-- --------------------------------------------------------

--
-- Cấu trúc cho view `tasks_full_info`
--
DROP TABLE IF EXISTS `tasks_full_info`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `tasks_full_info`  AS SELECT `t`.`id` AS `id`, `t`.`user_id` AS `user_id`, `t`.`category_id` AS `category_id`, `t`.`title` AS `title`, `t`.`description` AS `description`, `t`.`due_date` AS `due_date`, `t`.`due_time` AS `due_time`, `t`.`priority` AS `priority`, `t`.`status` AS `status`, `t`.`is_important` AS `is_important`, `t`.`completed_at` AS `completed_at`, `t`.`display_order` AS `display_order`, `t`.`created_at` AS `created_at`, `t`.`updated_at` AS `updated_at`, `u`.`username` AS `username`, `c`.`name` AS `category_name`, `c`.`color` AS `category_color`, group_concat(distinct `tg`.`name` order by `tg`.`name` ASC separator ', ') AS `tags`, count(distinct `st`.`id`) AS `subtask_count`, sum(case when `st`.`is_completed` = 1 then 1 else 0 end) AS `completed_subtask_count` FROM (((((`tasks` `t` join `users` `u` on(`t`.`user_id` = `u`.`id`)) left join `categories` `c` on(`t`.`category_id` = `c`.`id`)) left join `task_tags` `tt` on(`t`.`id` = `tt`.`task_id`)) left join `tags` `tg` on(`tt`.`tag_id` = `tg`.`id`)) left join `subtasks` `st` on(`t`.`id` = `st`.`task_id`)) GROUP BY `t`.`id` ;

-- --------------------------------------------------------

--
-- Cấu trúc cho view `user_task_statistics`
--
DROP TABLE IF EXISTS `user_task_statistics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_task_statistics`  AS SELECT `u`.`id` AS `user_id`, `u`.`username` AS `username`, count(`t`.`id`) AS `total_tasks`, sum(case when `t`.`status` = 'completed' then 1 else 0 end) AS `completed_tasks`, sum(case when `t`.`status` = 'pending' then 1 else 0 end) AS `pending_tasks`, sum(case when `t`.`status` = 'in_progress' then 1 else 0 end) AS `in_progress_tasks`, sum(case when `t`.`due_date` < curdate() and `t`.`status` <> 'completed' then 1 else 0 end) AS `overdue_tasks`, sum(case when `t`.`is_important` = 1 then 1 else 0 end) AS `important_tasks` FROM (`users` `u` left join `tasks` `t` on(`u`.`id` = `t`.`user_id`)) GROUP BY `u`.`id`, `u`.`username` ;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_task_id` (`task_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_category` (`user_id`,`name`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Chỉ mục cho bảng `subtasks`
--
ALTER TABLE `subtasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_task_id` (`task_id`);

--
-- Chỉ mục cho bảng `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_tag` (`user_id`,`name`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_due_date` (`due_date`),
  ADD KEY `idx_priority` (`priority`);

--
-- Chỉ mục cho bảng `task_attachments`
--
ALTER TABLE `task_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_task_id` (`task_id`);

--
-- Chỉ mục cho bảng `task_tags`
--
ALTER TABLE `task_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_task_tag` (`task_id`,`tag_id`),
  ADD KEY `idx_task_id` (`task_id`),
  ADD KEY `idx_tag_id` (`tag_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`);

--
-- Chỉ mục cho bảng `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_session_token` (`session_token`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `subtasks`
--
ALTER TABLE `subtasks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `task_attachments`
--
ALTER TABLE `task_attachments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `task_tags`
--
ALTER TABLE `task_tags`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_logs_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `subtasks`
--
ALTER TABLE `subtasks`
  ADD CONSTRAINT `subtasks_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `tags`
--
ALTER TABLE `tags`
  ADD CONSTRAINT `tags_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `task_attachments`
--
ALTER TABLE `task_attachments`
  ADD CONSTRAINT `task_attachments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `task_tags`
--
ALTER TABLE `task_tags`
  ADD CONSTRAINT `task_tags_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
