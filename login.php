<?php 
session_start();
include('db.php');

$message = '';
if (isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin where email = ?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password,$row['password'])) {
            $_SESSION['adminId'] = $row['adminId'];
            header('Location: index.php');
            exit;
        } else {
            $message = "Password Salah!!";
        }
    }else {
        $message = 'Email Tidak Ditemukan !';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="text-center mb-4">Login admin</h2>
                        <form method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" id="email" placeholder="Masukkan email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" id="password" placeholder="Masukkan password" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-info w-100">Login</button>
                            <?php if ($message): ?>
                                <div class="alert alert-danger mt-3" role="alert">
                                    <?php echo $message; ?>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>