<?php
session_start();
include('db.php');

if (!isset($_SESSION['adminId']) && basename($_SERVER['PHP_SELF']) != 'login.php'){
    header('Location: login.php');
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'inventory';

$low_stock_treshold = 5;
$stmt = $conn->prepare("SELECT * from inventory where stok <= ?");
$stmt->bind_param("i",$low_stock_treshold);
$stmt->execute();
$low_stock_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Dashboard</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container-fluid p-0">
        <div class="row g-0">


            <div class="col-md-3 col-lg-2 d-md-block bg-secondary text-light vh-100 position-fixed">
                <div class="d-flex flex-column vh-100 p-3">
                    <h2 class="text-center mb-4">Dashboard</h2>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="index.php?page=inventory" class="nav-link text-light <?php echo $page == 'inventory' ? 'active bg-dark' : ''; ?>">
                                Data Barang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=gudang" class="nav-link text-light <?php echo $page == 'gudang' ? 'active bg-dark' : ''; ?>">
                                Data Gudang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=vendor" class="nav-link text-light <?php echo $page == 'vendor' ? 'active bg-dark' : ''; ?>">
                                Data Vendor
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=transaksi" class="nav-link text-light <?php echo $page == 'transaksi' ? 'active bg-dark' : ''; ?>">
                                Transaksi Barang
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div>
                        <a href="logout.php" class="btn btn-danger w-100">Logout</a>
                    </div>
                </div>
            </div>


            <div class="col-md-9 ms-auto col-lg-10 px-0">


                <nav class="navbar navbar-expand-lg navbar-secondary bg-secondary shadow-sm">
                    <div class="container-fluid">
                        <span class="navbar-brand">Inventory Management</span>
                        <div class="d-flex">
                            <span class="text-dark me-3">Welcome, Admin!</span>
                        </div>
                    </div>
                </nav>


                <div class="container-fluid px-4 mt-4">

                    <?php if (!empty($low_stock_items)): ?>
                            <div class="alert alert-danger">
                                <h5 class="text-dark">Peringatan Barang Stok Rendah!!!</h5>
                            </div>
                    <?php endif; ?>

                    <div id="content">
                        <?php
                        switch ($page) {
                            case 'inventory':
                                include('inventory.php');
                                break;
                            case 'gudang':
                                include('gudang.php');
                                break;
                            case 'vendor':
                                include('vendor.php');
                                break;
                            case 'transaksi':
                                include('transaksi.php');
                                break;
                            
                            default:
                            include('inventory.php');
                            break;
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>