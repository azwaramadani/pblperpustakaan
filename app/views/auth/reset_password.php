<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
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

        /* LEFT */
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

        /* RIGHT */
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
    </style>
</head>
<body>
<div class="container">
    <div class="left">
        <img src="public/assets/library.jpg">
    </div>
    <div class="right">
        <div class="card">
            <h2>Reset Password</h2>
            <form method="POST" action="?route=Auth/updatePassword">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <div class="form-group">
                <input type="password" name="password" placeholder="Password Baru" required>
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
            </div>
            <button type="submit">
                Update Password
            </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>