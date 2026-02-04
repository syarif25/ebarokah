<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Maintenance_updates extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Protect this controller in production
        // if (!$this->input->is_cli_request()) exit('CLI only');
    }

    public function run_danru_migration()
    {
        echo "<h1>Running Danru Migration...</h1>";

        // 1. Create master_honor_satpam table
        $sql1 = "CREATE TABLE IF NOT EXISTS `master_honor_satpam` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `nama_honor` varchar(100) NOT NULL,
            `nominal` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        if ($this->db->query($sql1)) {
            echo "<p>[OK] Table master_honor_satpam created/exists.</p>";
        } else {
            echo "<p>[ERR] Failed to create table: " . $this->db->error()['message'] . "</p>";
        }

        // 2. Insert initial data
        $check = $this->db->get_where('master_honor_satpam', ['nama_honor' => 'Danru'])->row();
        if (!$check) {
            $this->db->insert('master_honor_satpam', ['nama_honor' => 'Danru', 'nominal' => 100000]);
            echo "<p>[OK] Inserted initial Danru nominal (100.000).</p>";
        } else {
            echo "<p>[SKIP] Danru nominal already exists.</p>";
        }

        // 3. Add column is_danru to satpam table
        if (!$this->db->field_exists('is_danru', 'satpam')) {
            $sql3 = "ALTER TABLE `satpam` ADD COLUMN `is_danru` ENUM('0','1') NOT NULL DEFAULT '0' COMMENT '1=Danru, 0=Anggota'";
            if ($this->db->query($sql3)) {
                echo "<p>[OK] Column is_danru added to satpam.</p>";
            } else {
                echo "<p>[ERR] Add column satpam failed: " . $this->db->error()['message'] . "</p>";
            }
        } else {
            echo "<p>[SKIP] Column is_danru already exists in satpam.</p>";
        }

        // 4. Add column nominal_danru to total_barokah_satpam table
        if (!$this->db->field_exists('nominal_danru', 'total_barokah_satpam')) {
            $sql4 = "ALTER TABLE `total_barokah_satpam` ADD COLUMN `nominal_danru` INT(11) NOT NULL DEFAULT 0 AFTER `jumlah_konsumsi`";
            if ($this->db->query($sql4)) {
                echo "<p>[OK] Column nominal_danru added to total_barokah_satpam.</p>";
            } else {
                echo "<p>[ERR] Add column total_barokah_satpam failed: " . $this->db->error()['message'] . "</p>";
            }
        } else {
             echo "<p>[SKIP] Column nominal_danru already exists in total_barokah_satpam.</p>";
        }

        echo "<h3>Migration Completed. Please delete this file/controller after use.</h3>";
    }
}
