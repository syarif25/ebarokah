<!-- Required vendors -->
<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>

<!-- Datatable -->
<script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Bootstrap Datepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js"></script>

<script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>
<script src="<?php echo base_url() ?>assets/js/demo.js"></script>

<script>
    var table;
    var selectedBulan = '';
    var selectedTahun = '';
    
    $(document).ready(function() {
        // Initialize DataTable (empty initially)
        table = $('#tabel_per_bulan').DataTable({
            "processing": true,
            "serverSide": false,
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "columnDefs": [
                {
                    "targets": [0, 3], // No, Aksi
                    "className": "text-center"
                },
                {
                    "targets": [2], // Jumlah Nominal
                    "className": "text-right"
                },
                {
                    "targets": [3], // Aksi column
                    "orderable": false
                }
            ],
            "language": {
                "emptyTable": "Silakan pilih periode untuk menampilkan data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "loadingRecords": "Memuat...",
                "processing": "Memproses...",
                "search": "Cari:",
                "zeroRecords": "Tidak ada data yang cocok",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            }
        });

        // Initialize Bootstrap Datepicker as Month Picker
        $('#periode_selector').datepicker({
            format: "MM yyyy",
            startView: "months", 
            minViewMode: "months",
            language: 'id',
            autoclose: true,
            todayHighlight: true,
            orientation: "bottom auto"
        }).on('changeDate', function(e) {
            // Extract month and year from selected date
            var date = e.date;
            var monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                              "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            var month = monthNames[date.getMonth()];
            var year = date.getFullYear();
            
            // Set hidden fields
            $('#selected_bulan').val(month);
            $('#selected_tahun').val(year);
            selectedBulan = month;
            selectedTahun = year;
            
            console.log('Periode selected:', month, year);
        });
    });

    function loadData() {
        var bulan = $('#selected_bulan').val();
        var tahun = $('#selected_tahun').val();
        
        if (!bulan || !tahun) {
            toastr.warning('Silakan pilih periode terlebih dahulu', 'Peringatan');
            return;
        }
        
        // Show loading
        table.clear().draw();
        toastr.info('Memuat data periode ' + bulan + ' ' + tahun + '...', 'Loading');
        
        // Fetch data via AJAX
        $.ajax({
            url: '<?php echo site_url('laporan_lembaga/data_per_bulan')?>',
            type: 'POST',
            data: {
                bulan: bulan,
                tahun: tahun
            },
            dataType: 'json',
            success: function(response) {
                // Clear table
                table.clear();
                
                if (response.data && response.data.length > 0) {
                    // Add rows to table
                    table.rows.add(response.data).draw();
                    
                    // Update summary cards
                    updateSummaryCards(response.data, bulan, tahun);
                    
                    // Show summary cards
                    $('#summary_cards').slideDown();
                    
                    toastr.success('Data berhasil dimuat: ' + response.data.length + ' lembaga', 'Berhasil');
                } else {
                    table.draw();
                    $('#summary_cards').slideUp();
                    toastr.info('Tidak ada data untuk periode ' + bulan + ' ' + tahun, 'Info');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                toastr.error('Gagal memuat data', 'Error');
                table.draw();
            }
        });
    }

    function updateSummaryCards(data, bulan, tahun) {
        var totalLembaga = data.length;
        var totalBarokah = 0;
        
        // Calculate total
        data.forEach(function(row) {
            // Extract number from formatted string (e.g., "Rp 1.500.000" -> 1500000)
            var nominalStr = row[2]; // Jumlah Nominal column
            var numericValue = nominalStr.replace(/[^0-9]/g, '');
            totalBarokah += parseInt(numericValue) || 0;
        });
        
        var rataRata = totalLembaga > 0 ? totalBarokah / totalLembaga : 0;
        
        // Update cards
        $('#card_total_lembaga').text(totalLembaga);
        $('#card_total_barokah').text(formatRupiah(totalBarokah));
        $('#card_rata_rata').text(formatRupiah(rataRata));
        $('#card_periode').text(bulan + ' ' + tahun);
    }

    function formatRupiah(angka) {
        var number_string = angka.toString().replace(/[^,\d]/g, ''),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        
        if (ribuan) {
            var separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return 'Rp ' + rupiah;
    }

    function refreshTable() {
        if (selectedBulan && selectedTahun) {
            loadData();
        } else {
            toastr.info('Silakan pilih periode terlebih dahulu', 'Info');
        }
    }

    function resetPeriode() {
        $('#periode_selector').datepicker('clearDates');
        $('#selected_bulan').val('');
        $('#selected_tahun').val('');
        selectedBulan = '';
        selectedTahun = '';
        
        table.clear().draw();
        $('#summary_cards').slideUp();
        
        toastr.success('Periode berhasil direset', 'Berhasil');
    }
</script>
