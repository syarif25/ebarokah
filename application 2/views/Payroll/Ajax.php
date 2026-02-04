<script src="<?php echo base_url() ?>assets/vendor/global/global.min.js"></script>
    <script src="<?php echo base_url() ?>assets/vendor/chart.js/Chart.bundle.min.js"></script>
	<!-- Apex Chart -->
	<script src="<?php echo base_url() ?>assets/vendor/apexchart/apexchart.js"></script>
	
    <!-- Datatable -->
    <script src="<?php echo base_url() ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/plugins-init/datatables.init.js"></script>

	<script src="<?php echo base_url() ?>assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>

    <script src="<?php echo base_url() ?>assets/js/custom.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/dlabnav-init.js"></script>
	 <!-- JavaScript DataTables Buttons -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
   
    var save_method; //for save method string
    var table;

    $(document).ready(function(){
     
     table_item =   $('#tabel2').DataTable({ 
        "paging": false,
        "searching": false,
        "ordering": false,
        "info": true,
        "destroy": true,
        "deferRender": true,
         dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5',
            'print'
        ]
      });
   });

    $(function () {
      // $("#example1").DataTable();
    table =   $('#tabel_view').DataTable({
        "ajax": {
            "url": "<?php echo site_url('payroll/data_list')?>",
            "type": "POST"
        },

        "columnDefs": [
            { 
                "targets": [ -1 ], //last column
                "orderable": false, //set not orderable
            },
        
        ],  

        "paging": true,
        "searching": true,
        "ordering": true,
        });
    });

    function edit_stts(id)
    {
        $('#btnSave').show();
        $.ajax({
        url : "<?php echo site_url('payroll/get_byid')?>/" + id,
        type: "POST",
        dataType: "JSON",
        success: function(data)
        {
                $('#nama_lembaga').text(data.data_elemen.nama_lembaga);
                $('#id_kehadiran_lembaga').val(data.data_elemen.id_kehadiran_lembaga);
                $('#periode').val(data.data_elemen.bulan + ' ' + data.data_elemen.tahun );
                $('#jumlah').text(data.data_elemen.jumlah_total);
                $('#periode_').text(data.data_elemen.bulan + ' ' + data.data_elemen.tahun );
                table_item.clear();
                table_item.rows.add($(data.html_item)).draw();
                $('#modal_sudah_trans').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text(''); // Set title to Bootstrap modal title
        },
            error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
        });
    }
    
     function selesai(id)
    {
        $('#btnSave').hide();
        $.ajax({
        url : "<?php echo site_url('payroll/get_byid')?>/" + id,
        type: "POST",
        dataType: "JSON",
        success: function(data)
        {
                $('#nama_lembaga').text(data.data_elemen.nama_lembaga);
                // $('#nalem').text(data.data_elemen.nama_lembaga);
                $('#id_kehadiran_lembaga').val(data.data_elemen.id_kehadiran_lembaga);
                $('#periode').val(data.data_elemen.bulan + ' ' + data.data_elemen.tahun );
                $('#jumlah').text(data.data_elemen.jumlah_total);
                $('#periode_').text(data.data_elemen.bulan + ' ' + data.data_elemen.tahun );
                table_item.clear();
                table_item.rows.add($(data.html_item)).draw();
                $('#modal_sudah_trans').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text(''); // Set title to Bootstrap modal title
        },
            error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
        });
    }

    function save() {
        const BATCH_SIZE = 10;
        const BATCH_DELAY = 15000;
        const url = "<?php echo site_url('Payroll/save')?>";

        const form = document.getElementById('form');
        const formData = new FormData(form);

        const namaLengkap = formData.getAll('nama_lengkap[]');
        const namaLembaga = formData.getAll('nama_lembaga[]');
        const namaBank    = formData.getAll('nama_bank[]');
        const norek       = formData.getAll('norek[]');
        const jumlah      = formData.getAll('diterima[]');
        const nomorHp     = formData.getAll('nomor_hp[]');
        const kategori    = formData.getAll('kategori[]');
        const bulan       = formData.getAll('bulan[]');
        const tahun       = formData.getAll('tahun[]');
        const idTotal     = formData.getAll('id_total[]');
        const idKehadiranLog = formData.getAll('id_kehadiran_lembaga_log[]');

        const periode     = formData.get('periode');
        const idKehadiranLembaga = formData.get('id_kehadiran_lembaga');

        const dataArray = namaLengkap.map((_, i) => ({
            nama_lengkap: namaLengkap[i],
            nama_lembaga: namaLembaga[i],
            nama_bank: namaBank[i],
            norek: norek[i],
            diterima: jumlah[i],
            nomor_hp: nomorHp[i],
            kategori: kategori[i],
            bulan: bulan[i],
            tahun: tahun[i],
            id_total: idTotal[i],
            id_kehadiran_lembaga_log: idKehadiranLog[i],
            periode: periode,
            id_kehadiran_lembaga: idKehadiranLembaga
        }));

        // batching
        const batches = [];
        for (let i = 0; i < dataArray.length; i += BATCH_SIZE) {
            batches.push(dataArray.slice(i, i + BATCH_SIZE));
        }

        const totalBatches = batches.length;
        let currentBatch = 0;

        // 🚀 Tampilkan SweetAlert progress
        Swal.fire({
            title: 'Mengirim Notifikasi WhatsApp...',
            html: `
                <div style="margin-top:10px;">
                    <b>Batch: <span id="batch-count">0</span> / ${totalBatches}</b>
                    <div class="progress" style="height: 25px; margin-top:8px;">
                        <div id="swal-progress-bar" class="progress-bar progress-bar-striped bg-success" 
                            role="progressbar" style="width: 0%">0%</div>
                    </div>
                </div>
            `,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                sendNextBatch();
            }
        });

        function updateProgress() {
            const percent = Math.round((currentBatch / totalBatches) * 100);
            const bar = document.getElementById('swal-progress-bar');
            bar.style.width = percent + '%';
            bar.textContent = percent + '%';
            document.getElementById('batch-count').textContent = currentBatch;
        }

        function sendNextBatch() {
            if (currentBatch >= totalBatches) {
                Swal.fire({
                    icon: 'success',
                    title: 'Pengiriman Selesai',
                    text: `${dataArray.length} notifikasi berhasil dikirim.`,
                    confirmButtonText: 'OK'
                }).then(() => {
                    $('#modal_sudah_trans').modal('hide');
                    reload_table();
                });
                return;
            }

            const batch = batches[currentBatch];
            const batchForm = new FormData();

            batch.forEach(item => {
                batchForm.append('nama_lengkap[]', item.nama_lengkap);
                batchForm.append('nama_lembaga[]', item.nama_lembaga);
                batchForm.append('nama_bank[]', item.nama_bank);
                batchForm.append('norek[]', item.norek);
                batchForm.append('diterima[]', item.diterima);
                batchForm.append('nomor_hp[]', item.nomor_hp);
                batchForm.append('kategori[]', item.kategori);
                batchForm.append('bulan[]', item.bulan);
                batchForm.append('tahun[]', item.tahun);
                batchForm.append('id_total[]', item.id_total);
                batchForm.append('id_kehadiran_lembaga_log[]', item.id_kehadiran_lembaga_log);
            });

            batchForm.append('periode', batch[0].periode);
            batchForm.append('id_kehadiran_lembaga', batch[0].id_kehadiran_lembaga);

            $.ajax({
                url: url,
                type: "POST",
                data: batchForm,
                contentType: false,
                processData: false,
                dataType: "JSON",
                success: function (data) {
                    if (data.status) {
                        currentBatch++;
                        updateProgress();
                        setTimeout(sendNextBatch, BATCH_DELAY);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: `Gagal mengirim batch ke-${currentBatch+1}`
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Error',
                        text: `Batch ke-${currentBatch+1} gagal: ${errorThrown}`
                    });
                }
            });
        }
    }

    function reload_table() {
        table.ajax.reload(null,false);
    }

    let currentWaLogParams = {id_lembaga: null, bulan: null, tahun: null};

    function showWaLog(id_lembaga, bulan, tahun) {
        // Simpan parameter agar bisa dipakai saat refresh
        currentWaLogParams = {id_lembaga, bulan, tahun};

        $('#waLogModal').modal('show');
        $('#waLogTableBody').html('<tr><td colspan="8" class="text-center text-muted">Memuat data...</td></tr>');

        $.ajax({
            url: '<?= site_url('Payroll/get_wa_log') ?>',
            type: 'GET',
            data: { id_lembaga: id_lembaga, bulan: bulan, tahun: tahun },
            dataType: 'json',
            success: function(res) {
                if (res.length === 0) {
                    $('#waLogTableBody').html('<tr><td colspan="8" class="text-center text-muted">Tidak ada data log.</td></tr>');
                    return;
                }

                let rows = '';
                res.forEach((log, i) => {
                    let badge = log.status === 'success'
                        ? '<span class="badge bg-success">Success</span>'
                        : log.status === 'failed'
                            ? '<span class="badge bg-danger">Failed</span>'
                            : '<span class="badge bg-secondary">Pending</span>';

                            let actionBtn = log.status === 'failed'
                            ? `<button class="btn btn-sm btn-info" 
                                        onclick="resendWA(${log.id_wa_log}, ${id_lembaga}, '${bulan}', ${tahun})">
                                    <i class="fas fa-redo me-1"></i>Ulang
                            </button>`
                            : '-';

                    rows += `
                        <tr>
                            <td>${i+1}</td>
                            <td>${log.nama_penerima || '-'}</td>
                            <td>${log.nomor_hp}</td>
                            <td>${badge}</td>
                            <td>${log.http_code || '-'}</td>
                            <td>${log.response ? log.response.substring(0, 60) + (log.response.length > 60 ? '…' : '') : '-'}</td>
                            <td>${log.created_at}</td>
                            <td>${actionBtn}</td>
                        </tr>
                    `;
                });

                $('#waLogTableBody').html(rows);
            },
            error: function() {
                $('#waLogTableBody').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data log.</td></tr>');
            }
        });
    }

    function resendWA(id, id_lembaga, bulan, tahun) {
        Swal.fire({
            title: 'Konfirmasi Pengiriman Ulang',
            text: 'Apakah Anda yakin ingin mengirim ulang pesan WhatsApp ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, kirim ulang!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Mengirim...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: '<?= site_url('Payroll/resend_wa') ?>/' + id,
                    type: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        Swal.close();
                        if (res.status) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Pesan WhatsApp berhasil dikirim ulang!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Refresh modal tanpa menutupnya
                                showWaLog(id_lembaga, bulan, tahun);
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal kirim ulang: ' + (res.message || 'Terjadi kesalahan tidak diketahui.'),
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan koneksi: ' + error,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }



</script>

  

<div id="modal_sudah_trans" class="modal fade bd-example-modal-xl" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div id="progressContainer" class="mt-3" style="display:none;">
                        <label>Progress Pengiriman WA</label>
                        <div class="progress">
                            <div id="progressBar" class="progress-bar progress-bar-striped bg-success" 
                                role="progressbar" style="width:0%">0%</div>
                        </div>
                    </div>
                    <form action="#" id="form">
                        <div class="card-header">
                            <h4 class="card-title">Form Detail Nominal Transfer</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <label>Nama Lembaga</label>
                                    <h5 id="nama_lembaga"></h5>
                                    <input type="hidden" id="id_kehadiran_lembaga" name="id_kehadiran_lembaga">
                                    <input type="hidden" id="periode" name="periode">
                                </div>
                                <div class="col-3">
                                    <label>Periode</label>
                                    <h5 id="periode_"></h5>
                                </div>
                                <div class="col-3">
                                    <label>Jumlah </label>
                                    <h5 id="jumlah"></h5>
                                </div>
                            </div>
                            <br>
                            <form action="#" id="form">
                                <table class="table" id="tabel2" style="min-width: 100%" >
                                    <thead>
                                        <tr>
                                            <td>No</td>
                                            <td>Nama Lengkap</td>
                                            <td>Nama Bank</td>
                                            <td>Nomor Rekening</td>
                                            <td>Atas Nama </td>
                                            <td>Nominal</td>
                                            <td>+ Admin</td>
                                            <td>Bifast Kode</td>
                                            <td>Kode</td>
                                            <td>Ref</td>
                                            <td>Lembaga</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-xl btn-primary" id="btnSave" onclick="save()"> <i class="fas fa-check"></i> Sudah ditransfer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Log WA -->
<div class="modal fade" id="waLogModal" tabindex="-1" aria-labelledby="waLogModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h4 class="modal-title" id="waLogModalLabel"><i class="fab fa-whatsapp me-2"></i>Log Pengiriman WA</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Nama</th>
                <th>No. HP</th>
                <th>Status</th>
                <th>HTTP Code</th>
                <th>Response</th>
                <th>Waktu</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="waLogTableBody">
              <tr>
                <td colspan="8" class="text-center text-muted">Memuat data...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
