ALTER TABLE `usulan_perubahan_tmt`
ADD COLUMN `tunj_kel_lama` VARCHAR(5) NULL DEFAULT NULL AFTER `rank_baru`,
ADD COLUMN `tunj_kel_baru` VARCHAR(5) NULL DEFAULT NULL AFTER `tunj_kel_lama`,
ADD COLUMN `tunj_anak_lama` VARCHAR(10) NULL DEFAULT NULL AFTER `tunj_kel_baru`,
ADD COLUMN `tunj_anak_baru` VARCHAR(10) NULL DEFAULT NULL AFTER `tunj_anak_lama`,
ADD COLUMN `tunj_mp_lama` VARCHAR(10) NULL DEFAULT NULL AFTER `tunj_anak_baru`,
ADD COLUMN `tunj_mp_baru` VARCHAR(10) NULL DEFAULT NULL AFTER `tunj_mp_lama`,
ADD COLUMN `kehormatan_lama` VARCHAR(5) NULL DEFAULT NULL AFTER `tunj_mp_baru`,
ADD COLUMN `kehormatan_baru` VARCHAR(5) NULL DEFAULT NULL AFTER `kehormatan_lama`;
