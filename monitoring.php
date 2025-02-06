<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kppn') {
    header("Location: login.php");
    exit;
}

// Koneksi ke database
$host = "localhost";
$user = "root";
$password = "";
$dbname = "upload_surat";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Menghapus surat berdasarkan ID
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Cek apakah file ada dan hapus file yang terkait
    $sql = "SELECT file_path FROM surat WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($file_path);
        $stmt->fetch();
        
        // Hapus file di server jika ada
        if (file_exists($file_path)) {
            unlink($file_path); // Hapus file
        }
    }
    
    // Hapus data surat dari database
    $sql = "DELETE FROM surat WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    
    // Redirect untuk mencegah pengiriman ulang data saat refresh
    header("Location: monitoring.php");
    exit;
}

// Ambil data surat dari database
$sql = "SELECT * FROM surat";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Surat Masuk - KPPN Kolaka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center text-primary">Monitoring Surat - KPPN Kolaka</h2>
                <!-- Tombol logout di kanan atas -->
                <div class="text-end mb-3">
                    <a href="login.php" class="btn btn-danger">Logout</a>
                </div>
                <table class="table table-bordered mt-4">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>Nama Surat</th>
                            <th>Nama Pengirim</th>
                            <th>Satker</th>
                            <th>Kode Satker</th>
                            <th>File</th>
                            <th>Uploaded At</th>
                            <th>Aksi</th> <!-- Kolom Aksi untuk tombol hapus -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nama_surat']); ?></td>
                                    <td><?= htmlspecialchars($row['nama_pengirim']); ?></td>
                                    <td><?= htmlspecialchars($row['satker']); ?></td>
                                    <td><?= htmlspecialchars($row['kode_satker']); ?></td>
                                    <td><a href="<?= htmlspecialchars($row['file_path']); ?>" class="btn btn-sm btn-primary" download>Unduh</a></td>
                                    <td><?= htmlspecialchars($row['uploaded_at']); ?></td>
                                    <td>
                                        <!-- Tombol hapus -->
                                        <a href="?delete_id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus surat ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data surat.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
