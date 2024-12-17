<?php
include 'db.php';

if (!isset($_SESSION['adminId'])) {
    header('Location: login.php');
    exit;
}

$success_message = $error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $barangId = $_POST['barangId'];
    $jumlah = $_POST['jumlah'];
    $tipe = $_POST['tipe'];

    if ($jumlah <= 0) {
        $error_message = "Jumlah barang harus lebih dari 0.";
    } else {
        try {
            $conn->begin_transaction();

            $query = $conn->prepare("SELECT stok FROM inventory WHERE barangId = ?");
            $query->bind_param("i", $barangId);
            $query->execute();
            $result = $query->get_result();

            if ($result->num_rows === 0) throw new Exception("Barang tidak ditemukan.");
            $stok = $result->fetch_assoc()['stok'];

            if ($tipe === 'keluar' && $jumlah > $stok) throw new Exception("Stok tidak mencukupi.");

            $new_stock = ($tipe === 'masuk') ? $stok + $jumlah : $stok - $jumlah;
            $update = $conn->prepare("UPDATE inventory SET stok = ? WHERE barangId = ?");
            $update->bind_param("ii", $new_stock, $barangId);
            $update->execute();

            $insert = $conn->prepare("INSERT INTO transaksi (barangId, jumlah, tipe) VALUES (?, ?, ?)");
            $insert->bind_param("iis", $barangId, $jumlah, $tipe);
            $insert->execute();

            $conn->commit();
            $success_message = "Transaksi berhasil disimpan.";
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = $e->getMessage();
        }
    }
}

// Ambil daftar barang
$barang_list = $conn->query("SELECT barangId, nama FROM inventory");
?>

<body class="bg-dark text-light">
<div class="container-fluid py-5">
    <div class="row">
        <!-- Form Transaksi -->
        <div class="col-lg-4">
            <div class="card bg-light text-dark">
                <div class="card-header bg-secondary text-dark">
                    <h2 class="text-dark">Form Transaksi</h2>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="barangId" class="form-label">Pilih Barang</label>
                            <select name="barangId" id="barangId" class="form-select bg-light text-dark" required>
                                <option value="">Pilih Barang</option>
                                <?php while ($row = $barang_list->fetch_assoc()): ?>
                                    <option value="<?= $row['barangId'] ?>"><?= $row['nama'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control bg-light text-dark" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipe" class="form-label">Tipe Transaksi</label>
                            <select name="tipe" id="tipe" class="form-select bg-light text-dark" required>
                                <option value="masuk">Barang Masuk</option>
                                <option value="keluar">Barang Keluar</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info text-dark">Simpan Transaksi</button>
                    </form>
                    <?php if ($success_message): ?>
                        <div class="alert alert-success mt-3"><?= $success_message ?></div>
                    <?php elseif ($error_message): ?>
                        <div class="alert alert-danger mt-3"><?= $error_message ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card bg-light text-dark">
                <div class="card-header bg-secondary text-dark">
                    <h2>Riwayat Transaksi</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-light table-hover">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Tipe</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $transaksi_result = $conn->query("
                                SELECT t.jumlah, t.tipe, i.nama AS nama_barang
                                FROM transaksi t
                                JOIN inventory i ON t.barangId = i.barangId
                                ORDER BY t.transaksiId DESC LIMIT 10
                            ");
                            $no = 1;
                            while ($data = $transaksi_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $data['nama_barang'] ?></td>
                                    <td><?= $data['jumlah'] ?></td>
                                    <td><?= ucfirst($data['tipe']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
