<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Perbaiki Data Registrasi</title>

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
    margin-bottom:10px;
}

.info{
    font-size:14px;
    margin-bottom:15px;
    color:#555;
}

.error{
    background:#ffe5e5;
    color:#b30000;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
    font-size:14px;
}

.success{
    background:#e6ffed;
    color:#0f5132;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
    font-size:14px;
}

.form-group{
    margin-bottom:16px;
}

label{
    display:block;
    margin-bottom:6px;
    font-size:14px;
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

<!-- LEFT -->
<div class="left">
    <img src="public/assets/library.jpg">
</div>

<!-- RIGHT -->
<div class="right">

<div class="card">

<h2>Perbaiki Data</h2>

<div class="info">
Silakan upload ulang bukti aktivasi Kubaca agar akun dapat diverifikasi kembali.
</div>

<!-- FLASH MESSAGE -->
<?php if ($error = Session::get('flash_error')): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success = Session::get('flash_success')): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<!-- ALASAN DITOLAK -->
<?php if (!empty($user['rejection_reason'])): ?>
    <div class="error">
        Ditolak karena: <?= htmlspecialchars($user['rejection_reason']) ?>
    </div>
<?php endif; ?>

<form action="?route=Auth/submitFixRegistration" method="POST" enctype="multipart/form-data">

<div class="form-group">
    <label>Upload Bukti Aktivasi Kubaca</label>
    <input type="file" name="bukti" required>
</div>

<button type="submit">
    Submit Ulang
</button>

</form>

</div>

</div>

</div>

</body>
</html>