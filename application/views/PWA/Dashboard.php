<div id="page">
    <div class="header header-fixed header-logo-center header-auto-show">
        <a href="#" class="header-title">Ebarokah</a>
        <a href="<?php echo base_url() ?>/Login/logout" class="header-icon header-icon-2">
        <i class="fa fa-sign-out"></i>
        </a>
        <a href="#" data-menu="menu-main" class="header-icon header-icon-4">
        <i class="fas fa-bars"></i>
        </a>
    </div>

  <div id="footer-bar" class="footer-bar-6">
    <a href="<?php echo base_url() ?>Report/Kehadiran" >
      <i class="fa fa-file"></i><span class="active-nav">Kehadiran</span>
    </a>
    <a href="<?php echo base_url() ?>Report" class="circle-nav">
      <i class="fa fa-home"></i><span>Home</span>
    </a>
    <a href="#" onclick="showMaintenanceModal()"
          ><i class="fa fa-user"></i><span>Data Diri</span></a
        >
  </div>

  <!-- <div class="page-title page-title-fixed">
    <h1>Ebarokah</h1>
    <a href="<?php echo base_url() ?>/Login/logout" class="header-icon header-icon-2">
        <i class="fa fa-sign-out"></i>
        </a>
  </div> -->
  <div class="page-title page-title-fixed" style="opacity: 1;">
      <h1>Ebarokah</h1>
      <a href="<?php echo base_url() ?>/Login/logout" class="page-title-icon shadow-xl bg-theme color-theme" data-menu="menu-main"><i class="fa fa-sign-out"></i></a>
  </div>


  <div class="page-title-clear"></div>

    <div class="page-content">
        <div class="card card-style">
            <div class="content mb-0">
                <p class="mb-4 color-highlight font-700 font-10 mb-n1">Selamat Datang Kembali</p>
                <h4 id="namaGelar">-</h4>
                <p class="mb-1">Lembaga Pengabdian saat ini <b><span id="lembagaAktif">0</span> Lembaga</b></p>

                <div class="divider mb-2"></div>

                <div class="d-flex pb-2">
                    <div>
                    <h6 class="font-11 mb-0 text-success">
                        Barokah Bulan Lalu <br>
                        <small id="periodeLalu" class="text-muted">(—)</small>
                    </h6>
                    <h3 class="font-15" id="barokahLalu">Rp. 0</h3>
                    </div>
                    <div class="ms-auto text-end">
                    <h6 class="font-11 mb-0 text-primary">
                        Barokah Bulan Ini <br>
                        <small id="periodeIni" class="text-muted">(—)</small>
                    </h6>
                    <h3 class="font-15" id="barokahIni">Rp. 0</h3>
                    </div>
            </div>
        </div>

        </div>
        <div class="card card-style">
            <div class="content">
                <h5 class="mb-2">Pengabdian Saat ini</h5>

                <h6 class="color-highlight mb-2">Struktural</h6>
                <ul class="list-group" id="listStruktural">
                <li class="list-group-item">Memuat data...</li>
                </ul>

                <div class="divider my-3"></div>

                <h6 class="color-highlight mb-2">Pengajar</h6>
                <ul class="list-group" id="listPengajar">
                <li class="list-group-item">Memuat data...</li>
                </ul>
            </div>
        </div>
        
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // ===== Rincian =====
  const ulStruk = document.getElementById('listStruktural');
  const ulPeng  = document.getElementById('listPengajar');
  if (ulStruk && ulPeng) {
    fetch('<?php echo site_url("report/dashboard_rincian"); ?>', {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
    .then(async r => {
      const text = await r.text();
      try { return JSON.parse(text); }
      catch (e) { console.error('RAW RESPONSE (rincian):', text); throw e; }
    })
    .then(res => {
      if (!res.ok) throw new Error(res.message || 'Gagal');

      const render = (ul, rows, emptyText, map) => {
        ul.innerHTML = '';
        if (!rows || !rows.length) {
          ul.innerHTML = `<li class="list-group-item">${emptyText}</li>`;
          return;
        }
        rows.forEach(row => {
          const { title, subtitle } = map(row);
          const li = document.createElement('li');
          li.className = 'list-group-item';
          li.innerHTML = `
           <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="font-600">${title}</div>
                <div class="font-12 color-gray">${subtitle}</div>
              </div>
              <i class="fa fa-check-circle text-success"></i>
            </div>`;
          ul.appendChild(li);
        });
      };

      render(ulStruk, res.struktural, 'Tidak ada penempatan struktural aktif.', r => ({
        title: r.nama_lembaga || '-',
        subtitle: r.jabatan_lembaga || '—'
      }));

      render(ulPeng, res.pengajar, 'Tidak ada pengajar aktif.', r => ({
        title: r.nama_lembaga || '-',
        subtitle: (r.kategori || '—').toUpperCase()
      }));
    })
    .catch(err => {
      console.error('AJAX rincian error:', err);
      if (ulStruk) ulStruk.innerHTML = '<li class="list-group-item">Gagal memuat data.</li>';
      if (ulPeng)  ulPeng.innerHTML  = '<li class="list-group-item">Gagal memuat data.</li>';
    });
  }

  // ===== Header angka (nama/total/barokah) =====
  const elNama    = document.getElementById('namaGelar');
  const elAktif   = document.getElementById('lembagaAktif');
  const elIni     = document.getElementById('barokahIni');
  const elLalu    = document.getElementById('barokahLalu');

  if (elNama && elAktif && elIni && elLalu) {
    fetch('<?php echo site_url("report/dashboard_barokah"); ?>', {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
    .then(async r => {
      const text = await r.text();
      try { return JSON.parse(text); }
      catch (e) { console.error('RAW RESPONSE (header):', text); throw e; }
    })
    .then(res => {
        if (!res.ok) return;
        elNama.textContent  = res.nama_gelar || '-';
        elAktif.textContent = res.lembaga_aktif ?? 0;
        elIni.textContent   = res.total_bulan_ini_formatted  || 'Rp. 0';
        elLalu.textContent  = res.total_bulan_lalu_formatted || 'Rp. 0';
        document.getElementById('barokahIni').textContent = res.total_bulan_ini_formatted || 'Rp. 0';
        document.getElementById('barokahLalu').textContent = res.total_bulan_lalu_formatted || 'Rp. 0';
        // Tambahkan label periode
        const pIni = document.getElementById('periodeIni');
        const pLalu = document.getElementById('periodeLalu');
        if (pIni)  pIni.textContent  = `${res.label_bulan_ini || '-'} ${res.label_tahun_ini || ''}`;
        if (pLalu) pLalu.textContent = `${res.label_bulan_lalu || '-'} ${res.label_tahun_lalu || ''}`;
    })
    .catch(err => console.error('AJAX header error:', err));
  }
});

(function(){
  const elIni   = document.getElementById('barokahIni');
  const elLalu  = document.getElementById('barokahLalu');
  const listIni = document.getElementById('listBarokahIni');
  const listLalu= document.getElementById('listBarokahLalu');
  const badgeIni= document.getElementById('badgeSumIni');
  const badgeLalu=document.getElementById('badgeSumLalu');
  const headIni = document.getElementById('headingIni');
  const headLalu= document.getElementById('headingLalu');

  if (!elIni || !elLalu || !listIni || !listLalu) return;

  fetch('<?php echo site_url("report/dashboard_barokah"); ?>', {
    headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' },
    credentials: 'same-origin'
  })
  .then(async r => { const t=await r.text(); try { return JSON.parse(t);} catch(e){console.error('RAW RESPONSE (barokah):',t); throw e;} })
  .then(res => {
    if (!res.ok) return;

    // Header angka
    elIni.textContent  = res.total_bulan_ini_formatted  || 'Rp. 0';
    elLalu.textContent = res.total_bulan_lalu_formatted || 'Rp. 0';

    // Heading & badge total
    if (headIni)  headIni.textContent  = `${res.label_bulan_ini || 'Bulan Ini'} ${res.label_tahun_ini || ''}`.trim();
    if (headLalu) headLalu.textContent = `${res.label_bulan_lalu || 'Bulan Lalu'} ${res.label_tahun_lalu || ''}`.trim();
    if (badgeIni)  badgeIni.textContent  = res.total_bulan_ini_formatted  || 'Rp. 0';
    if (badgeLalu) badgeLalu.textContent = res.total_bulan_lalu_formatted || 'Rp. 0';

    // Helper render list
    const render = (ul, rows, emptyText) => {
      ul.innerHTML = '';
      if (!rows || !rows.length) {
        ul.innerHTML = `<li class="list-group-item">${emptyText}</li>`;
        return;
      }
      rows.forEach(r => {
        const li = document.createElement('li');
        li.className = 'list-group-item';
        li.innerHTML = `
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="font-600">${r.lembaga || '-'}</div>
              <div class="font-12 color-gray">${r.jenis || '-'}</div>
            </div>
            <div class="font-600">Rp. ${Number(r.nominal||0).toLocaleString('id-ID')}</div>
          </div>`;
        ul.appendChild(li);
      });
    };

    render(listIni,  res.rincian_bulan_ini,  'Tidak ada data barokah bulan ini.');
    render(listLalu, res.rincian_bulan_lalu, 'Tidak ada data barokah bulan lalu.');
  })
  .catch(err => {
    console.error('AJAX barokah error:', err);
    listIni.innerHTML  = '<li class="list-group-item">Gagal memuat data.</li>';
    listLalu.innerHTML = '<li class="list-group-item">Gagal memuat data.</li>';
  });
})();
</script>
