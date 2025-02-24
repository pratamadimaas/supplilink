<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "upload_surat";

// Create a database connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $new_username = $_POST['new_username'];
    $new_password = $_POST['new_password'];
    $role = $_POST['role'];

    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $new_username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $register_error = "Username sudah terdaftar.";
    } else {
        // Hash the password before storing it
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $new_username, $hashed_password, $role);

        if ($stmt->execute()) {
            $register_success = "Registrasi berhasil! Silakan login.";
        } else {
            $register_error = "Terjadi kesalahan saat registrasi. Coba lagi.";
        }
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Registrasi Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }

        .card {
            border-radius: 8px;
        }

        .form-outline input {
            margin-bottom: 1rem;
        }

        .btn-block {
            width: 100%;
        }

        .form-label {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- Section: Design Block -->
    <section>
        <!-- Jumbotron -->
        <div class="px-4 py-5 px-md-5 text-center text-lg-start" style="background-color: hsl(0, 0%, 96%)">
            <div class="container">
                <div class="row gx-lg-5 align-items-center">
                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <h1 class="my-5 display-3 fw-bold ls-tight">
                            Buat Akun Baru! <br />
                            <span class="text-primary">Untuk Mengakses Fitur</span>
                        </h1>
                        <p style="color: hsl(217, 10%, 50.8%)">
                            Daftar untuk mulai menggunakan aplikasi kami. Isi formulir untuk memulai.
                        </p>
                    </div>

                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <div class="card">
                            <div class="card-body py-5 px-md-5">
                                <form action="" method="POST">
                                    <!-- Username Field -->
                                    <div class="mb-4">
                                        <label class="form-label" for="username">Username</label>
                                        <input type="text" id="username" name="new_username" class="form-control" required />
                                    </div>

                                    <!-- Password Field -->
                                    <div class="mb-4">
                                        <label class="form-label" for="password">Password</label>
                                        <input type="password" id="password" name="new_password" class="form-control" required />
                                    </div>

                                    <!-- Role Field -->
                                    <div class="mb-4">
                                        <label class="form-label" for="role">Pilih Role</label>
                                        <select class="form-select" name="role" required>
                                            <option value="veraki">MPHLBJS</option>
                                            <option value="satker">Permohonan</option>
                                            <option value="kppn">Tanggapan Koreksi</option>
                                        </select>
                                    </div>

                                    <!-- Registration Success/Failure Messages -->
                                    <?php if (isset($register_error)): ?>
                                        <div class="alert alert-danger"><?php echo $register_error; ?></div>
                                    <?php elseif (isset($register_success)): ?>
                                        <div class="alert alert-success"><?php echo $register_success; ?></div>
                                    <?php endif; ?>

                                    <!-- Submit Button -->
                                    <button type="submit" name="register" class="btn btn-primary btn-block mb-4">Daftar Akun</button>

                                    <!-- Additional Links -->
                                    <div class="text-center">
                                        <p>Sudah punya akun? <a href="index.php">Login</a></p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Jumbotron -->
    </section>
    <!-- Section: Design Block -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
