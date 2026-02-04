# Panduan Deployment Sistem Cuti Umana

## 1. Database Migration

Jalankan SQL script berikut untuk membuat tabel `cuti_umana`:

```bash
# Login ke MySQL/MariaDB
mysql -u ebarokah_user -p ebarokah

# Atau via file
mysql -u ebarokah_user -p ebarokah < migrations/001_create_cuti_umana_table.sql
```

Atau copy-paste SQL dari file: `migrations/001_create_cuti_umana_table.sql`

**Verifikasi:**
```sql
SHOW TABLES LIKE 'cuti_umana';
DESCRIBE cuti_umana;
```

## 2. File yang Sudah Dibuat

### Backend:
✅ `/application/models/Cuti_model.php` - Model untuk operasi database  
✅ `/application/controllers/Cuti.php` - Controller CRUD dan validasi  
✅ `/application/controllers/Dashboard.php` - Added `get_cuti_akan_habis()` method

### Frontend:
✅ `/application/views/Cuti/Cuti.php` - Halaman utama  
✅ `/application/views/Cuti/Ajax.php` - JavaScript  
✅ `/application/views/Cuti/Css.php` - CSS includes

## 3. Integrasi Dashboard (Manual)

Tambahkan widget di `/application/views/Statistik.php`:

**Lokasi:** Setelah tabel "Jumlah Rekening Umana" (sekitar baris 155), tambahkan:

```php
		<!-- Widget Cuti Akan Habis -->
		<div class="col-xl-12">
			<div class="card">
				<div class="card-header border-0 pb-0">
					<h4 class="fs-20 font-w700 text-danger">
						<i class="fa fa-calendar-times"></i> Cuti Umana Akan Habis
					</h4>
					<span class="badge badge-rounded badge-warning">Perhatian!</span>
				</div>
				<div class="card-body pt-2">
					<div id="cuti-alert-container">
						<div class="text-center py-3">
							<div class="spinner-border text-primary" role="status">
								<span class="sr-only">Loading...</span>
							</div>
							<p class="text-muted mt-2">Memuat data...</p>
						</div>
					</div>
				</div>
			</div>
		</div>
```

Kemudian di `/application/views/Ajax.php`, tambahkan sebelum penutup `</script>`:

```javascript
// Load Cuti Akan Habis
function loadCutiAkanHabis() {
	$.ajax({
		url: "<?php echo site_url('Dashboard/get_cuti_akan_habis')?>",
		type: "GET",
		dataType: "JSON",
		success: function(data) {
			if(data.length > 0) {
				var html = '<div class="table-responsive"><table class="table table-hover table-sm">';
				html += '<thead><tr>';
				html += '<th>Nama</th>';
				html += '<th>Jenis</th>';
				html += '<th>Selesai</th>';
				html += '<th class="text-center">Sisa</th>';
				html += '</tr></thead><tbody>';
				
				$.each(data, function(i, item) {
					var nama = '';
					if(item.gelar_depan) nama += item.gelar_depan + ' ';
					nama += item.nama_lengkap;
					if(item.gelar_belakang) nama += ' ' + item.gelar_belakang;
					
					var jenisBadge = item.jenis_cuti == 'Cuti 100%' ? 
						'<span class="badge badge-danger badge-sm">100%</span>' : 
						'<span class="badge badge-warning badge-sm">50%</span>';
					
					var sisaBadge = '';
					if(item.sisa_hari <= 7) {
						sisaBadge = '<span class="badge badge-danger"><i class="fa fa-exclamation-triangle"></i> '+item.sisa_hari+' hari</span>';
					} else {
						sisaBadge = '<span class="badge badge-warning">'+item.sisa_hari+' hari</span>';
					}
					
					html += '<tr>';
					html += '<td><small>'+nama+'</small></td>';
					html += '<td>'+jenisBadge+'</td>';
					html += '<td><small>'+item.tanggal_selesai+'</small></td>';
					html += '<td class="text-center">'+sisaBadge+'</td>';
					html += '</tr>';
				});
				
				html += '</tbody></table></div>';
				html += '<div class="text-center mt-2">';
				html += '<a href="<?php echo site_url('cuti')?>" class="btn btn-primary btn-sm">Lihat Semua Cuti <i class="fa fa-arrow-right"></i></a>';
				html += '</div>';
			} else {
				html = '<div class="text-center py-4">';
				html += '<i class="fa fa-check-circle fa-3x text-success mb-3"></i>';
				html += '<p class="text-muted">Tidak ada cuti yang akan habis dalam 14 hari ke depan</p>';
				html += '</div>';
			}
			
			$('#cuti-alert-container').html(html);
		},
		error: function() {
			$('#cuti-alert-container').html(
				'<div class="alert alert-danger">Gagal memuat data cuti</div>'
			);
		}
	});
}

// Load when page ready
$(document).ready(function() {
	loadCutiAkanHabis();
	// Reload every 5 minutes
	setInterval(loadCutiAkanHabis, 300000);
});
```

## 4. Testing

### A. Test Akses Modul Cuti:
```
http://localhost/ebarokah/cuti
```

**Expected:** Halaman list cuti muncul dengan DataTable kosong

### B. Test Tambah Cuti:
1. Klik tombol "Tambah Cuti"
2. Pilih umana dari dropdown
3. Pilih jenis cuti (50% atau 100%)
4. Set tanggal mulai (hari ini atau lebih)
5. Set tanggal selesai (maksimal 90 hari dari mulai)
6. Submit

**Expected:**
- Data tersimpan
- Status umana di tabel `umana` terupdate
- Alert success muncul

### C. Test Validasi:
1. Coba tambah cuti > 90 hari  
   **Expected:** Error "Masa cuti tidak boleh lebih dari 3 bulan"

2. Coba tambah cuti untuk umana yang sudah ada cuti aktif  
   **Expected:** Error "Umana ini sedang memiliki cuti aktif"

### D. Test Dashboard Alert:
1. Buka dashboard petugas: `http://localhost/ebarokah/dashboard/petugas`
2. Scroll ke widget "Cuti Umana Akan Habis"

**Expected:**
- Jika ada cuti < 14 hari lagi: muncul list umana
- Jika tidak ada: "Tidak ada cuti yang akan habis"

### E. Test Multi-Role Sync (PENTING):
Pastikan umana yang punya jabatan ganda (Misal: Guru + Satpam) statusnya berubah semua.

1. Pilih umana yang punya data di tabel `penempatan`, `pengajar`, atau `satpam`.
2. Tambahkan Cuti Baru.
3. Cek database:
   ```sql
   SELECT status_aktif FROM umana WHERE nik = 'NIK_XXX';
   SELECT status FROM pengajar WHERE nik = 'NIK_XXX';
   SELECT status FROM penempatan WHERE nik = 'NIK_XXX';
   ```
   **Expected:** Semua kolom `status` harus berubah jadi "Cuti 50%" atau "Cuti 100%".

4. Batalkan/Selesaikan cuti.
   **Expected:** Semua kolom `status` harus kembali jadi "Aktif".

### F. Test Auto-complete:
1. Update manual di database, set `tanggal_selesai` ke kemarin
2. Akses halaman cuti atau dashboard

**Expected:**
- Status cuti otomatis berubah jadi "Selesai"
- Status umana kembali "Aktif"

## 5. Troubleshooting

### Error: Table 'cuti_umana' doesn't exist
**Solusi:** Jalankan migration SQL

### Error: Call to undefined method Cuti_model::...
**Solusi:** Clear cache CodeIgniter
```bash
rm -rf application/cache/*
```

### Widget dashboard tidak muncul
**Solusi:** Cek apakah kode di Ajax.php sudah ditambahkan dengan benar

### Dropdown umana kosong
**Solusi:** Pastikan ada data umana yang tidak sedang cuti di database

## 6. Fitur yang Sudah Diimplementasi

✅ CRUD Cuti (Create, Read, Update, Delete)  
✅ Validasi maksimal 3 bulan (90 hari)  
✅ Validasi duplikasi cuti aktif  
✅ Auto-update status umana  
✅ Auto-selesaikan cuti yang lewat tanggal  
✅ Dashboard alert cuti akan habis (14 hari)  
✅ Badge status warna-warni  
✅ Selesaikan/Batalkan cuti manual  
✅ Hitung sisa hari otomatis  
✅ Filter & search di DataTable

## 7. Catatan Penting

- Cuti otomatis selesai saat `tanggal_selesai` sudah lewat
- Status umana auto-update sesuai jenis cuti
- Dashboard widget refresh setiap 5 menit
- Semua tanggal menggunakan timezone server
- Maximum cuti: 90 hari (3 bulan)

## 8. Next Steps (Optional Enhancement)

- [ ] Export Excel/PDF untuk laporan cuti
- [ ] Notifikasi WhatsApp untuk cuti akan habis
- [ ] Upload dokumen pendukung cuti
- [ ] History log perubahan status
- [ ] Filter by periode di halaman cuti
