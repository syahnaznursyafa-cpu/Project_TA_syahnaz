<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$username = $_SESSION['username'];

if (isset($_POST['use_feature'])) {
    $fitur = $_POST['use_feature'];
    $teks = $_POST['teks'];

    $insert_query = mysqli_query(
        $conn,
        "INSERT INTO request (id_user, fitur, tanggal) 
         VALUES ('$id_user', '$fitur', NOW())"
    );

    if ($insert_query) {
        // Redirect to feature processing page
        $_SESSION['fitur'] = $fitur;
        $_SESSION['teks'] = $teks;
        if ($fitur == 'tata_bahasa') {
            header("Location: penyunting_bahasa.php");
        } else {
            header("Location: asisten_ai.php");
        }
        exit;
    } else {
        $error = "Gagal menggunakan fitur: " . mysqli_error($conn);
    }
}

// Get user statistics
$total_request = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) total FROM request WHERE id_user = '$id_user'")
)['total'];

$bahasa_usage = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) total FROM request WHERE id_user = '$id_user' AND fitur='tata_bahasa'")
)['total'];

$ai_usage = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) total FROM request WHERE id_user = '$id_user' AND fitur='asisten_ai'")
)['total'];

// Get recent requests
$requests_query = mysqli_query(
    $conn,
    "SELECT * FROM request WHERE id_user = '$id_user' ORDER BY tanggal DESC LIMIT 10"
);

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'home';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, white, #6594B1);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            color: white;
        }

        th,
        td {
            padding: 12px 15px;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.5);
        }

        th {
            background-color: rgba(96, 146, 182, 0.8);
        }

        td {
            background-color: rgba(116, 165, 202, 0.8);
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #333;
        }

        .header-info {
            text-align: right;
        }

        .header-info p {
            color: #666;
            margin: 5px 0;
        }

        .logout-btn {
            background-color: red;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            font-weight: bold;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card h3 {
            color: #213C51;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .card p {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            flex-wrap: wrap;
        }

        .tabs a {
            padding: 10px 20px;
            background: #f0f0f0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }

        .tabs a.active {
            background: #213C51;
            color: white;
        }

        .tabs a:hover {
            background: #213C51;
            color: white;
        }

        .content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .feature-box {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #213C51;
        }

        .feature-box h3 {
            color: #213C51;
            margin-bottom: 10px;
        }

        .feature-box p {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .feature-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            color: #333;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group textarea {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Arial', sans-serif;
            min-height: 120px;
            resize: vertical;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: #213C51;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #213C51;
            color: white;
        }

        .btn-primary:hover {
            background: #6594B1;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .history-table th {
            background-color: #213C51;
            color: white;
            padding: 12px;
            text-align: left;
        }

        .history-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .history-table tr:hover {
            background-color: #f5f5f5;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-bahasa {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .status-ai {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .empty-state svg {
            width: 60px;
            height: 60px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Dashboard</h1>
            </div>
            <div class="header-info">
                <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
                <p><strong>Role:</strong> User</p>
                <a href="../logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <h3>Total Penggunaan</h3>
                <p><?= $total_request ?></p>
            </div>
            <div class="card">
                <h3>Penyunting Bahasa</h3>
                <p><?= $bahasa_usage ?></p>
            </div>
            <div class="card">
                <h3>Asisten Informatika</h3>
                <p><?= $ai_usage ?></p>
            </div>
        </div>

        <div class="tabs">
            <a href="?tab=home" class="<?= $tab === 'home' ? 'active' : '' ?>">Beranda</a>
            <a href="?tab=features" class="<?= $tab === 'features' ? 'active' : '' ?>">Fitur</a>
            <a href="?tab=history" class="<?= $tab === 'history' ? 'active' : '' ?>">Riwayat</a>
        </div>

        <div class="content">
            <!-- Tab Beranda -->
            <div class="tab-content <?= $tab === 'home' ? 'active' : '' ?>">
                <h2>Selamat Datang, <?= htmlspecialchars($username) ?>!</h2>
                <p style="color: #666; margin: 20px 0; line-height: 1.8;">
                    Platform ini menyediakan dua fitur utama untuk membantu Anda:
                </p>

                <div class="feature-box">
                    <h3>📝 Penyunting Bahasa Indonesia</h3>
                    <p>
                        Fitur ini membantu Anda memeriksa dan memperbaiki kesalahan tata bahasa, ejaan,
                        dan gaya penulisan dalam teks berbahasa Indonesia.
                    </p>
                </div>

                <div class="feature-box">
                    <h3>🤖 Asisten Belajar Informatika</h3>
                    <p>
                        Asisten AI ini siap membantu Anda dalam belajar pemrograman dan informatika.
                        Tanyakan pertanyaan, minta penjelasan konsep, atau minta bantuan dengan kode Anda.
                    </p>
                </div>

                <h3 style="margin-top: 30px; margin-bottom: 15px;">Statistik Aktivitas Anda</h3>
                <p style="color: #666;">
                    Total penggunaan: <strong><?= $total_request ?></strong> kali |
                    Penyunting Bahasa: <strong><?= $bahasa_usage ?></strong> kali |
                    Asisten Informatika: <strong><?= $ai_usage ?></strong> kali
                </p>
            </div>

            <!-- Tab Fitur -->
            <div class="tab-content <?= $tab === 'features' ? 'active' : '' ?>">
                <h2>Gunakan Fitur</h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?= $error ?></div>
                <?php endif; ?><br>

                <div class="feature-box">
                    <h3>📝 Penyunting Bahasa Indonesia</h3>
                    <p>Masukkan teks bahasa Indonesia yang ingin Anda periksa dan perbaiki:</p>
                    <form method="POST" class="feature-form">
                        <div class="form-group">
                            <label for="teks_bahasa">Teks yang akan diperiksa:</label>
                            <textarea id="teks_bahasa" name="teks" placeholder="Masukkan teks Anda di sini..."
                                required></textarea>
                        </div>
                        <button type="submit" name="use_feature" value="tata_bahasa" class="btn btn-primary"
                            onclick="document.querySelector('input[name=fitur]').value='tata_bahasa'">
                            Periksa Teks
                        </button>
                        <input type="hidden" name="fitur" value="">
                    </form>
                </div>

                <div class="feature-box">
                    <h3>🤖 Asisten Belajar Informatika</h3>
                    <p>Tanyakan pertanyaan tentang pemrograman atau informatika:</p>
                    <form method="POST" class="feature-form">
                        <div class="form-group">
                            <label for="teks_ai">Pertanyaan Anda:</label>
                            <textarea id="teks_ai" name="teks"
                                placeholder="Tanyakan tentang pemrograman, algoritma, atau topik informatika lainnya..."
                                required></textarea>
                        </div>
                        <button type="submit" name="use_feature" value="asisten_ai" class="btn btn-primary"
                            onclick="document.querySelector('input[name=fitur]').value='asisten_ai'">
                            Tanya AI
                        </button>
                        <input type="hidden" name="fitur" value="">
                    </form>
                </div>
            </div>

            <!-- Tab Riwayat -->
            <div class="tab-content <?= $tab === 'history' ? 'active' : '' ?>">
                <h2>Riwayat Penggunaan</h2><br>

                <?php if ($requests_query && mysqli_num_rows($requests_query) > 0): ?>
                    <table class="history-table">
                        <tr>
                            <th>No</th>
                            <th>Fitur</th>
                            <th>Tanggal</th>
                        </tr>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($requests_query)) {
                            $fitur = $row['fitur'] == 'tata_bahasa'
                                ? 'Penyunting Bahasa'
                                : 'Asisten Informatika';

                            $tanggal = date('d-m-Y H:i', strtotime($row['tanggal']));
                            echo "
                <tr>
                    <td>$no</td>
                    <td>$fitur</td>
                    <td>$tanggal</td>
                </tr>";
                            $no++;
                        }
                        ?>
                    </table>
                <?php else: ?>
                    <p>Belum ada riwayat penggunaan.</p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</body>

</html>