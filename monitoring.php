<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'kppn') {
    header("Location: index.php");
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



// Proses hapus surat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_surat_id'])) {
    $surat_id = $_POST['delete_surat_id'];
    $sql_delete = "DELETE FROM surat WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $surat_id);
    if ($stmt_delete->execute()) {
        echo "<script>alert('Surat berhasil dihapus.'); window.location.href='monitoring.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus surat: " . $stmt_delete->error . "');</script>";
    }
}
// Proses upload surat tanggapan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['surat_tanggapan'])) {
    $surat_id = $_POST['surat_id'];
    $file_name = $_FILES['surat_tanggapan']['name'];
    $file_tmp = $_FILES['surat_tanggapan']['tmp_name'];
    $file_path = "uploads/tanggapan/" . $file_name;
    
    if (move_uploaded_file($file_tmp, $file_path)) {
        $sql = "INSERT INTO tanggapan_surat (surat_id, surat_tanggapan) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $surat_id, $file_path);
        if ($stmt->execute()) {
            echo "Surat tanggapan berhasil diupload.";
        } else {
            echo "Gagal mengupload surat tanggapan: " . $stmt->error;
        }
    } else {
        echo "Gagal memindahkan file.";
    }
}

// Pagination dan pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search_term = "%$search%";

$sql = "SELECT surat.*, tanggapan_surat.surat_tanggapan FROM surat 
        LEFT JOIN tanggapan_surat ON surat.id = tanggapan_surat.surat_id 
        WHERE surat.nama_pengirim LIKE ? LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $search_term, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

$sql_count = "SELECT COUNT(*) AS total FROM surat WHERE nama_pengirim LIKE ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("s", $search_term);
$stmt_count->execute();
$result_count = $stmt_count->get_result()->fetch_assoc();
$total_rows = $result_count['total'];
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Surat Masuk - KPPN Kolaka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="text-center text-primary">Monitoring Surat - KPPN Kolaka</h2>
        
        <form method="get" class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan kode satker" value="<?= htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </div>
            </div>
        </form>
        
        <div class="text-end mb-3">
            <a href="index.php" class="btn btn-danger">Logout</a>
        </div>
        
        <table class="table table-bordered mt-4">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Jenis Koreksi</th>
                    <th>Kode Satker</th>
                    <th>Nama Satker</th>
                    <th>Nomor Surat</th>
                    <th>Akun Semula</th>
                    <th>Akun Menjadi</th>
                    <th>Nilai Koreksi</th>
                    <th>Unduh Surat Permohonan</th>
                    <th>Tanggal Upload</th>
                    <th>Upload Tanggapan</th>
                    <th>Tanggapan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['jenis_koreksi']); ?></td>
                            <td><?= htmlspecialchars($row['nama_pengirim']); ?></td>
                            <td><?= htmlspecialchars($row['satker']); ?></td>
                            <td><?= htmlspecialchars($row['nama_surat']); ?></td>
                            <td><?= htmlspecialchars($row['akun_semula']); ?></td>
                            <td><?= htmlspecialchars($row['akun_menjadi']); ?></td>
                            <td><?= htmlspecialchars($row['nilai_koreksi']); ?></td>
                            <td><a href="<?= htmlspecialchars($row['file_path']); ?>" class="btn btn-sm btn-primary" download>Unduh</a></td>
                            <td><?= htmlspecialchars($row['uploaded_at']); ?></td>
                            <td>
                                <form method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="surat_id" value="<?= $row['id']; ?>">
                                    <input type="file" name="surat_tanggapan" accept="application/pdf" required>
                                    <button type="submit" class="btn btn-sm btn-success">Download</button>
                                </form>
                            </td>
                            <td>
                                <?php if (!empty($row['surat_tanggapan'])): ?>
                                    <a href="<?= htmlspecialchars($row['surat_tanggapan']); ?>" class="btn btn-sm btn-primary" download>Unduh</a>
                                <?php else: ?>
                                    <span class="text-danger">Belum ada tanggapan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ini?');">
                                    <input type="hidden" name="delete_surat_id" value="<?= $row['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12" class="text-center">Belum ada data surat.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
