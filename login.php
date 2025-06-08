<?php
session_start();
include 'koneksi.php'; // pastikan file koneksi database sudah dibuat

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query user berdasarkan username
    $query = mysqli_query($conn, "SELECT * FROM pengguna WHERE username = '$username' LIMIT 1");
    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);

        // Verifikasi password hash
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['id_pengguna'] = $user['id_pengguna'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

            // Redirect sesuai role
            if ($user['role'] == 'admin') {
                header("Location: dashboard.php");
                exit;
            } elseif ($user['role'] == 'kasir') {
                header("Location: dashboard.php");
                exit;
            } elseif ($user['role'] == 'manajer') {
                header("Location: dashboard.php");
                exit;
            } else {
                // role tidak dikenal
                $error = "Role pengguna tidak valid.";
            }
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Username tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Login POS</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; }
        .login-box { width: 300px; margin: 100px auto; padding: 20px; background: white; border-radius: 6px; box-shadow: 0 0 10px #aaa; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; margin: 8px 0; box-sizing: border-box; }
        input[type="submit"] { width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .error { color: red; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login POS</h2>
        <?php if (!empty($error)) echo '<div class="error">'.$error.'</div>'; ?>
        <form method="POST" action="">
            <label>Username</label>
            <input type="text" name="username" required />

            <label>Password</label>
            <input type="password" name="password" required />

            <input type="submit" name="login" value="Login" />
        </form>
    </div>
</body>
</html>
