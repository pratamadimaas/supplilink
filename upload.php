<?php
// Koneksi ke database
$host = "localhost";
$user = "root";
$password = "";
$dbname = "upload_surat";

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'satker') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Buat tabel jika belum ada
$sql = "CREATE TABLE IF NOT EXISTS surat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_surat VARCHAR(255) NOT NULL,
    nama_pengirim VARCHAR(255) NOT NULL,
    satker VARCHAR(255) NOT NULL,
    kode_satker VARCHAR(10) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($sql)) {
    die("Error membuat tabel: " . $conn->error);
}

// Proses upload file jika ada permintaan POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_surat = htmlspecialchars($_POST['nama_surat']);
    $nama_pengirim = htmlspecialchars($_POST['nama_pengirim']);
    $satker = htmlspecialchars($_POST['satker']);
    $kode_satker = htmlspecialchars($_POST['kode_satker']);
    $file = $_FILES['file_surat'];

    // Validasi file upload
    if ($file['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $uniqueFileName = uniqid() . "_" . basename($file['name']);
        $targetFile = $targetDir . $uniqueFileName;

        // Cek apakah folder "uploads" ada, jika tidak buat foldernya
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Hanya izinkan file PDF
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if ($fileType !== 'pdf') {
            echo "<div class='alert alert-danger'>Hanya file PDF yang diperbolehkan.</div>";
        } else {
            // Pindahkan file ke folder target
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                // Simpan informasi ke database
                $stmt = $conn->prepare("INSERT INTO surat (nama_surat, nama_pengirim, satker, kode_satker, file_path) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $nama_surat, $nama_pengirim, $satker, $kode_satker, $targetFile);

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>Surat berhasil diunggah.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Gagal menyimpan data ke database: " . $stmt->error . "</div>";
                }
                $stmt->close();
            } else {
                echo "<div class='alert alert-danger'>Gagal mengunggah file.</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Terjadi kesalahan saat mengunggah file.</div>";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi KPPN Kolaka</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            color: #000;
            overflow-x: hidden;
            height: 100%;
            background-image: url("https://i.imgur.com/GMmCQHC.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }
        .card {
            padding: 30px 40px;
            margin-top: 60px;
            margin-bottom: 60px;
            border: none !important;
            box-shadow: 0 6px 12px 0 rgba(0,0,0,0.2);
        }
        .blue-text {
            color: #00BCD4;
        }
        .form-control-label {
            margin-bottom: 0;
        }
        input, textarea, button {
            padding: 8px 15px;
            border-radius: 5px !important;
            margin: 5px 0px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            font-size: 18px !important;
            font-weight: 300;
        }
        input:focus, textarea:focus {
            box-shadow: none !important;
            border: 1px solid #00BCD4;
            outline-width: 0;
            font-weight: 400;
        }
        .btn-block {
            text-transform: uppercase;
            font-size: 15px !important;
            font-weight: 400;
            height: 43px;
            cursor: pointer;
        }
        .btn-block:hover {
            color: #fff !important;
        }
        button:focus {
            box-shadow: none !important;
            outline-width: 0;
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
            font-size: 16px;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <button class="logout-btn" onclick="window.location.href='login.php';">Logout</button>
    <div class="container-fluid px-1 py-5 mx-auto">
        <div class="row d-flex justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-9 col-11 text-center">
                <h3>Upload Surat Penonaktifan Supplier</h3>
                <p class="blue-text">Mohon isi data di bawah ini untuk mengunggah surat penonaktifan supplier</p>
                <div class="card">
                    <h5 class="text-center mb-4">Form Permohonan Surat</h5>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="row justify-content-between text-left">
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Nomor Surat<span class="text-danger"> *</span></label>
                                <input type="text" id="nama_surat" name="nama_surat" placeholder="" required>
                            </div>
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Nama Pengirim<span class="text-danger"> *</span></label>
                                <input type="text" id="nama_pengirim" name="nama_pengirim" placeholder="" required>
                            </div>
                        </div>
                        <div class="row justify-content-between text-left">
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Satker<span class="text-danger"> *</span></label>
                                <input type="text" id="satker" name="satker" placeholder="" required>
                            </div>
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Kode Satker<span class="text-danger"> *</span></label>
                                <input type="text" id="kode_satker" name="kode_satker" placeholder="" required>
                            </div>
                        </div>
                        <div class="row justify-content-between text-left">
                            <div class="form-group col-12 flex-column d-flex">
                                <label class="form-control-label px-3">File Surat (PDF)<span class="text-danger"> *</span></label>
                                <input type="file" id="file_surat" name="file_surat" accept=".pdf" required>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="form-group col-sm-6">
                                <button type="submit" class="btn-block btn-primary">Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>
