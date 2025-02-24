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

// Ambil data dari database
$sql = "SELECT * FROM mphlbjs";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring MPHLBJS</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .container { max-width: 1000px; margin: auto; }
    </style>
</head>

    <div class="text-end mb-3">
            <a href="index.php" class="btn btn-danger">Logout</a>
        </div>
    <body>
    <div class="container">
        <h2 class="text-center">Monitoring MPHLBJS</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Satker</th>
                    <th>Kode Satker</th>
                    <th>Register Hibah</th>
                    <th>Bentuk Hibah</th>
                    <th>Nilai Hibah (Rp)</th>
                    <th>Dokumen</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['nama_satker']}</td>
                            <td>{$row['kode_satker']}</td>
                            <td>{$row['register_hibah']}</td>
                            <td>{$row['bentuk_hibah']}</td>
                            <td>" . number_format($row['nilai_hibah'], 0, ',', '.') . "</td>
                            <td><a href='{$row['dokumen_register']}' target='_blank' class='btn btn-primary btn-sm'>Unduh</a></td>
                        </tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>Belum ada data</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>