<?php
include('db.php');

if (isset($_POST['simpan'])){
    $nama = $_POST['nama'];
    $stok = $_POST['stok'];
    $gudang = $_POST['gudang'];
    $serial_number = $_POST['serial_number'];

    $stmt = $conn->prepare("INSERT into inventory (nama,stok,gudang,serial_number) values (?,?,?,?)");
    $stmt->bind_param("sisi",$nama,$stok,$gudang,$serial_number);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['update'])){
    $update_id = $_POST['update_id'];
    $nama = $_POST['nama'];
    $stok = $_POST['stok'];
    $gudang = $_POST['gudang'];
    $serial_number = $_POST['serial_number'];

    $stmt = $conn->prepare("UPDATE inventory set nama = ?,stok = ?,gudang = ?,serial_number = ? where barangId = ?");
    $stmt->bind_param("sisii",$nama,$stok,$gudang,$serial_number,$update_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['delete'])){
    $delete_id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE from inventory where barangId = ? ");
    $stmt->bind_param("i",$delete_id);
    $stmt->execute();
    $stmt->close();
}

$inventory = null;
if (isset($_GET['edit_id'])){
    $edit_id = $_GET['edit_id'];
    $stmt = $conn->prepare('SELECT * from inventory where barangId = ?');
    $stmt->bind_param("i",$edit_id);
    $stmt->execute();
    $inventory = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$gudang_result = $conn->query("SELECT * FROM gudang");

$barang_result = $conn->query("SELECT barang from vendor");
$all_barang = [];

while ($row = $barang_result->fetch_assoc()) {
    $all_barang = array_merge($all_barang,explode(",",$row['barang']));
}
$all_barang = array_unique(array_map("trim",$all_barang));

$inventory_result = $conn->query("SELECT * FROM inventory")
?>

<body class="bg-dark text-light">
    <div class="container-fluid py-5">
        <div class="row">

            <div class="col-lg-4">
                <div class="card bg-secondary text-white">
                    <div class="card-header bg-secondary text-dark">
                        <h2 class="mb-0">
                            <?php echo $inventory ? 'Edit Data' : 'Tambah Data' ?>
                        </h2>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nama" class="form-label text-dark">Nama Barang</label>
                                <select name="nama"class="form-select">
                                    <option>--Pilih Barang--</option>
                                    <?php foreach ($all_barang as $barang) { ?>                                    
                                        <option value="<?php echo $barang?>" <?php echo isset($inventory) && $inventory['nama'] == $barang ? 'selected' : ''?>>
                                            <?php echo $barang; ?>
                                        </option>
                                    <?php }?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="stok" class="form-label text-dark">Stok</label>
                                <input type="number" name="stok" class="form-control bg-light" placeholder="Masukkan Stok Barang" required value="<?php echo $inventory ? $inventory['stok'] : '' ?>">
                            </div>
                            <div class="mb-3">
                                <label for="gudang" class="form-label text-dark">Gudang</label>
                                <select name="gudang" class="form-select text-dark">
                                    <option>--Pilih Gudang--</option>
                                    <?php while ($row = $gudang_result->fetch_assoc()) { ?>
                                        <option value="<?php echo $row['nama'] ?>" <?php echo isset($inventory) && $inventory['gudang'] == $row['nama'] ? 'selected' : '' ?>>
                                        <?php echo $row['nama']?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="serial_number" class="form-label text-dark">Nomor Seri</label>
                                <input type="number" name="serial_number" class="form-control bg-light" placeholder="Masukkan Nomor Seri" required value="<?php echo $inventory ? $inventory['serial_number'] : '' ?>">
                            </div>
                            <?php if (isset($inventory)) {?>
                                <input type="hidden" name="update_id" value="<?php echo $inventory['barangId']; ?>">
                                <button name="update" type="submit" class="btn btn-info">Update</button>
                                <a href="index.php?page=inventory" class="btn btn-secondary">Cancel</a>
                                <?php } else { ?>
                                    <button class="btn btn-info" type="submit" name="simpan">Simpan</button>
                                <?php } ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-secondary">
                        <h2 class="text-dark">Data Gudang</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Stok</th>
                                        <th>Gudang</th>
                                        <th>Nomor Seri</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($row = $inventory_result->fetch_assoc()) { 
                                    $low_stock_alert = $row['stok'] <= 5 ? 'table-danger' : '';    
                                    ?>
                                    <tr class="<?php echo $low_stock_alert ?>">
                                        <td><?php echo $no++ ?></td>
                                        <td><?php echo $row['nama'] ?></td>
                                        <td><?php echo $row['stok'] ?></td>
                                        <td><?php echo $row['gudang'] ?></td>
                                        <td><?php echo $row['serial_number'] ?></td>
                                        <td>
                                            <form method="POST">
                                                    <input type="hidden" name="delete_id" value="<?php echo $row['barangId']; ?>">
                                                    <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Yakin Menghapus data ini?')" >Hapus</button>
                                                    <a href="index.php?page=inventory&edit_id=<?php echo $row['barangId']?>" class="btn btn-success">Edit</a>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>