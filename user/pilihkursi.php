<?php
session_start();
require_once __DIR__ . '/../koneksi.php'; 
// ————————— Generate booking_session untuk pemesan —————————
if (!isset($_SESSION['booking_session'])) {
    $_SESSION['booking_session'] = uniqid('book', true);
}
$booking_session = $_SESSION['booking_session'];

// ————————— AJAX handler: save_seats & check_seats —————————
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    $id_bus  = $_POST['id_bus']   ?? '';
    $tanggal = $_POST['tanggal']  ?? '';

    if ($_POST['action'] === 'save_seats') {
        $selected_seats = json_decode($_POST['selected_seats'], true);
        $nama_pemesan   = $_POST['nama_pemesan'] ?? '';

        try {
            $koneksi->beginTransaction();
            // Hapus pilihan Temporary milik session yg sama
            $del = $koneksi->prepare("
                DELETE FROM booking
                 WHERE nama_pemesan = :nama
                   AND status       = 'Temporary'
            ");
            $del->execute([':nama' => $nama_pemesan]);

            // Insert setiap kursi
            $ins = $koneksi->prepare("
                INSERT INTO booking
                  (id_bus, tanggal_pemesanan, kursi, nama_pemesan, status, created_at)
                VALUES
                  (:bus , :tgl , :kursi, :nama, 'Temporary', NOW())
            ");
            foreach ($selected_seats as $s) {
                // Cek availability final
                $check = $koneksi->prepare("
                    SELECT COUNT(*) FROM booking
                     WHERE id_bus            = :bus
                       AND tanggal_pemesanan = :tgl
                       AND kursi             = :kursi
                       AND status IN ('Pending','Confirmed','Paid')
                ");
                $check->execute([
                    ':bus'   => $id_bus,
                    ':tgl'   => $tanggal,
                    ':kursi' => $s
                ]);
                if ($check->fetchColumn() > 0) {
                    throw new Exception("Kursi $s sudah diambil orang lain.");
                }
                $ins->execute([
                    ':bus'   => $id_bus,
                    ':tgl'   => $tanggal,
                    ':kursi' => $s,
                    ':nama'  => $nama_pemesan
                ]);
            }
            $koneksi->commit();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $koneksi->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($_POST['action'] === 'check_seats') {
        $seats_to_check = json_decode($_POST['seats'], true);
        if (empty($seats_to_check) || !$id_bus || !$tanggal) {
            echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap']);
            exit;
        }
        $placeholders = rtrim(str_repeat('?,', count($seats_to_check)), ',');
        $sql_check = "
            SELECT kursi
              FROM booking
             WHERE id_bus            = ?
               AND tanggal_pemesanan = ?
               AND status IN ('Pending','Confirmed','Paid')
               AND kursi IN ($placeholders)
        ";
        $stmt_ck = $koneksi->prepare($sql_check);
        $params  = array_merge([$id_bus, $tanggal], $seats_to_check);
        $stmt_ck->execute($params);
        $unavailable = $stmt_ck->fetchAll(PDO::FETCH_COLUMN);

        echo json_encode(['success' => true, 'unavailable_seats' => $unavailable]);
        exit;
    }
}

// ————————— GET: Ambil parameter —————————
$id_bus = !empty($_GET['id_bus']) ? $_GET['id_bus'] : (isset($bus['id_bus']) ? $bus['id_bus'] : 'default_id');
$tanggal = !empty($_GET['tanggal']) ? $_GET['tanggal'] : (isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d'));
$jumlah_penumpang = $_GET['jumlah'] ?? 1;

/* Ambil detail bus */
$stmt = $koneksi->prepare("SELECT * FROM data_bus WHERE id_bus = :id_bus");
$stmt->execute([':id_bus' => $id_bus]);
$bus = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$bus) die("Data bus tidak ditemukan.");

/* Hitung jam tiba */
$waktu_berangkat = new DateTime($bus['waktu_berangkat']);
[$h,$m] = explode(':', substr($bus['estimasi_waktu'],0,5));
$waktu_berangkat->add(new DateInterval("PT{$h}H{$m}M"));
$waktu_tiba = $waktu_berangkat->format('H:i');

/* Format tampilan tanggal */
$t_obj = new DateTime($tanggal);
$hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$tanggal_format = $hari[$t_obj->format('w')] . ', ' . $t_obj->format('d M Y');

/* Hitung harga */
$harga_per_kursi = (int)$bus['harga_tiket'];
$total_harga = $harga_per_kursi * $jumlah_penumpang;

/* Ambil kursi yang sudah terpesan + Temporary user ini */
$sql_booked = "
    SELECT DISTINCT kursi FROM booking
     WHERE id_bus            = :id_bus
       AND tanggal_pemesanan = :tanggal
       AND (
           status IN ('Pending','Confirmed','Paid')
           OR (nama_pemesan = :session AND status = 'Temporary')
       )
";
$stmt_book = $koneksi->prepare($sql_booked);
$stmt_book->execute([
    ':id_bus' => $id_bus,
    ':tanggal' => $tanggal,
    ':session' => $booking_session
]);

$raw = $stmt_book->fetchAll(PDO::FETCH_COLUMN);
$booked_seats = [];
foreach ($raw as $entry) {
    foreach (explode(',', $entry) as $s) {
        $code = trim($s);
        if ($code !== '') {
            $booked_seats[] = $code;
        }
    }
}
$booked_seats = array_unique($booked_seats);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pilih Kursi Anda - Nova Trans</title>
  <link rel="stylesheet" href="pilihkurssi.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo-mobile-wrap">
      <a href="pesantiket.php" class="logo">
        <img src="Gambar/LOGO.png" alt="Logo Nova Trans"/>
      </a>
    </div>
    <div class="nav-links">
      <a href="pesantiket.php"><i class="fas fa-ticket-alt"></i> Pesan Tiket</a>
      <a href="tentang.php"><i class="fas fa-info-circle"></i> Tentang</a>
      <a href="outlet.php"><i class="fas fa-store"></i> Outlet</a>
      <a href="kontak.php"><i class="fas fa-phone"></i> Kontak</a>
      <a href="blog.php"><i class="fas fa-newspaper"></i> Blog</a>
    </div>
    <div class="auth-buttons">
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
  </nav>

  <!-- Progress bar booking -->
  <div class="booking-progress">
    <div class="step completed"><div class="step-icon"><i class="fas fa-search"></i></div><div class="step-label">Pilih Bus</div></div>
    <div class="step active"><div class="step-icon"><i class="fas fa-chair"></i></div><div class="step-label">Pilih Kursi</div></div>
    <div class="step"><div class="step-icon"><i class="fas fa-user"></i></div><div class="step-label">Detail Penumpang</div></div>
    <div class="step"><div class="step-icon"><i class="fas fa-credit-card"></i></div><div class="step-label">Pembayaran</div></div>
  </div>

  <main>
    <div class="container">
      <div class="journey-details">
        <div class="route-info">
          <div class="city-pair">
            <h2><?=htmlspecialchars($bus['kota_asal'])?> <i class="fas fa-arrow-right"></i> <?=htmlspecialchars($bus['kota_tujuan'])?></h2>
          </div>
          <div class="journey-meta">
            <span><i class="far fa-calendar"></i> <?=$tanggal_format?></span>
            <span><i class="far fa-clock"></i> <?=date('H:i',strtotime($bus['waktu_berangkat']))?> - <?=$waktu_tiba?> (<?=substr($bus['estimasi_waktu'],0,5)?> jam)</span>
            <span><i class="fas fa-bus"></i> <?=htmlspecialchars($bus['nama_kelas'])?></span>
          </div>
        </div>
        <div class="price-tag">
          <span>Rp <?=number_format($harga_per_kursi,0,',','.')?></span>
          <div class="label">per kursi</div>
          <div class="total-price">
            Total untuk <?=$jumlah_penumpang?> penumpang:
            <strong>Rp <?=number_format($total_harga,0,',','.')?></strong>
          </div>
        </div>
      </div>

      <div class="seat-selection-container">
        <div class="seat-selection-layout">
          <div class="bus-layout">
            <div class="bus-front">
              <div class="driver-area"><i class="fas fa-steering-wheel"></i><span>Sopir</span></div>
              <div class="door"><span>Pintu</span></div>
            </div>
            <div class="seats-container">
              <div class="seat-legend">
                <div class="legend-item"><div class="seat available"></div><span>Tersedia</span></div>
                <div class="legend-item"><div class="seat selected"></div><span>Pilihan Anda</span></div>
                <div class="legend-item"><div class="seat occupied"></div><span>Sudah Dipesan</span></div>
              </div>
              <div class="seats-map">
                <div class="row row-label">
                  <div class="label">A</div><div class="label">B</div>
                  <div class="aisle">Gang</div>
                  <div class="label">C</div><div class="label">D</div>
                </div>
                <?php for($r=1;$r<=8;$r++): ?>
                  <div class="row">
                    <div class="row-number"><?=$r?></div>
                    <?php foreach(['A','B','C','D'] as $col):
                      $code = $r.$col;
                      $state = in_array($code, $booked_seats) ? 'occupied' : 'available';
                    ?>
                      <div class="seat <?=$state?>" data-seat="<?=$code?>" data-price="<?= $harga_per_kursi ?>">
                        <?=$code?>
                      </div>
                      <?php if($col==='B'): ?><div class="aisle"></div><?php endif; ?>
                    <?php endforeach; ?>
                  </div>
                <?php endfor; ?>
              </div>
            </div>
          </div>

          <div class="seat-selection-sidebar">
            <div class="selected-seats-panel">
              <h3>Kursi Dipilih</h3>
              <div id="selected-seats-list"></div>
              <div class="empty-selection-message" id="empty-message">
                <p>Silakan pilih kursi pada denah bus</p>
                <p class="hint">Maksimal 4 kursi per transaksi</p>
              </div>
              <div class="seat-selection-summary" id="summary" style="display:none;">
                <div class="summary-row">
                  <span>Subtotal (<span id="seat-count">0</span> kursi)</span>
                  <span class="price" id="subtotal">Rp 0</span>
                </div>
                <div class="summary-row">
                  <span>Biaya layanan</span><span class="price">Rp 5.000</span>
                </div>
                <div class="summary-row total">
                  <span>Total</span><span class="price" id="total">Rp 5.000</span>
                </div>
              </div>
              <div class="action-buttons">
                <a href="pesantiket.php" class="btn btn-outline">Kembali</a>
                <button id="continue-btn" class="btn btn-primary" disabled onclick="continueToPayment()">
                  <i class="fas fa-arrow-right"></i> Isi Data Pemesan
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="footer">
      <div class="footer-content">
        <div class="footer-section footer-logo">
          <img src="Gambar/LOGO2.png" alt="Logo Nova Trans" />
          <p>Solusi Transportasi Terbaik dan Terpercaya Rute Makassar - Luwu Timur</p>
          <div class="footer-social">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
        <div class="footer-section">
          <h3>Call Center</h3>
          <a href="tel:0821-9439-2804"><i class="fas fa-phone"></i> 0821-9439-2804</a>
          <a href="mailto:novatransbus@gmail.com"><i class="fas fa-envelope"></i> novatransbus@gmail.com</a>
          <a href="#"><i class="fab fa-whatsapp"></i> WhatsApp CS</a>
        </div>
        <div class="footer-section">
          <h3>Informasi</h3>
          <a href="pesantiket.php"><i class="fas fa-home"></i> Beranda</a>
          <a href="jadwal.php"><i class="fas fa-calendar-alt"></i> Jadwal</a>
          <a href="pesantiket.php"><i class="fas fa-ticket-alt"></i> Pesan Tiket</a>
          <a href="kontak.php"><i class="fas fa-address-book"></i> Kontak</a>
          <a href="blog.php"><i class="fas fa-newspaper"></i> Blog</a>
        </div>
        <div class="footer-section">
          <h3>Rute Populer</h3>
          <a href="#">Makassar - Sorowako</a>
          <a href="#">Makassar - Toroja</a>
          <a href="#">Makassar - Pare-Pare</a>
          <a href="#">Makassar - Palopo</a>
        </div>
      </div>
      <div class="footer-bottom">
        © 2025 Nova Trans. All rights reserved.
      </div>
    </footer>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const busData = {
        id: '<?= !empty($id_bus) ? htmlspecialchars($id_bus, ENT_QUOTES) : 'default_id' ?>',
        tanggal: '<?= !empty($tanggal) ? htmlspecialchars($tanggal, ENT_QUOTES) : date('Y-m-d') ?>',
        session: '<?= $booking_session ?>',
        harga: <?= $harga_per_kursi ?>
    };
    console.log('busData:', busData); // Debugging
    let selected = [];
    const maxSeats = 4, serviceCharge = 5000;
    const seats = document.querySelectorAll('.seat');
    const seatList = document.getElementById('selected-seats-list');
    const emptyMessage = document.getElementById('empty-message');
    const summary = document.getElementById('summary');
    const seatCount = document.getElementById('seat-count');
    const subtotal = document.getElementById('subtotal');
    const total = document.getElementById('total');
    const continueBtn = document.getElementById('continue-btn');

    const formatNumber = n => n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");

    // Fungsi untuk memeriksa ketersediaan kursi secara dinamis
    async function checkSeatAvailability(seatCode) {
      const response = await post({
        action: 'check_seats',
        id_bus: busData.id,
        tanggal: busData.tanggal,
        seats: JSON.stringify([seatCode])
      });
      return response.unavailable_seats && response.unavailable_seats.includes(seatCode);
    }

    seats.forEach(seat => {
      seat.addEventListener('click', async () => {
        const code = seat.dataset.seat;
        console.log('Seat clicked:', code, 'Classes:', seat.classList); // Debugging

        // Periksa ketersediaan sebelum mengizinkan pemilihan
        const isOccupied = await checkSeatAvailability(code);
        if (isOccupied || seat.classList.contains('occupied')) {
          alert('Kursi ' + code + ' sudah dipesan oleh orang lain.');
          return;
        }

        if (seat.classList.contains('selected')) {
          seat.classList.remove('selected');
          selected = selected.filter(c => c !== code);
        } else {
          if (selected.length >= maxSeats) {
            alert(`Maksimal ${maxSeats} kursi per transaksi`);
            return;
          }
          seat.classList.add('selected');
          selected.push(code);
        }
        render();
      });
    });

    function render() {
      console.log('Rendering, selected seats:', selected); // Debugging
      seatList.innerHTML = '';
      if (!selected.length) {
        emptyMessage.style.display = 'block';
        summary.style.display = 'none';
        continueBtn.disabled = true;
        return;
      }
      emptyMessage.style.display = 'none';
      summary.style.display = 'block';
      continueBtn.disabled = false;

      selected.forEach(c => {
        const d = document.createElement('div');
        d.textContent = c;
        seatList.appendChild(d);
      });

      seatCount.textContent = selected.length;
      const sub = selected.length * busData.harga;
      subtotal.textContent = 'Rp ' + formatNumber(sub);
      total.textContent = 'Rp ' + formatNumber(sub + serviceCharge);
    }

    async function post(data) {
      try {
        const res = await fetch(window.location.href, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams(data)
        });
        const json = await res.json();
        console.log('AJAX response:', json); // Debugging
        return json;
      } catch (e) {
        console.error('AJAX error:', e);
        return { success: false, message: 'Terjadi kesalahan jaringan' };
      }
    }

    window.continueToPayment = async () => {
      console.log('Continuing to payment with:', { busData, selected }); // Debugging
      if (!busData.id || busData.id === 'default_id' || !busData.tanggal || !selected.length) {
        alert('Data pemesanan tidak lengkap. Debug: id=' + busData.id + ', tanggal=' + busData.tanggal + ', selected=' + selected.length);
        continueBtn.disabled = false;
        continueBtn.textContent = 'Isi Data Pemesan';
        return;
      }

      continueBtn.disabled = true;
      continueBtn.textContent = 'Memproses...';

      const chk = await post({
        action: 'check_seats',
        id_bus: busData.id,
        tanggal: busData.tanggal,
        seats: JSON.stringify(selected)
      });
      if (chk.unavailable_seats?.length) {
        alert('Beberapa kursi sudah terambil: ' + chk.unavailable_seats.join(', '));
        continueBtn.disabled = false;
        continueBtn.textContent = 'Isi Data Pemesan';
        return location.reload();
      }

      const save = await post({
        action: 'save_seats',
        id_bus: busData.id,
        tanggal: busData.tanggal,
        selected_seats: JSON.stringify(selected),
        nama_pemesan: busData.session
      });
      if (!save.success) {
        alert(save.message || 'Gagal menyimpan pilihan kursi');
        continueBtn.disabled = false;
        continueBtn.textContent = 'Isi Data Pemesan';
        return location.reload();
      }

      console.log('Navigating to datapemesan.php with params:', {
        id_bus: busData.id,
        tanggal: busData.tanggal,
        kursi: selected.join(','),
        total: selected.length * busData.harga + serviceCharge
      });

      const params = new URLSearchParams({
        id_bus: busData.id,
        tanggal: busData.tanggal,
        kursi: selected.join(','),
        total: (selected.length * busData.harga + serviceCharge).toString()
      });
      location.href = 'datapemesan.php?' + params.toString();
    };

    render();
  });
  </script>
</body>
</html>