CREATE TABLE IF NOT EXISTS `usulan_perubahan_tmt` (
  `id_usulan` INT(11) NOT NULL AUTO_INCREMENT,
  `id_penempatan` INT(11) NOT NULL,
  `id_pengirim` INT(11) NOT NULL COMMENT 'ID Pengguna pembuat usulan',
  `tmt_lama` DATE NULL DEFAULT NULL,
  `tmt_baru` DATE NOT NULL,
  `rank_lama` INT(11) NULL DEFAULT NULL COMMENT 'Estimasi MP/Rank sebelum diubah',
  `rank_baru` INT(11) NOT NULL COMMENT 'Estimasi MP/Rank yang diajukan',
  `file_dokumen` VARCHAR(255) NOT NULL COMMENT 'Path ke file upload SK/bukti',
  `keterangan_usulan` TEXT NULL DEFAULT NULL,
  `catatan_reviewer` TEXT NULL DEFAULT NULL,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `tanggal_usulan` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tanggal_validasi` DATETIME NULL DEFAULT NULL,
  `divalidasi_oleh` INT(11) NULL DEFAULT NULL COMMENT 'ID Pengguna (SuperAdmin/SDM) yang ACC',
  PRIMARY KEY (`id_usulan`),
  INDEX `idx_fk_penempatan` (`id_penempatan`),
  CONSTRAINT `fk_usulan_penempatan` FOREIGN KEY (`id_penempatan`) REFERENCES `penempatan` (`id_penempatan`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_usulan_pengirim` FOREIGN KEY (`id_pengirim`) REFERENCES `pengguna` (`id_pengguna`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Handle duplicate column safely or run without if exists (ignore errors if exists)
-- ALTER TABLE `kehadiran_lembaga` ADD COLUMN `catatan_umum_pimpinan` TEXT NULL DEFAULT NULL COMMENT 'Catatan evaluasi global untuk lembaga periode ini' AFTER `jumlah_total`;
-- ALTER TABLE `total_barokah` ADD COLUMN `catatan_khusus_umana` TEXT NULL DEFAULT NULL COMMENT 'Catatan revisi barokah spesifik untuk sdr bersangkutan' AFTER `diterima`;
