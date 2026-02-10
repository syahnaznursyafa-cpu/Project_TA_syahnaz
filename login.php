<?php
session_start();
include "koneksi.php";

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama_pengguna = $_POST['nama_pengguna'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $cek_query = mysqli_query($conn,
        "SELECT * FROM user WHERE username='$username' OR email='$email'"
    );

    if (mysqli_num_rows($cek_query) > 0) {
        echo "<script>alert('Username atau Email sudah terdaftar');</script>";
    } else {
        $insert_query = mysqli_query($conn,
            "INSERT INTO user (username, password, nama_pengguna, email, role) 
             VALUES ('$username', '$password', '$nama_pengguna', '$email', '$role')"
        );

        if ($insert_query) {
            echo "<script>alert('Registrasi berhasil! Silakan login.');</script>";
        } else {
            echo "<script>alert('Registrasi gagal: " . mysqli_error($conn) . "');</script>";
        }
    }
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn,
        "SELECT * FROM user WHERE username='$username' AND password='$password'"
    );

    $user = mysqli_fetch_assoc($query);

    if ($user) {
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit;
    } else {
        echo "<script>alert('Username atau Password salah');</script>";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register</title>
    <link rel="stylesheet" href="style.css">
    <!-- ✅ ADD FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- form login -->
        <div class="form-box login">
            <form action="login.php" method="POST">
                <h1>Login</h1>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required>
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class="fa-solid fa-lock"></i>
                </div>
                <button type="submit" name="login" class="btn">Login</button>
            </form>
        </div>

        <!-- form Register -->
        <div class="form-box register">
            <form action="login.php" method="POST">
                <h1>Register</h1>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required>
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div class="input-box">
                    <input type="text" name="nama_pengguna" placeholder="Nama Pengguna" required>
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="input-box">
                    <select name="role" id="role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                    <i class="fa-solid fa-lock"></i>
                </div>
                <button type="submit" name="register" class="btn">Register</button>
            </form>
        </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Hello Welcome</h1>
                <p>Don't have an account?</p>
                <button type="button" class="register-btn">Register</button>
            </div>

            <div class="toggle-panel toggle-right">
                <h1>Welcome!</h1>
                <p>Already have an account?</p>
                <button type="button" class="login-btn">Login</button>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>