<?php 
include '../koneksi.php';

$id       = $_POST['id'];
$nama     = $_POST['nama'];
$username = $_POST['username'];
$pwd      = $_POST['password'];
$level    = $_POST['level'];

// Jika password diisi, enkripsi dengan md5
$password = !empty($pwd) ? md5($pwd) : null;

// Penanganan gambar
$rand      = rand();
$allowed   = array('gif', 'png', 'jpg', 'jpeg');
$filename  = $_FILES['foto']['name'];
$ext       = pathinfo($filename, PATHINFO_EXTENSION);

// Ambil data pengguna lama untuk menghapus foto lama jika perlu
$userData = mysqli_query($koneksi, "SELECT user_foto FROM user WHERE user_id='$id'");
$userOld  = mysqli_fetch_assoc($userData);
$oldFoto  = $userOld['user_foto'];

// Jika password dan gambar kosong, update data selain password dan foto
if (empty($pwd) && empty($filename)) {
    mysqli_query($koneksi, "UPDATE user SET user_nama='$nama', user_username='$username', user_level='$level' WHERE user_id='$id'");
    header("location:user.php");

// Jika hanya password kosong, update data dengan foto baru
} elseif (empty($pwd)) {
    if (!in_array($ext, $allowed)) {
        header("location:user.php?alert=gagal");
    } else {
        $newFoto = $rand . '_' . $filename;
        move_uploaded_file($_FILES['foto']['tmp_name'], '../gambar/user/' . $newFoto);

        // Hapus foto lama jika ada
        if ($oldFoto != "" && file_exists('../gambar/user/' . $oldFoto)) {
            unlink('../gambar/user/' . $oldFoto);
        }

        mysqli_query($koneksi, "UPDATE user SET user_nama='$nama', user_username='$username', user_foto='$newFoto', user_level='$level' WHERE user_id='$id'");
        header("location:user.php?alert=berhasil");
    }

// Jika hanya gambar kosong, update data dengan password baru
} elseif (empty($filename)) {
    mysqli_query($koneksi, "UPDATE user SET user_nama='$nama', user_username='$username', user_password='$password', user_level='$level' WHERE user_id='$id'");
    header("location:user.php");

// Jika password dan gambar diisi, update semua data
} else {
    if (!in_array($ext, $allowed)) {
        header("location:user.php?alert=gagal");
    } else {
        $newFoto = $rand . '_' . $filename;
        move_uploaded_file($_FILES['foto']['tmp_name'], '../gambar/user/' . $newFoto);

        // Hapus foto lama jika ada
        if ($oldFoto != "" && file_exists('../gambar/user/' . $oldFoto)) {
            unlink('../gambar/user/' . $oldFoto);
        }

        mysqli_query($koneksi, "UPDATE user SET user_nama='$nama', user_username='$username', user_password='$password', user_foto='$newFoto', user_level='$level' WHERE user_id='$id'");
        header("location:user.php?alert=berhasil");
    }
}
?>
