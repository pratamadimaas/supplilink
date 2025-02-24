<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$password = "";
$dbname = "upload_surat";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses upload file
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jenis_koreksi = htmlspecialchars($_POST['jenis_koreksi']);
    $nama_surat = htmlspecialchars($_POST['nama_surat']);
    $nama_pengirim = htmlspecialchars($_POST['nama_pengirim']);
    $satker = htmlspecialchars($_POST['satker']);
    $akun_semula = htmlspecialchars($_POST['akun_semula']);
    $akun_menjadi = htmlspecialchars($_POST['akun_menjadi']);
    $nilai_koreksi = htmlspecialchars($_POST['nilai_koreksi']);
    $file = $_FILES['file_surat'];

    // **USER ID HARUS ADA**
    $user_id = 4; // Ganti dengan user yang sedang login (contoh)

    // **CEK APAKAH USER ID VALID**
    $userCheck = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $userCheck->bind_param("i", $user_id);
    $userCheck->execute();
    $userCheck->store_result();

    if ($userCheck->num_rows === 0) {
        echo "<div class='alert alert-danger'>User ID tidak valid.</div>";
        exit;
    }
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $uniqueFileName = uniqid() . "_" . basename($file['name']);
        $targetFile = $targetDir . $uniqueFileName;

        // Buat folder uploads jika belum ada
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Cek hanya menerima file PDF
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if ($fileType !== 'pdf') {
            echo "<div class='alert alert-danger'>Hanya file PDF yang diperbolehkan.</div>";
        } else {
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                // Masukkan ke database dengan user_id
                $stmt = $conn->prepare("INSERT INTO surat (user_id, jenis_koreksi, nama_surat, nama_pengirim, satker, akun_semula, akun_menjadi, nilai_koreksi, file_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssssds", $user_id, $jenis_koreksi, $nama_surat, $nama_pengirim, $satker, $akun_semula, $akun_menjadi, $nilai_koreksi, $targetFile);

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>Surat berhasil diupload.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Terjadi kesalahan saat mengupload surat.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Terjadi kesalahan saat mengupload file.</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Tidak ada file yang diupload atau terjadi kesalahan.</div>";
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Surat Koreksi - KPPN Kolaka</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            color: #000;
            background-image: url("https://i.imgur.com/GMmCQHC.png");
            background-repeat: no-repeat;
            background-size: cover;
        }
        .card {
            padding: 30px 40px;
            margin-top: 60px;
            border: none;
            box-shadow: 0 6px 12px 0 rgba(0,0,0,0.2);
        }
        .blue-text {
            color: #00BCD4;
        }
        .logout-btn {
            position: absolute;
            top: 15px;
            right: 30px;
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <button class="logout-btn" onclick="window.location.href='index.php';">Logout</button>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <h3 class="text-center">Upload Surat Permohonan Koreksi</h3>
                    <p class="blue-text text-center">Mohon isi data berikut untuk mengunggah surat Permohonan</p>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Jenis Koreksi <span class="text-danger">*</span></label>
                            <select name="jenis_koreksi" class="form-control" required>
                                <option value="" disabled selected>Pilih Jenis Koreksi</option>
                                <option value="koreksi_penerimaan">Koreksi Penerimaan</option>
                                <option value="koreksi_spm">Koreksi SPM</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nama Satker <span class="text-danger">*</span></label>
                            <input type="text" name="satker" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Kode Satker <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pengirim" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nomor Surat <span class="text-danger">*</span></label>
                            <input type="text" name="nama_surat" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Akun Semula <span class="text-danger">*</span></label>
                            <input type="text" name="akun_semula" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Akun Menjadi <span class="text-danger">*</span></label>
                            <input type="text" name="akun_menjadi" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nilai Koreksi (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="nilai_koreksi" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>File Surat Permohonan Koreksi dari Satker (PDF) <span class="text-danger">*</span></label>
                            <input type="file" name="file_surat" class="form-control" accept=".pdf" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
