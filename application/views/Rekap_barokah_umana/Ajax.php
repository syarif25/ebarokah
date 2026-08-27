<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
    <!-- Datatable -->
    <script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>

	<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>

    <script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>

<script>
    var table;

    // -------------------------------------------------------
    // Fungsi format nominal ke Rupiah (client-side)
    // Data dari server sudah diformat, ini dipakai untuk footer
    // -------------------------------------------------------
    function formatRupiah(angka) {
        if (angka === 0 || angka === '0') return 'Rp 0';
        var number_string = Math.round(angka).toString();
        var split = number_string.split('.');
        var sisa  = split[0].length % 3;
        var rupiah = split[0].substr(0, sisa);
        var ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        if (ribuan) {
            var separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return 'Rp ' + rupiah;
    }

    // -------------------------------------------------------
    // Fungsi: parse angka dari string Rupiah (untuk footer SUM)
    // Contoh: "Rp 1.500.000" -> 1500000
    // -------------------------------------------------------
    function parseRupiah(str) {
        if (!str || str === '-') return 0;
        return parseInt(str.replace(/[^0-9]/g, ''), 10) || 0;
    }

    // -------------------------------------------------------
    // Muat daftar periode ke dropdown dari endpoint AJAX
    // -------------------------------------------------------
    function loadPeriode() {
        $.getJSON("<?php echo site_url('Rekap_barokah_umana/get_periode') ?>", function(data) {
            var selectBulan = $('#filter_bulan');
            var selectTahun = $('#filter_tahun');
            
            selectBulan.find('option:not(:first)').remove();
            selectTahun.find('option:not(:first)').remove();
            
            if (data.length === 0) {
                selectBulan.append('<option value="" disabled>Kosong</option>');
                selectTahun.append('<option value="" disabled>Kosong</option>');
                return;
            }
            
            var uniqueBulan = [];  // [{num, nama}]
            var uniqueTahun = [];  // [tahun]
            var bulanKeys   = [];  // track num_bulan sudah masuk
            
            $.each(data, function(i, row) {
                if (bulanKeys.indexOf(row.num_bulan) === -1) {
                    bulanKeys.push(row.num_bulan);
                    uniqueBulan.push({ num: row.num_bulan, nama: row.nama_bulan });
                }
                if (uniqueTahun.indexOf(row.tahun) === -1) {
                    uniqueTahun.push(row.tahun);
                }
            });

            // Urutkan bulan secara numerik
            uniqueBulan.sort(function(a, b) { return parseInt(a.num) - parseInt(b.num); });
            // Urutkan tahun dari terbaru ke terlama
            uniqueTahun.sort(function(a, b) { return b - a; });
            
            $.each(uniqueBulan, function(i, bln) {
                selectBulan.append('<option value="' + bln.num + '">' + bln.nama + '</option>');
            });
            
            $.each(uniqueTahun, function(i, thn) {
                selectTahun.append('<option value="' + thn + '">' + thn + '</option>');
            });
            
            selectBulan.niceSelect('update');
            selectTahun.niceSelect('update');
        });
    }

    // -------------------------------------------------------
    // Hitung dan tampilkan footer total
    // -------------------------------------------------------
    function hitungFooter(api) {
        if (typeof api === 'undefined' || !api.data().any()) {
            $('#footer_struktural').text('-');
            $('#footer_mengajar').text('-');
            $('#footer_satpam').text('-');
            $('#footer_kepanitiaan').text('-');
            $('#footer_jumlah').text('-');
            return;
        }

        var totalStr = 0, totalMgj = 0, totalSat = 0, totalKep = 0, totalAll = 0;

        api.rows({ search: 'applied' }).data().each(function(row) {
            totalStr += parseRupiah(row.struktural);
            totalMgj += parseRupiah(row.mengajar);
            totalSat += parseRupiah(row.satpam);
            totalKep += parseRupiah(row.kepanitiaan);
            totalAll += parseRupiah(row.jumlah);
        });

        $('#footer_struktural').text(formatRupiah(totalStr));
        $('#footer_mengajar').text(formatRupiah(totalMgj));
        $('#footer_satpam').text(formatRupiah(totalSat));
        $('#footer_kepanitiaan').text(formatRupiah(totalKep));
        $('#footer_jumlah').text(formatRupiah(totalAll));
    }

    // -------------------------------------------------------
    // Inisialisasi DataTables
    // -------------------------------------------------------
    function initTable(bulan, tahun, lembaga) {
        // Hancurkan instance lama jika ada
        if (table) {
            table.destroy();
        }

        table = $('#tabel_rekap').DataTable({
            "ajax": {
                "url"  : "<?php echo site_url('Rekap_barokah_umana/data_list') ?>",
                "type" : "POST",
                "data" : { bulan: bulan, tahun: tahun, id_lembaga: lembaga }
            },
            "columns": [
                { "data": "no", "width": "4%",  "className": "text-center" },
                { "data": "nama", "width": "14%" },
                { "data": "struktural", "width": "10%", "className": "text-end" },
                { "data": "mengajar", "width": "10%", "className": "text-end" },
                { "data": "satpam", "width": "10%", "className": "text-end" },
                { "data": "kepanitiaan", "width": "10%", "className": "text-end" },
                { "data": "jumlah", "width": "10%", "className": "text-end fw-bold text-success" },
                { "data": "keterangan", "width": "22%" },
                { "data": "aksi", "width": "10%", "className": "text-center" }
            ],
            "columnDefs": [
                { "orderable": false, "targets": [8] }
            ],
            "paging"  : true,
            "searching": true,
            "ordering": true,
            "order"   : [],
            "language": {
                "search"       : "Cari:",
                "lengthMenu"   : "Tampilkan _MENU_ data",
                "info"         : "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                "infoEmpty"    : "Tidak ada data",
                "zeroRecords"  : "Data tidak ditemukan",
                "paginate"     : { "first": "Awal", "last": "Akhir", "next": "&raquo;", "previous": "&laquo;" }
            },
            "drawCallback": function(settings) {
                var api = this.api();
                hitungFooter(api);
            }
        });
    }

    // -------------------------------------------------------
    // Muat Dropdown Lembaga via AJAX
    // -------------------------------------------------------
    function loadLembaga() {
        $.ajax({
            url: "<?php echo site_url('Rekap_barokah_umana/get_lembaga') ?>",
            type: "GET",
            dataType: "json",
            success: function(data) {
                var selectLembaga = $('#filter_lembaga');
                selectLembaga.empty();
                selectLembaga.append('<option value="">Semua Lembaga</option>');
                
                $.each(data, function(i, row) {
                    selectLembaga.append('<option value="' + row.id_lembaga + '">' + row.nama_lembaga + '</option>');
                });
                
                // Update plugin nice-select agar UI diperbarui
                selectLembaga.niceSelect('update');
            }
        });
    }

    // -------------------------------------------------------
    // Event: tombol Tampilkan diklik
    // -------------------------------------------------------
    $(function() {
        // Muat dropdown saat halaman siap
        loadPeriode();
        loadLembaga();

        $('#btn_tampilkan').on('click', function() {
            var bln = $('#filter_bulan').val();
            var thn = $('#filter_tahun').val();
            var lmb = $('#filter_lembaga').val();
            if (!bln || !thn) {
                toastr.warning('Pilih bulan dan tahun terlebih dahulu.', 'Peringatan', {
                    positionClass : "toast-top-right",
                    timeOut       : 3000,
                    closeButton   : true
                });
                return;
            }
            initTable(bln, thn, lmb);
        });

        // Event listener untuk tombol Detail (Digital Payslip)
        $(document).on('click', '.btn-detail', function() {
            var nik = $(this).data('nik');
            var bulan = $(this).data('bulan');
            var tahun = $(this).data('tahun');
            
            // Tampilkan loading text di modal
            $('#detailHeaderProfil').html('<div class="text-center"><div class="spinner-border text-primary"></div><p>Memuat profil...</p></div>');
            $('#detailContainerStruktural').empty();
            $('#detailContainerMengajar').empty();
            $('#detailContainerSatpam').empty();
            $('#detailContainerKepanitiaan').empty();
            $('#detailBlockTotal').empty();
            
            $('#modalDetailUmana').modal('show');

            $.ajax({
                url: "<?php echo site_url('Rekap_barokah_umana/get_detail_rincian') ?>",
                type: "POST",
                data: { nik: nik, bulan: bulan, tahun: tahun },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        // Header Profil
                        var lembagaTerpilih = $('#filter_lembaga option:selected').text();
                        var headerHtml = '<div class="text-center mb-2">' +
                                         '<h4 class="font-weight-bold text-dark mb-1">' + res.nama + '</h4>' +
                                         '<span class="badge badge-info badge-pill me-2">NIK: ' + nik + '</span>' +
                                         '<span class="badge badge-light badge-pill">' + bulan + ' ' + tahun + '</span><br>' +
                                         '<small class="text-muted d-block mt-2">Lembaga Filter: ' + lembagaTerpilih + '</small>' +
                                         '</div>';
                        $('#detailHeaderProfil').html(headerHtml);

                        // Card Struktural
                        var sumStr = 0;
                        var strHtml = '<div class="card border-0 shadow-sm"><div class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center"><h6 class="mb-0 text-white"><i class="fas fa-sitemap me-2"></i> Barokah Struktural</h6><span class="font-weight-bold" id="sumStrTitle"></span></div><ul class="list-group list-group-flush">';
                        if (res.struktural && res.struktural.length > 0) {
                            $.each(res.struktural, function(i, st) {
                                var val = parseFloat(st.nominal_struktural) || 0;
                                sumStr += val;
                                strHtml += '<li class="list-group-item d-flex justify-content-between align-items-center py-2">' +
                                           '<div class="d-flex align-items-start">' +
                                           '<div><h6 class="mb-0 text-dark">' + (st.nama_lembaga || '-') + '</h6><small class="text-muted">' + (st.jabatan || '-') + '</small></div></div>' +
                                           '<span class="font-weight-bold text-dark">' + formatRupiah(val) + '</span></li>';
                            });
                        } else {
                            strHtml += '<li class="list-group-item text-center text-muted py-2"><small>Tidak ada data</small></li>';
                        }
                        strHtml += '</ul></div>';
                        $('#detailContainerStruktural').html(strHtml);
                        $('#sumStrTitle').text(formatRupiah(sumStr));

                        // Card Mengajar
                        var sumMgj = 0;
                        var mgjHtml = '<div class="card border-0 shadow-sm"><div class="card-header bg-success text-white py-2 d-flex justify-content-between align-items-center"><h6 class="mb-0 text-white"><i class="fas fa-chalkboard-teacher me-2"></i> Barokah Pengajar</h6><span class="font-weight-bold" id="sumMgjTitle"></span></div><ul class="list-group list-group-flush">';
                        if (res.mengajar && res.mengajar.length > 0) {
                            $.each(res.mengajar, function(i, mg) {
                                var val = parseFloat(mg.diterima) || 0;
                                sumMgj += val;
                                mgjHtml += '<li class="list-group-item d-flex justify-content-between align-items-center py-2">' +
                                           '<div class="d-flex align-items-center">' +
                                           '<h6 class="mb-0 text-dark">' + (mg.nama_lembaga || '-') + '</h6></div>' +
                                           '<span class="font-weight-bold text-dark">' + formatRupiah(val) + '</span></li>';
                            });
                        } else {
                            mgjHtml += '<li class="list-group-item text-center text-muted py-2"><small>Tidak ada data</small></li>';
                        }
                        mgjHtml += '</ul></div>';
                        $('#detailContainerMengajar').html(mgjHtml);
                        $('#sumMgjTitle').text(formatRupiah(sumMgj));

                        // Satpam Placeholder
                        var satHtml = '<div class="card border-0 shadow-sm"><div class="card-header bg-danger text-white py-2 d-flex justify-content-between align-items-center"><h6 class="mb-0 text-white"><i class="fas fa-shield-alt me-2"></i> Barokah Satpam</h6><span class="font-weight-bold">Rp 0</span></div><ul class="list-group list-group-flush"><li class="list-group-item text-center text-muted py-2"><small>Tidak ada data</small></li></ul></div>';
                        $('#detailContainerSatpam').html(satHtml);

                        // Kepanitiaan Placeholder
                        var kepHtml = '<div class="card border-0 shadow-sm"><div class="card-header bg-warning text-white py-2 d-flex justify-content-between align-items-center"><h6 class="mb-0 text-white"><i class="fas fa-users-cog me-2"></i> Barokah Kepanitiaan</h6><span class="font-weight-bold">Rp 0</span></div><ul class="list-group list-group-flush"><li class="list-group-item text-center text-muted py-2"><small>Tidak ada data</small></li></ul></div>';
                        $('#detailContainerKepanitiaan').html(kepHtml);

                        // Block Total
                        var totalHtml = '<div class="alert alert-success d-flex justify-content-between align-items-center shadow-sm mb-0"><h5 class="mb-0 font-weight-bold text-dark">Total Keseluruhan</h5><h4 class="mb-0 font-weight-bold">' + formatRupiah(res.total) + '</h4></div>';
                        $('#detailBlockTotal').html(totalHtml);
                    } else {
                        toastr.error(res.message || 'Gagal memuat rincian', 'Error');
                        $('#modalDetailUmana').modal('hide');
                    }
                },
                error: function() {
                    toastr.error('Terjadi kesalahan koneksi', 'Error');
                    $('#modalDetailUmana').modal('hide');
                }
            });
        });
    });
</script>
