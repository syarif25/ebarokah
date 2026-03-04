ALTER TABLE usulan_perubahan_tmt ADD COLUMN id_reviewer INT(11) NULL DEFAULT NULL AFTER id_penempatan, ADD COLUMN tanggal_verifikasi DATETIME NULL DEFAULT NULL AFTER catatan_reviewer;
