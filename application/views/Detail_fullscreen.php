<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rincian Penggajian - Fullscreen</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
            font-size: 14px;
        }
        .fullscreen-wrapper {
            max-width: 100%;
        }
        .table-responsive {
            overflow-x: auto;
        }
        table.dataTable {
            white-space: nowrap;
        }
    </style>
</head>
<body>
<div class="fullscreen-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- <img src="<?php echo base_url();?>assets/p2s2_fav.png" alt="" width="10">  -->
        <h4>Rincian Kehadiran Struktural Bulan Juli 2025 - Lembaga Pendidikan A</h4>
        <div>
            <button class="btn btn-warning">
                <i class="bx bx-printer"></i> Cetak
            </button>
            <button class="btn btn-primary">
                <i class="bx bx-arrow-back"></i> Kembali
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table id="tabel_gaji" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
            <thead class="thead-light">
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Eselon</th>
                    <th>TMT</th>
                    <th>Tunjab</th>
                    <th>MP</th>
                    <th>TMP</th>
                    <th colspan="2">Kehadiran</th>
                    <th>Tunkel</th>
                    <th>Tunj Anak</th>
                    <th>Kehormatan</th>
                    <th>TBK</th>
                    <th>Jumlah Barokah</th>
                    <th>Potongan</th>
                    <th>Diterima</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php for($i=1; $i<=15; $i++): ?>
                <tr>
                    <td><?= $i ?></td>
                    <td>Drs. Ahmad Fauzi, M.Pd</td>
                    <td>KTU Bidang</td>
                    <td>2015</td>
                    <td>1.500.000</td>
                    <td>3</td>
                    <td>10.000</td>
                    <td>10</td>
                    <td>150.000</td>
                    <td>Rp 500.000</td>
                    <td>Rp 750.000</td>
                    <td>0</td>
                    <td>Rp 300.000</td>
                    <td>Rp 200.000</td>
                    <td>Rp 400.000</td>
                    <td>Rp 250.000</td>

                <?php endfor; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4"><strong>Total</strong></td>
                    <td><strong>Rp 22.500.000</strong></td>
                    <td colspan="2"></td>
                    <td><strong>Rp 7.500.000</strong></td>
                    <td><strong>Rp 11.250.000</strong></td>
                    <td><strong>Rp 4.500.000</strong></td>
                    <td><strong>Rp 3.000.000</strong></td>
                    <td><strong>Rp 6.000.000</strong></td>
                    <td><strong>Rp 3.750.000</strong></td>
                    <td><strong>Rp 1.500.000</strong></td>
                    <td><strong>Rp 2.500.000</strong></td>
                    <td><strong>Rp 58.500.000</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- JS: jQuery, Bootstrap, FontAwesome, DataTables -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tabel_gaji').DataTable({
            responsive: false,
            paging: false,
            ordering: true,
            info: true,
            lengthMenu: [10, 25, 50, 100],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "Sebelumnya",
                    next: "Berikutnya"
                }
            }
        });
    });
</script>
</body>
</html>
