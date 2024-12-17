<?php 

include('db.php');

if (isset($_POST['simpan'])){
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];

    $stmt = $conn->prepare("INSERT into gudang (nama,lokasi) values (?,?)");
    $stmt->bind_param("ss",$nama,$lokasi);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['update'])){
    $update_id = $_POST['update_id'];
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];

    $stmt = $conn->prepare("UPDATE gudang set nama = ?, lokasi = ? where gudangId = ? ");
    $stmt->bind_param("ssi",$nama,$lokasi,$update_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['delete'])){
    $delete_id = $_POST['delete_id'];
    $stmt = $conn->prepare("DELETE from gudang where gudangId = ?");
    $stmt->bind_param("i",$delete_id);
    $stmt->execute();
    $stmt->close();
}

$gudang = null;
if (isset($_GET['edit_id'])){
    $edit_id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * from gudang where gudangId = ?");
    $stmt->bind_param("i",$edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $gudang = $result->fetch_assoc();
    $stmt->close();
}

$query = "SELECT * from gudang";
$result = $conn->query($query);
?>

<body class="bg-dark text-light">
    <div class="container-fluid py-5">
        <div class="row">


            <div class="col-lg-4">
                <div class="card bg-secondary text-white">
                    <div class="card-header bg-secondary text-dark">
                        <h2 class="mb-0">
                            <?php echo $gudang ? 'Edit Data' : 'Tambah Data'; ?>
                        </h2>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nama" class="form-label text-dark">Nama Gudang</label>
                                <input type="text" name="nama" class="form-control bg-light text-dark" placeholder="Masukkan Nama Gudang" required value="<?php echo $gudang ? $gudang['nama'] : ''; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="lokasi" class="form-label text-dark">Lokasi</label>
                                <input type="text" name="lokasi" class="form-control bg-light text-dark" placeholder="Masukkan Lokasi Gudang" required value="<?php echo $gudang ? $gudang['lokasi'] : ''; ?>">
                            </div>

                            <?php if (isset($gudang)) { ?>
                                <input type="hidden" name="update_id" value="<?php echo $gudang['gudangId']; ?>">
                                <button type="submit" name="update" class="btn btn-info text-light">Update</button>
                                <a href="index.php?page=gudang" class="btn btn-secondary">Cancel</a>
                            <?php } else { ?>
                                <button type="submit" name="simpan" class="btn btn-info text-light">Simpan</button>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-8">
                <div class="card mb-4 bg-secondary text-white">
                    <div class="card-header bg-secondary text-dark">
                        <h2 class="mb-0">Data Gudang</h2>
                    </div>
                    <div class="card-body bg-light">
                        <div class="table-responsive">
                                <table class="table table-light table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Lokasi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                        $no = 1;
                                        while ($row = $result->fetch_assoc()) { ?>
                                        <tr>     
                                            <td><?php echo $no++ ?></td>
                                            <td><?php echo $row['nama']?></td>
                                            <td><?php echo $row['lokasi']?></td>
                                            <td>
                                                <form method="post" class="d-inline">
                                                    <input type="hidden" name="delete_id" value="<?php echo $row['gudangId']?>">
                                                    <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</button>
                                                </form>
                                                <a href="index.php?page=gudang&edit_id=<?php echo $row['gudangId'] ?>" class="btn btn-success">Edit</a>
                                            </td>
                                        <?php } ?>
                                        </tr>
                                    </tbody>
                                </table>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</body>