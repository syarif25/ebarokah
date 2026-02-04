-- ============================================
-- KEHADIRAN STRUKTURAL: ADD IZIN & SAKIT FIELDS
-- ============================================

-- Step 1: Add new columns
ALTER TABLE `kehadiran` 
ADD COLUMN `jumlah_izin` INT(10) DEFAULT 0 COMMENT 'Jumlah hari/shift izin' AFTER `jumlah_hadir`,
ADD COLUMN `jumlah_sakit` INT(10) DEFAULT 0 COMMENT 'Jumlah hari/shift sakit' AFTER `jumlah_izin`;

-- Step 2: Update existing records (set default 0)
UPDATE `kehadiran` 
SET `jumlah_izin` = 0, `jumlah_sakit` = 0 
WHERE `jumlah_izin` IS NULL OR `jumlah_sakit` IS NULL;

-- Step 3: Verify changes
SELECT 
    COLUMN_NAME, 
    COLUMN_TYPE, 
    IS_NULLABLE, 
    COLUMN_DEFAULT, 
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'kehadiran' 
  AND COLUMN_NAME IN ('jumlah_hadir', 'jumlah_izin', 'jumlah_sakit')
ORDER BY ORDINAL_POSITION;

-- Expected result:
-- jumlah_hadir  INT(5)   YES  NULL  (existing)
-- jumlah_izin   INT(10)  YES  0     Jumlah hari/shift izin
-- jumlah_sakit  INT(10)  YES  0     Jumlah hari/shift sakit
