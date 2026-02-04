<div class="container-fluid">
	<div class="row">
	    <?php if ($barokah_bulan_lalu->bulan_lalu == 0){ ?>
            
	<?php	} else {
			?>
		<div class="col-6">
			<div class="col-12">
				<div class="card">
					<div class="card-body d-flex px-4  justify-content-between">
						<div>
							<div class="">
								<h2 class="fs-32 font-w700">Barokah Bulan Ini (<?php echo $bulan_ini_nama; ?>) </h2>
								<span class="fs-18 font-w500 d-block text-primary">Rp. <?php echo rupiah($barokah_bulan->bulan_ini); ?> &nbsp;&nbsp;&nbsp; <strong class="">(<?php echo $barokah_bulan->lembaga; ?> Lembaga) </strong></span>
								<!-- <?php
								$persen = (($barokah_bulan->bulan_ini - $barokah_bulan_lalu->bulan_lalu) / $barokah_bulan_lalu->bulan_lalu) * 100;
								$pesentase = round($persen);
								if ($barokah_bulan->bulan_ini < $barokah_bulan_lalu->bulan_lalu){
								?>
								<span class="d-block fs-16 font-w400"><small class="text-danger"><?php echo $pesentase; ?>%</small> <i class="fas fa-long-arrow-alt-up"></i> </span>
								<?php } else { ?>
									<span class="d-block fs-16 font-w400"><h3 class="text-success"><?php echo $pesentase; ?>% <i  class="fa fa-arrow-up fa-1x" style="color: green;"></i> </h3> </span>
								<?php }?> -->
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
		<div class="col-6">
			<div class="col-12">
				<div class="card">
					<div class="card-body d-flex px-4  justify-content-between">
						<div>
							<div class="">
								<h2 class="fs-32 font-w700">Barokah Bulan lalu (<?php echo $bulan_lalu_nama; ?>)</h2>
								<span class="fs-18 font-w500 d-block">Rp. <?php echo rupiah($barokah_bulan_lalu->bulan_lalu); ?> &nbsp;&nbsp;&nbsp; <strong class="text-info">(<?php echo $barokah_bulan_lalu->lembaga; ?> Lembaga) </strong></span>
								<!-- <span class="d-block fs-16 font-w400"><small class="text-danger">-2%</small> than last month</span> -->
							</div>
						</div>
						<div >
						<br><br>
						</div>
						
					</div>
				</div>
			</div>
		</div>
		<?php }	?>
		<div class="col-xl-6">
			<div class="row">
				<!-- <div class="col-xl-12"> -->
					<div class="col-xl-6">
						<div class="card">
							<div class="card-body d-flex px-4 pb-0 justify-content-between">
								<div>
									<h4 class="fs-18 font-w600 mb-4 text-nowrap text-primary">Total Umana Putra</h4>
									<div class="d-flex align-items-center">
										<?php
										$total = $umana_pa->putra + $umana_pi->putri;
										$percen_umana = ($umana_pa->putra / $total) * 100;
										$percen_umana_pi = ($umana_pi->putri / $total) * 100;
										$pesentase_umana = round($percen_umana);
										$pesentase_umanapi = round($percen_umana_pi);
										?>
										<h2 class="fs-32 font-w700 mb-0"><?php 
										echo $umana_pa->putra;
										?></h2>
										<span class="d-block ms-4">
											<!-- <svg width="21" height="11" viewBox="0 0 21 11" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M1.49217 11C0.590508 11 0.149368 9.9006 0.800944 9.27736L9.80878 0.66117C10.1954 0.29136 10.8046 0.291359 11.1912 0.661169L20.1991 9.27736C20.8506 9.9006 20.4095 11 19.5078 11H1.49217Z" fill="#09BD3C"/>
											</svg> -->
											<!-- <small class="d-block fs-16 font-w400 text-success">+0,5%</small> -->
										</span>
									</div>
								</div>
								<div class="bg-gradient1 rounded text-center p-3">
									<div class="d-inline-block position-relative donut-chart-sale mb-3">
										<span class="donut1" data-peity='{ "fill": ["var(--primary)", "rgba(238, 238, 237, 1)"],   "innerRadius": 33, "radius": 5}'><?php echo $umana_pa->putra; ?>/<?php echo $total; ?></span>
										<small class="text-primary fs-22"><?php echo $pesentase_umana; ?>%</small>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-6">
						<div class="card">
							<div class="card-body px-4">
								<h4 class="fs-18 font-w600 mb-5 text-nowrap text-info">Total Umana Putri</h4>
								<div class="progress default-progress">
									<div class="progress-bar progress-animated bg-info" style="width: <?php echo $percen_umana_pi; ?>%; height:10px;" role="progressbar">
										<!-- <span class="sr-only">90% Complete</span> -->
									</div>
								</div>
								<div class="d-flex align-items-end mt-1 justify-content-between">
									<span class="text-info fs-22 font-w200"><?php echo $pesentase_umanapi; ?>%</span>
									<h4 class="mb-0 fs-32 font-w800 text-info"><?php echo $umana_pi->putri; ?></h4>
								</div>
							</div>
						</div>
					</div>
				<!-- </div> -->
			</div>
			<div class="row">
				<div class="col-xl-12">
					<div class="card">
						<div class="card-header border-0">
							<h4 class="fs-20 font-w700 text-center"> Jumlah Rekening Umana</h4>
							<div class="dropdown ">
								<div class="btn-link" data-bs-toggle="dropdown">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<circle cx="12.4999" cy="3.5" r="2.5" fill="#A5A5A5"/>
										<circle cx="12.4999" cy="11.5" r="2.5" fill="#A5A5A5"/>
										<circle cx="12.4999" cy="19.5" r="2.5" fill="#A5A5A5"/>
									</svg>
								</div>
								
							</div>
						</div>
						<div class="card-body p-2">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>No</th>
										<th>Nama Bank</th>
										<th>Jumlah</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$query = $this->db->query('select nama_bank, count(nik) as jumlah  from umana GROUP by nama_bank order by jumlah desc')->result();
									$no = 1; // Tetapkan nilai awal untuk $no
									foreach ($query as $data) {
									    $totalJumlah += $data->jumlah;
									?>
										<tr>
											<td><?php echo $no; ?></td> <!-- Tambahkan echo untuk menampilkan $no -->
											<td><?php echo $data->nama_bank; ?></td> <!-- Tambahkan echo untuk menampilkan $data->nama_bank -->
											<td><?php echo $data->jumlah; ?></td> <!-- Tambahkan echo untuk menampilkan $data->jumlah -->
										</tr>
									<?php
										$no++; // Tingkatkan nilai $no di setiap iterasi
									}
									?>
									<tr>
									    <td colspan="2" class="text-center fw-bold">Jumlah</td>
									    <td class="fw-bold"><?php echo $totalJumlah; ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xl-6">
			<div class="row">
				<div class="col-xl-12">
					
				</div>
				<div class="col-xl-12">
					<div class="card">
						<div class="card-header border-0">
							<h4 class="fs-20 font-w700"> Statistik Barokah</h4>
							<div class="dropdown ">
								<div class="btn-link" data-bs-toggle="dropdown">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<circle cx="12.4999" cy="3.5" r="2.5" fill="#A5A5A5"/>
										<circle cx="12.4999" cy="11.5" r="2.5" fill="#A5A5A5"/>
										<circle cx="12.4999" cy="19.5" r="2.5" fill="#A5A5A5"/>
									</svg>
								</div>
								
							</div>
						</div>
						<div class="card-body p-2">
							<div id="reservationChart" class="reservationChart"></div>
						</div>
					</div>
				</div>
				
			</div>
		</div>
		<div class="col-xl-6">
			<div class="row">
				<div class="col-xl-12">
				
				</div>
				<div class="col-xl-12">
					
				</div>
			</div>
		</div>
	</div>
</div>