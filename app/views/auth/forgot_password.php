<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
    <style>
        body{
            margin:0;
            font-family: Arial, sans-serif;
            background:#f5f5f5;
        }

        .container{
            display:flex;
            height:100vh;
        }

        /* LEFT IMAGE */

        .left{
            width:50%;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        .left img{
            width:70%;
            border-radius:16px;
        }

        /* RIGHT FORM */

        .right{
            width:50%;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        .card{
            background:white;
            padding:40px;
            width:350px;
            border-radius:12px;
            box-shadow:0 10px 25px rgba(0,0,0,0.05);
        }

        .card h2{
            text-align:center;
            margin-bottom:20px;
        }

        .form-group{
            margin-bottom:16px;
        }

        input{
            width:100%;
            padding:10px;
            border-radius:8px;
            border:1px solid #ddd;
        }

        button{
            width:100%;
            padding:12px;
            background:#FFC107;
            border:none;
            border-radius:8px;
            font-weight:bold;
            cursor:pointer;
        }

        button:hover{
            background:#ffb300;
        }

        .back{
            text-align:center;
            margin-top:15px;
        }

        .back a{
            color:#00a5a5;
            text-decoration:none;
        }

        .flash {
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-weight: 600;
        }

        .flash.success {
            background: #e6f7f3;
            color: #0f8f8c;
            border: 1px solid #bce6dc;
        }

        .flash.error {
            background: #fff2f0;
            color: #c0392b;
            border: 1px solid #f3c6bf;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <img src="public/assets/library.jpg">
        </div>
        <div class="right">
            <div class="card">
                <h2>Lupa Password</h2>
                    <!-- Flash Messages -->
                        <?php if (!empty($success = $flash['success'])): ?>
                            <div class="flash success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($error = $flash['error'])): ?>
                            <div class="flash error"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                <p style="font-size:14px;color:#666;">
                Masukkan email akun anda. Kami akan mengirimkan link untuk reset password.
                </p>

                <form method="POST" action="?route=Auth/sendResetLink">

                <div class="form-group">
                <input type="email" name="email" placeholder="Masukkan Email" required>
                </div>

                <button type="submit">
                Kirim Link Reset
                </button>

                </form>

                <div class="back">
                <a href="?route=Auth/login">Kembali ke Login</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>