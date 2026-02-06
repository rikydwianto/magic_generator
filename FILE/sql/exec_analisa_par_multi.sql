CREATE TABLE IF NOT EXISTS `exec_analisa_par_multi` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session` VARCHAR(120) NOT NULL,
  `cabang` VARCHAR(100) DEFAULT NULL,
  `file_list` LONGTEXT NOT NULL,
  `total_files` INT UNSIGNED NOT NULL DEFAULT 0,
  `status` ENUM('','uploaded','processing','done','failed','proses_analisa','selesai') NOT NULL DEFAULT 'uploaded',
  `message` TEXT DEFAULT NULL,
  `uploaded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_exec_analisa_par_multi_session` (`session`),
  KEY `idx_exec_analisa_par_multi_status` (`status`),
  KEY `idx_exec_analisa_par_multi_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
