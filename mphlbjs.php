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
    $nama_satker = htmlspecialchars($_POST['nama_satker']);
    $kode_satker = htmlspecialchars($_POST['kode_satker']);
    $register_hibah = htmlspecialchars($_POST['register_hibah']);
    $bentuk_hibah = htmlspecialchars($_POST['bentuk_hibah']);
    $nilai_hibah = htmlspecialchars($_POST['nilai_hibah']); // Pastikan angka
    $file = $_FILES['file_dokumen'];

    // Validasi ukuran file (maksimum 2MB)
    if ($file['size'] > 2 * 1024 * 1024) {
        echo "<div class='alert alert-danger'>Ukuran file terlalu besar. Maksimum 2MB.</div>";
    } elseif ($file['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $uniqueFileName = uniqid() . "_" . basename($file['name']);
        $targetFile = $targetDir . $uniqueFileName;

        // Buat folder jika belum ada
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Cek hanya menerima file PDF
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if ($fileType !== 'pdf') {
            echo "<div class='alert alert-danger'>Hanya file PDF yang diperbolehkan.</div>";
        } else {
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                // Masukkan ke database
                $stmt = $conn->prepare("INSERT INTO mphlbjs (nama_satker, kode_satker, register_hibah, bentuk_hibah, nilai_hibah, dokumen_register) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $nama_satker, $kode_satker, $register_hibah, $bentuk_hibah, $nilai_hibah, $targetFile);

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>Data hibah berhasil diupload.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Terjadi kesalahan saat menyimpan data: " . $conn->error . "</div>";
                }
                $stmt->close();
            } else {
                echo "<div class='alert alert-danger'>Gagal mengupload file.</div>";
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
    <title>MPHLBJS - KPPN Kolaka</title>
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
                    <h3 class="text-center">Upload Surat MPHLBJS</h3>
                    <p class="blue-text text-center">Mohon isi data berikut untuk mengunggah MPHLBJS</p>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Nama Satker <span class="text-danger">*</span></label>
                            <input type="text" name="nama_satker" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Kode Satker <span class="text-danger">*</span></label>
                            <input type="text" name="kode_satker" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Register Hibah <span class="text-danger">*</span></label>
                            <input type="text" name="register_hibah" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Bentuk Hibah / Jenis Barang <span class="text-danger">*</span></label>
                            <input type="text" name="bentuk_hibah" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nilai Hibah (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="nilai_hibah" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Dokumen Register (PDF) <span class="text-danger">*</span></label>
                            <input type="file" name="file_dokumen" class="form-control" accept=".pdf" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
