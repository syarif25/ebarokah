    <script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
    <script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
	<!-- Apex Chart -->
	<script src="<?php echo base_url() ?>assets/vendor/apexchart/apexchart.js"></script>
	
    <!-- Datatable -->
    <script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>
    <script src="<?php echo base_url() ?>assets/js/jquery.mask.js"></script>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Tambahkan ini sebelum script kehadiran.js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
$(document).ready(function() {
    // Global variables
    let chartPie, chartBar, chartLine, chartDonut, chartPotongan, chartTrend;
    let table;
    
    // Initialize
    init();
    
    function init() {
        // Init Select2
        $('#filterLembaga').select2({
            width: '100%',
            placeholder: "Pilih Lembaga"
        });

        loadSummaryData();
        loadCharts();
        initDataTable();
    }
    
    // Filter buttons
    $('#btnFilter').click(function() {
        loadSummaryData();
        loadCharts();
        table.ajax.reload();
    });
    
    $('#btnReset').click(function() {
        // Reset to default values (read from initial page load)
        $('#filterBulan').val('<?= $default_bulan ?>').trigger('change');
        $('#filterTahun').val('<?= $default_tahun ?>').trigger('change');
        $('#filterLembaga').val('semua').trigger('change');
        
        loadSummaryData();
        loadCharts();
        table.ajax.reload();
    });
    
    // Export Excel
    $('#btnExport').click(function() {
        const bulan = $('#filterBulan').val();
        const tahun = $('#filterTahun').val();
        const lembaga = $('#filterLembaga').val();
        
        window.location.href = '<?php echo base_url() ?>Dashboard_struktural/export_excel?bulan=' + bulan + '&tahun=' + tahun + '&lembaga=' + lembaga;
    });
    
    // Load Summary Data
    function loadSummaryData() {
        showLoading();
        
        $.ajax({
            url: '<?php echo base_url() ?>Dashboard_struktural/get_summary',
            type: 'POST',
            data: {
                bulan: $('#filterBulan').val(),
                tahun: $('#filterTahun').val(),
                lembaga: $('#filterLembaga').val()
            },
            dataType: 'json',
            success: function(data) {
                // Update cards
                $('#cardTotalBarokah').text(formatRupiah(data.total_barokah));
                $('#cardJumlahPegawai').text(data.jumlah_umana + ' Umana');
                $('#cardRataRata').text(data.umana_putra + ' Umana');
                $('#cardTrend').text(data.umana_putri + ' Umana');
                
                hideLoading();
            },
            error: function() {
                hideLoading();
                alert('Gagal memuat data summary');
            }
        });
    }
    
    // Load Charts
    function loadCharts() {
        // Only load charts that exist in the DOM
        loadDonutChart();
        loadBarChart();
        loadTrendChart();
        // Removed: loadPieChart, loadLineChart, loadKehadiranStats, loadPotonganChart as elements were removed from view
    }
    
    // Pie Chart - Distribusi per Lembaga
    function loadPieChart() {
        const ctxElement = document.getElementById('chartPie');
        if (!ctxElement) return; // Guard clause just in case
        
        $.ajax({
            url: '<?php echo base_url() ?>Dashboard_struktural/get_chart_data',
            type: 'POST',
            data: {
                type: 'pie',
                bulan: $('#filterBulan').val(),
                tahun: $('#filterTahun').val()
            },
            dataType: 'json',
            success: function(data) {
                const labels = data.map(item => item.nama_lembaga);
                const values = data.map(item => parseFloat(item.total_barokah));
                
                if (chartPie) chartPie.destroy();
                
                const ctx = document.getElementById('chartPie').getContext('2d');
                chartPie = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: [
                                '#667eea', '#764ba2', '#f093fb', '#f5576c',
                                '#4facfe', '#00f2fe', '#fa709a', '#fee140',
                                '#ff6b6b', '#ee5a6f', '#a8edea', '#fed6e3'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ': Rp ' + context.parsed.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    }
    
    // Bar Chart - Top 10 Lembaga
    function loadBarChart() {
        $.ajax({
            url: '<?php echo base_url() ?>Dashboard_struktural/get_chart_data',
            type: 'POST',
            data: {
                type: 'bar',
                bulan: $('#filterBulan').val(),
                tahun: $('#filterTahun').val()
            },
            dataType: 'json',
            success: function(data) {
                const labels = data.map(item => item.nama_lembaga);
                const values = data.map(item => parseFloat(item.total_barokah));
                
                if (chartBar) chartBar.destroy();
                
                const ctx = document.getElementById('chartBar').getContext('2d');
                chartBar = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Barokah',
                            data: values,
                            backgroundColor: 'rgba(102, 126, 234, 0.8)',
                            borderColor: 'rgba(102, 126, 234, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp ' + context.parsed.x.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    }
    
    // Trend Chart - 4 Month Trend
    function loadTrendChart() {
        $.ajax({
            url: '<?php echo base_url() ?>Dashboard_struktural/get_chart_data',
            type: 'POST',
            data: {
                type: 'trend',
                bulan: $('#filterBulan').val(),
                tahun: $('#filterTahun').val(),
                lembaga: $('#filterLembaga').val()
            },
            dataType: 'json',
            success: function(data) {
                const labels = data.map(item => item.bulan);
                const values = data.map(item => parseFloat(item.total_barokah));
                
                if (chartTrend) chartTrend.destroy();
                
                const ctx = document.getElementById('chartTrend').getContext('2d');
                
                // Create gradient
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(102, 126, 234, 0.5)');
                gradient.addColorStop(1, 'rgba(102, 126, 234, 0.05)');
                
                chartTrend = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Barokah',
                            data: values,
                            borderColor: '#667eea',
                            backgroundColor: gradient,
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3,
                            pointRadius: 5,
                            pointBackgroundColor: '#667eea',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Total: Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        });
    }

    
    // Line Chart - Trend 6 Bulan
    function loadLineChart() {
        $.ajax({
            url: '<?php echo base_url() ?>Dashboard_struktural/get_chart_data',
            type: 'POST',
            data: {
                type: 'line',
                tahun: $('#filterTahun').val()
            },
            dataType: 'json',
            success: function(data) {
                const labels = data.map(item => item.bulan);
                const values = data.map(item => parseFloat(item.total_barokah));
                
                if (chartLine) chartLine.destroy();
                
                const ctx = document.getElementById('chartLine').getContext('2d');
                chartLine = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Barokah',
                            data: values,
                            borderColor: 'rgba(102, 126, 234, 1)',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    }
    
    // Chart - Breakdown Komponen (Vertical Bar)
    function loadDonutChart() {
        $.ajax({
            url: '<?php echo base_url() ?>Dashboard_struktural/get_chart_data',
            type: 'POST',
            data: {
                type: 'donut', // Key kept same for controller compatibility
                bulan: $('#filterBulan').val(),
                tahun: $('#filterTahun').val(),
                lembaga: $('#filterLembaga').val()
            },
            dataType: 'json',
            success: function(data) {
                const labels = [
                    'Tunjab', 
                    'Kehadiran',
                    'Tunj. Keluarga', 
                    'Tunj. Anak', 
                    'TMP', 
                    'Kehormatan', 
                    'TBK'
                ];
                
                const values = [
                    parseFloat(data.total_tunjab || 0),
                    parseFloat(data.total_kehadiran || 0),
                    parseFloat(data.total_tunkel || 0),
                    parseFloat(data.total_tunj_anak || 0),
                    parseFloat(data.total_tmp || 0),
                    parseFloat(data.total_kehormatan || 0),
                    parseFloat(data.total_tbk || 0)
                ];
                
                if (chartDonut) chartDonut.destroy();
                
                const ctx = document.getElementById('chartDonut').getContext('2d');
                chartDonut = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Nominal Barokah',
                            data: values,
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.7)',   // Blue - Tunjab
                                'rgba(75, 192, 192, 0.7)',   // Teal - Kehadiran
                                'rgba(255, 159, 64, 0.7)',   // Orange - Tunkel
                                'rgba(255, 205, 86, 0.7)',   // Yellow - Tunj Anak
                                'rgba(201, 203, 207, 0.7)',  // Gray - TMP
                                'rgba(255, 99, 132, 0.7)',   // Red - Kehormatan
                                'rgba(54, 162, 235, 0.4)'    // Light Blue - TBK
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(255, 205, 86, 1)',
                                'rgba(201, 203, 207, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)'
                            ],
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + formatRupiah(context.parsed.y);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    }
    
    // Load Kehadiran Stats
    function loadKehadiranStats() {
        $.ajax({
            url: '<?php echo base_url() ?>Dashboard_struktural/get_chart_data',
            type: 'POST',
            data: {
                type: 'kehadiran',
                bulan: $('#filterBulan').val(),
                tahun: $('#filterTahun').val(),
                lembaga: $('#filterLembaga').val()
            },
            dataType: 'json',
            success: function(data) {
                let hadir = 0, sakit = 0, alfa = 0;
                
                data.forEach(item => {
                    if (item.id_kehadi == '1') hadir = item.jumlah_pegawai;
                    else if (item.id_kehadi == '2') sakit = item.jumlah_pegawai;
                    else if (item.id_kehadi == '3') alfa = item.jumlah_pegawai;
                });
                
                $('#kehadiranHadir').text(hadir + ' pegawai');
                $('#kehadiranSakit').text(sakit + ' pegawai');
                $('#kehadiranAlfa').text(alfa + ' pegawai');
            }
        });
    }
    
    // Load Potongan Chart
    function loadPotonganChart() {
        $.ajax({
            url: '<?php echo base_url() ?>Dashboard_struktural/get_chart_data',
            type: 'POST',
            data: {
                type: 'potongan',
                bulan: $('#filterBulan').val(),
                tahun: $('#filterTahun').val(),
                lembaga: $('#filterLembaga').val()
            },
            dataType: 'json',
            success: function(data) {
                const labels = data.map(item => item.nama_potongan);
                const values = data.map(item => parseFloat(item.total_potongan));
                
                if (chartPotongan) chartPotongan.destroy();
                
                const ctx = document.getElementById('chartPotongan').getContext('2d');
                chartPotongan = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Potongan',
                            data: values,
                            backgroundColor: 'rgba(255, 107, 107, 0.8)',
                            borderColor: 'rgba(255, 107, 107, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Rp ' + context.parsed.x.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + (value / 1000000).toFixed(1) + 'Jt';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    }
    
    // Initialize DataTable
    function initDataTable() {
        table = $('#tableDetail').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?php echo base_url() ?>Dashboard_struktural/get_table_data',
                type: 'POST',
                data: function(d) {
                    d.bulan = $('#filterBulan').val();
                    d.tahun = $('#filterTahun').val();
                    d.lembaga = $('#filterLembaga').val();
                }
            },
            columns: [
                { data: 0 },  // No
                { data: 1 },  // Bulan
                { data: 2 },  // Tahun
                { data: 3 },  // NIK
                { data: 4 },  // Nama
                { data: 5 },  // Lembaga
                { data: 6 },  // Jabatan
                { data: 7 },  // Tunjab
                { data: 8 },  // MP
                { data: 9 },  // Kehadiran
                { data: 10 }, // Nominal
                { data: 11 }, // Tunkel
                { data: 12 }, // Tunj Anak
                { data: 13 }, // TMP
                { data: 14 }, // Kehormatan
                { data: 15 }, // TBK
                { data: 16 }, // Potongan
                { data: 17 }, // Diterima
                { data: 18 }  // Tgl Kirim
            ],
            order: [[4, 'asc']], // Sort by Nama
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            scrollX: true // Enable horizontal scroll for many columns
        });
    }
    
    // Detail button click
    $(document).on('click', '.btn-detail', function() {
        const id = $(this).data('id');
        loadDetail(id);
    });
    
    // Load Detail for Modal
    function loadDetail(id) {
        $.ajax({
            url: '<?php echo base_url() ?>Dashboard_struktural/get_detail/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#modalNama').text(data.nama_lengkap);
                $('#modalNIK').text(data.nik);
                $('#modalLembaga').text(data.nama_lembaga);
                $('#modalJabatan').text(data.jabatan_lembaga);
                $('#modalPeriode').text(data.bulan + ' ' + data.tahun);
                
                $('#modalBank').text(data.nama_bank);
                $('#modalNoRek').text(data.nomor_rekening);
                $('#modalPenerima').text(data.nama_penerima);
                
                $('#modalGajiPokok').text(formatRupiah(data.barokah_pokok));
                $('#modalTunjKel').text(formatRupiah(data.tunj_kel));
                $('#modalTunjAnak').text(formatRupiah(data.tunj_anak));
                $('#modalTransport').text(formatRupiah(data.transport));
                $('#modalKehormatan').text(formatRupiah(data.kehormatan));
                $('#modalTBK').text(formatRupiah(data.tunj_beban_kerja));
                $('#modalTotalPendapatan').text(formatRupiah(data.total_pendapatan));
                
                // Potongan detail
                let potonganHtml = '<table class="table table-sm table-bordered">';
                if (data.detail_potongan && data.detail_potongan.length > 0) {
                    data.detail_potongan.forEach(item => {
                        potonganHtml += '<tr><td>' + item.nama_potongan + '</td><td class="text-end">' + formatRupiah(item.nominal_potongan) + '</td></tr>';
                    });
                } else {
                    potonganHtml += '<tr><td colspan="2" class="text-center">Tidak ada potongan</td></tr>';
                }
                potonganHtml += '</table>';
                $('#modalPotonganList').html(potonganHtml);
                
                $('#modalTotalPotongan').text(formatRupiah(data.total_potongan));
                $('#modalTotalDiterima').text(formatRupiah(data.jumlah));
                
                $('#modalKehadiran').text('Hadir: ' + (data.jumlah_hadir || 0) + ' hari');
                
                let statusWA = 'Belum terkirim';
                if (data.wa_status == 'success') {
                    statusWA = '✅ Terkirim pada: ' + data.wa_sent_at;
                } else if (data.wa_status == 'pending') {
                    statusWA = '⏳ Pending';
                } else if (data.wa_status == 'failed') {
                    statusWA = '❌ Gagal';
                }
                $('#modalStatusWA').text(statusWA);
                
                $('#modalDetail').modal('show');
            },
            error: function() {
                alert('Gagal memuat detail pegawai');
            }
        });
    }
    
    // Helper Functions
    function formatRupiah(angka) {
        if (!angka) return 'Rp 0';
        return 'Rp ' + parseFloat(angka).toLocaleString('id-ID');
    }
    
    function showLoading() {
        $('#loadingOverlay').addClass('show');
    }
    
    function hideLoading() {
        $('#loadingOverlay').removeClass('show');
    }
});
</script>
