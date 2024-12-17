<?php
include('db.php');

if (isset($_POST['simpan'])){
    $nama = $_POST['nama'];
    $kontak = $_POST['kontak'];
    $barang = $_POST['barang'];

    $stmt = $conn->prepare("INSERT INTO vendor (nama,kontak,barang) VALUES (?,?,?)");
    $stmt->bind_param("sss", $nama,$kontak, $barang);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['update'])){
    $update_id = $_POST['update_id'];
    $nama = $_POST['nama'];
    $kontak = $_POST['kontak'];
    $barang = $_POST['barang'];

    $stmt = $conn->prepare("UPDATE vendor set nama = ?, kontak = ?, barang = ? where vendorId = ?");
    $stmt->bind_param("sssi",$nama,$kontak,$barang,$update_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST)){
    $delete_id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE from vendor where vendorId = ?");
    $stmt->bind_param("i",$delete_id);
    $stmt->execute();
    $stmt->close();
}

$vendor = null;
if (isset($_GET['edit_id'])){
    $edit_id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * from vendor where vendorId = ?");
    $stmt->bind_param("i",$edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vendor = $result->fetch_assoc();
    $stmt->close();    
}

$result = $conn->query("SELECT * FROM vendor");

?>

<body>
    <div class="container-fluid py-5">
        <div class="row">

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-secondary">
                        <h2 class="text-dark">
                            <?php echo $vendor ? 'Edit Data' : 'Tambah Data' ?>
                        </h2>
                    </div>
                    <div class="card-body bg-light">
                        <form method="POST">
                            <div class="mb-3">
                                    <label for="nama" class="form-label text-dark">Nama Vendor</label>
                                    <input type="text" name="nama" class="form-control bg-light text-dark" placeholder="Masukkan Nama Vendor" required value="<?php echo $vendor ? $vendor['nama'] : '' ?>">
                            </div>
                            <div class="mb-3">
                                <label for="kontak" class="form-label text-dark">Kontak</label>
                                <input type="number" name="kontak" class="form-control bg-light text-dark" placeholder="Masukkan Kontak" required value="<?php echo $vendor ? $vendor['kontak'] : '' ?>">
                            </div>
                            <div class="mb-3">
                                <label for="barang" class="form-label text-dark">Barang(Pisahkan dengan koma)</label>
                                <input type="text" name="barang" class="form-control bg-light text-dark" placeholder="Contoh: baju,celana,sepatu" required value="<?php echo $vendor ? $vendor['barang'] : '' ?>">
                            </div>
                            <?php if (isset($vendor)) { ?>
                                <input type="hidden" name="update_id" value="<?php echo $vendor['vendorId'] ?>">
                                <button type="submit" name="update" class="btn btn-info">Update</button>
                                <a href="index.php?page=vendor" class="btn btn-secondary">Cancel</a>
                            <?php } else { ?>
                                <button type="submit" name="simpan" class="btn btn-info">Simpan</button>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-secondary">
                        <h2 class="text-dark">Data Vendor</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-light table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Kontak</th>
                                        <th>Barang</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    while ($row = $result->fetch_assoc()) { 
                                    $no = 1;    
                                    ?>
                                        <tr>
                                            <td><?php echo $no++ ?></td>
                                            <td><?php echo $row['nama'] ?></td>
                                            <td><?php echo $row['kontak'] ?></td>
                                            <td><?php echo $row['barang'] ?></td>
                                            <td>
                                                <form method="POST">
                                                    <input type="hidden" name="delete_id" value="<?php echo $row['vendorId'] ?>">
                                                    <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Yakin hapus vendor ini?')">Delete</button>
                                                    <a href="index.php?page=vendor&edit_id=<?php echo $row['vendorId']?>" class="btn btn-success">Edit</a>
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