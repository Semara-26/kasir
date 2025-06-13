<?php
session_start();
include 'koneksi.php';

// --- Pengecekan Akses ---
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id_edit = $_GET['id'] ?? 0;

if (!$id_edit) {
    header('Location: pengguna.php');
    exit;
}

$error = '';

// --- Proses Form Update ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Bisa kosong
    $role = $_POST['role'];
    $id_toko = $_POST['id_toko'];
    $status_aktif = $_POST['status_aktif'];

    if (empty($nama_lengkap) || empty($username) || empty($role) || empty($id_toko)) {
        $error = "Field Nama, Username, Role, dan Toko tidak boleh kosong.";
    } else {
        // Logika untuk update password
        if (!empty($password)) {
            // Jika password baru diisi, hash dan update
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE pengguna SET nama_lengkap=?, username=?, password=?, role=?, id_toko=?, status_aktif=? WHERE id_pengguna=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssssiii", $nama_lengkap, $username, $hashed_password, $role, $id_toko, $status_aktif, $id_edit);
        } else {
            // Jika password kosong, jangan update password
            $query = "UPDATE pengguna SET nama_lengkap=?, username=?, role=?, id_toko=?, status_aktif=? WHERE id_pengguna=?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssiii", $nama_lengkap, $username, $role, $id_toko, $status_aktif, $id_edit);
        }

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['pesan_sukses'] = "Data pengguna berhasil diupdate.";
            header('Location: pengguna.php');
            exit;
        } else {
            if(mysqli_errno($conn) == 1062){
                $error = "Gagal update: Username '$username' sudah digunakan oleh pengguna lain.";
            } else {
                $error = "Gagal update pengguna. Error: " . mysqli_stmt_error($stmt);
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Ambil data pengguna yang akan diedit
$stmt_data = mysqli_prepare($conn, "SELECT * FROM pengguna WHERE id_pengguna = ?");
mysqli_stmt_bind_param($stmt_data, "i", $id_edit);
mysqli_stmt_execute($stmt_data);
$result = mysqli_stmt_get_result($stmt_data);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt_data);

if (!$data) {
    die("Pengguna tidak ditemukan.");
}

// Ambil data toko untuk dropdown
$toko_query = mysqli_query($conn, "SELECT id_toko, nama_toko FROM toko");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pengguna - POS Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow">
        <div class="card-body">
            <h2 class="card-title mb-4">Edit Pengguna</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" required value="<?= htmlspecialchars($data['nama_lengkap']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($data['username']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control">
                    <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="admin" <?= $data['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="kasir" <?= $data['role'] == 'kasir' ? 'selected' : ''; ?>>Kasir</option>
                        <option value="manajer" <?= $data['role'] == 'manajer' ? 'selected' : ''; ?>>Manajer</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Toko</label>
                    <select name="id_toko" class="form-select" required>
                        <?php mysqli_data_seek($toko_query, 0); // Reset pointer query toko ?>
                        <?php while($toko = mysqli_fetch_assoc($toko_query)): ?>
                            <option value="<?= $toko['id_toko'] ?>" <?= $data['id_toko'] == $toko['id_toko'] ? 'selected' : ''; ?>><?= htmlspecialchars($toko['nama_toko']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status_aktif" class="form-select" required>
                        <option value="1" <?= $data['status_aktif'] == '1' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="0" <?= $data['status_aktif'] == '0' ? 'selected' : ''; ?>>Tidak Aktif</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Pengguna</button>
                <a href="pengguna.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>