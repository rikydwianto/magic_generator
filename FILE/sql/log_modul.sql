-- Log table for Center Meeting module (center_input.php / center_proses.php)
CREATE TABLE IF NOT EXISTS `log_center_meeting` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_cabang` VARCHAR(100) NOT NULL,
  `id_cabang` VARCHAR(50) NULL,
  `file_name` VARCHAR(255) NULL,
  `total_center` INT UNSIGNED NOT NULL DEFAULT 0,
  `total_hari` INT UNSIGNED NOT NULL DEFAULT 0,
  `keterangan` ENUM('proses','selesai','gagal') NOT NULL DEFAULT 'proses',
  `mulai` TIME NULL,
  `selesai` TIME NULL,
  `pesan` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edited_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_log_center_meeting_cabang` (`nama_cabang`),
  KEY `idx_log_center_meeting_status` (`keterangan`),
  KEY `idx_log_center_meeting_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Log table for Permintaan Disburse module (permintaandisburse.php)
CREATE TABLE IF NOT EXISTS `log_permintaan_disburse` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_cabang` VARCHAR(100) NOT NULL,
  `file_name` VARCHAR(255) NULL,
  `tanggal_awal` DATE NULL,
  `tanggal_akhir` DATE NULL,
  `total_row` INT UNSIGNED NOT NULL DEFAULT 0,
  `keterangan` ENUM('proses','selesai','gagal') NOT NULL DEFAULT 'proses',
  `mulai` TIME NULL,
  `selesai` TIME NULL,
  `pesan` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edited_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_log_permintaan_cabang` (`nama_cabang`),
  KEY `idx_log_permintaan_tanggal` (`tanggal_awal`, `tanggal_akhir`),
  KEY `idx_log_permintaan_status` (`keterangan`),
  KEY `idx_log_permintaan_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
