<?php
session_start();
include 'config/koneksi.php';

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM pengguna WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true); // keamanan tambahan
            $_SESSION['id_pengguna'] = $user['id_pengguna'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['id_toko'] = $user['id_toko']; 

            header("Location: dashboard.php");
            exit;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<script>
function togglePassword() {
    const passwordInput = document.getElementById("password");
    const toggleIcon = document.getElementById("togglePassword");
    
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    }
}
</script>

<body>
    <div class="login-box">
        <h2>Login POS</h2>
        <?php if (!empty($error)) echo '<div class="error">'.$error.'</div>'; ?>
        <form method="POST" action="">
            <label>Username</label>
            <input type="text" name="username" required />

        <label>Password</label>
            <div style="position: relative;">
                <input type="password" name="password" id="password" required style="padding-right: 35px;" />
                <i class="fa-solid fa-eye" id="togglePassword" onclick="togglePassword()" 
                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
                        cursor: pointer; color: #666;"></i>
            </div>

            <input type="submit" name="login" value="Login" />
        </form>
    </div>
</body>
</html>
