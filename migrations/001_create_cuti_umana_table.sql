-- Migration: Create cuti_umana table
-- Created: 2026-01-02
-- Purpose: Track umana leave periods with start/end dates and automatic expiration alerts

CREATE TABLE IF NOT EXISTS `cuti_umana` (
  `id_cuti` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nik` VARCHAR(30) NOT NULL,
  `jenis_cuti` ENUM('Cuti 50%', 'Cuti 100%') NOT NULL,
  `tanggal_mulai` DATE NOT NULL,
  `tanggal_selesai` DATE NOT NULL,
  `keterangan` TEXT NULL,
  `status` ENUM('Aktif', 'Selesai', 'Dibatalkan') DEFAULT 'Aktif',
  `dibuat_oleh` VARCHAR(30) NULL,
  `dibuat_pada` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `diupdate_pada` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`nik`) REFERENCES `umana`(`nik`) ON DELETE CASCADE,
  INDEX `idx_nik` (`nik`),
  INDEX `idx_status` (`status`),
  INDEX `idx_tanggal` (`tanggal_mulai`, `tanggal_selesai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Add comments to columns for documentation
ALTER TABLE `cuti_umana` 
  MODIFY COLUMN `id_cuti` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
  MODIFY COLUMN `nik` VARCHAR(30) NOT NULL COMMENT 'NIK umana yang cuti (FK to umana table)',
  MODIFY COLUMN `jenis_cuti` ENUM('Cuti 50%', 'Cuti 100%') NOT NULL COMMENT 'Tipe cuti: 50% atau 100%',
  MODIFY COLUMN `tanggal_mulai` DATE NOT NULL COMMENT 'Tanggal mulai cuti',
  MODIFY COLUMN `tanggal_selesai` DATE NOT NULL COMMENT 'Tanggal selesai cuti (max 3 bulan dari mulai)',
  MODIFY COLUMN `keterangan` TEXT NULL COMMENT 'Keterangan tambahan tentang cuti',
  MODIFY COLUMN `status` ENUM('Aktif', 'Selesai', 'Dibatalkan') DEFAULT 'Aktif' COMMENT 'Status cuti saat ini',
  MODIFY COLUMN `dibuat_oleh` VARCHAR(30) NULL COMMENT 'NIK admin yang membuat record',
  MODIFY COLUMN `dibuat_pada` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp pembuatan',
  MODIFY COLUMN `diupdate_pada` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp update terakhir';
