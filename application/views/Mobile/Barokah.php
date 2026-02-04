<div class="row">
	<!-- Header Section with Navigation Buttons -->
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col-4 text-center">
						<button class="btn btn-danger btn-lg btn-rounded mb-2">Struktural</button>
					</div>
					<div class="col-4 text-center">
						<a class="btn light btn-warning btn-lg btn-rounded mb-2" href="<?php echo base_url() ?>Laporan/kehadiran_pengajar">Pengajar</a>
					</div>
					<div class="col-4 text-center">
						<a class="btn light btn-warning btn-lg btn-rounded mb-2" href="<?php echo base_url() ?>Laporan/kehadiran_pengajar">Lainnya</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
	// Initialize variables
	$no = 1;
	$jumlah_barokah = 0;
	$jumlah_tunkel = 0;
	$jumlah_hadir = 0;
	$jumlah_tmp = 0;

	// Loop through data
	foreach ($isilist as $key) {
		$jumlah_barokah += $key->diterima;
	?>
		<!-- Data Cards Section -->
		<div class="col-xl-12">
			<div class="row">
				<div class="col-12">
					<div class="card draggable-handle draggable" tabindex="0">
						<div class="card-body">
							<!-- Status Indicator -->
							<div class="d-flex justify-content-between mb-2">
								<span class="sub-title">
									<svg class="me-2" width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
										<circle cx="5" cy="5" r="5" fill="#09BD3C"/>
									</svg>
									Sukses
								</span>
							</div>

							<!-- Institution Information -->
							<div class="row">
								<div class="col-12">
									<h4 class="fs-18 font-w500 mt-2"><?php echo htmlentities($key->nama_lembaga); ?></h4>
									<h3 class="text-secondary"><?php echo htmlentities($key->nama_jabatan); ?></h3>
									<p class="mb-0 fs-16"><?php echo htmlentities($key->bulan); ?>, <?php echo htmlentities($key->tahun); ?></p>
								</div>
							</div>

							<br>

							<!-- Action Buttons and Amount -->
							<div class="row justify-content-between align-items-center kanban-user">
								<div class="col-6">
									<a href="#" onclick="detail_barokah(<?php echo $key->id_total_barokah; ?>)" class="btn btn-outline-primary btn-rounded mb-2">
										<i class="fas fa-file-invoice"></i> Rincian
									</a>
								</div>
								<div class="col-6 d-flex justify-content-end">
									<span class="me-2">Rp.</span>
									<h3 class="mb-0"><?php echo htmlentities(rupiah($key->diterima)); ?></h3>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
</div>

<!-- Bottom spacing -->
<br><br><br><br>