<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitoring WA Log</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: #f4f6f9;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .card {
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            text-transform: capitalize;
        }
        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }
        .status-success {
            background-color: #28a745;
            color: white;
        }
        .status-failed {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>

<!-- ✅ NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><i class="bi bi-whatsapp"></i> Monitoring WA Log</a>
    </div>
</nav>

<div class="container">

    <!-- ✅ HEADER -->
    <div class="row mb-3">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold">📊 Daftar Log Pengiriman WhatsApp</h4>
                <button class="btn btn-success" id="refreshBtn"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
            </div>
        </div>
    </div>

    <!-- ✅ TABLE -->
    <div class="card">
        <div class="card-body">
            <table id="logTable" class="table table-striped table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Nama Penerima</th>
                        <th>Nomor HP</th>
                        <th>Lembaga</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Dikirim</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): $no=1; foreach ($logs as $log): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($log->nama_penerima) ?></td>
                        <td><?= htmlspecialchars($log->nomor_hp) ?></td>
                        <td><?= htmlspecialchars($log->nama_lembaga) ?></td>
                        <td><?= htmlspecialchars($log->bulan) ?></td>
                        <td><?= htmlspecialchars($log->tahun) ?></td>
                        <td><?= number_format($log->jumlah, 0, ',', '.') ?></td>
                        <td>
                            <?php if ($log->status == 'success'): ?>
                                <span class="status-badge status-success">Success</span>
                            <?php elseif ($log->status == 'failed'): ?>
                                <span class="status-badge status-failed">Failed</span>
                            <?php else: ?>
                                <span class="status-badge status-pending">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d-m-Y H:i', strtotime($log->created_at)) ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ✅ JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function(){
    $('#logTable').DataTable({
        "order": [[ 8, "desc" ]], // Urutkan berdasarkan tanggal kirim (kolom ke-9)
        "pageLength": 10
    });

    $('#refreshBtn').click(function(){
        location.reload();
    });
});
</script>

</body>
</html>
