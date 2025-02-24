<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "upload_surat";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];  // Mendapatkan role yang dipilih

    // Validasi input tidak boleh kosong
    if (empty($username) || empty($password) || empty($role)) {
        $error = "Semua kolom harus diisi.";
    } else {
        $stmt = $conn->prepare("SELECT id, role, password FROM users WHERE username = ? AND role = ?");
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $db_role, $db_password);
            $stmt->fetch();
            if (password_verify($password, $db_password)) {  // Verifikasi password
                session_regenerate_id(true); // Mencegah sesi dibajak
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $db_role;

                // Redirect berdasarkan role yang dipilih
                if ($db_role === 'satker') {
                    header("Location: upload.php");
                } elseif ($db_role === 'kppn') {
                    header("Location: menu_monitoring.php");
                } elseif ($db_role === 'veraki') {
                    header("Location: mphlbjs.php");
                }
                exit;
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Username atau role salah.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - KPPN Kolaka</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">

    <style>
        /* Animasi Fade-in untuk seluruh halaman */
        body {
            animation: fadeIn 1.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Efek transisi pada form */
        .form-container {
            animation: slideUp 1s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .divider:after,
        .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
        }

        .h-custom {
            height: calc(100% - 73px);
        }

        @media (max-width: 450px) {
            .h-custom {
                height: 100%;
            }
        }
    </style>
</head>

<body>

    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                        class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1 form-container">
                    <form action="" method="POST">
                        <div class="divider d-flex align-items-center my-4">
                            <p class="text-center fw-bold mx-3 mb-0">Koreksi KPPN Kolaka</p>
                        </div>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <div class="form-group mb-4">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" class="form-control form-control-lg"
                                placeholder="Masukkan Username" required />
                        </div>

                        <div class="form-group mb-4">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-control form-control-lg"
                                placeholder="Masukkan Password" required />
                        </div>

                        <div class="form-group mb-4">
                            <label for="role">Pilih Role</label>
                            <select name="role" class="form-control form-control-lg" required>
                                <option value="veraki">MPHLBJS</option>
                                <option value="satker">Permohonan Koreksi</option>
                                <option value="kppn">Tanggapan Koreksi/Nota Konfirmasi</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check mb-0">
                                <input class="form-check-input me-2" type="checkbox" value="" id="form2Example3" />
                                <label class="form-check-label" for="form2Example3">
                                    Ingat Saya
                                </label>
                            </div>
                        </div>

                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button type="submit" name="login" class="btn btn-primary btn-lg"
                                style="padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-primary">
            <div class="text-white mb-3 mb-md-0">
                Copyright © 2024. KPPN Kolaka.
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/js/all.min.js"></script>

</body>
</html>
