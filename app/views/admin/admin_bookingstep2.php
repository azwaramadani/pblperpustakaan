<?php
$adminName = $admin['username'] ?? ($admin['nama'] ?? 'Admin');

$initialMembers = [''];

$kapasitasMax = (int)($room['kapasitas_max'] ?? 0);
$kapasitasMin = (int)($room['kapasitas_min'] ?? 0);
$maxAnggota   = $kapasitasMax > 0 ? max(0, $kapasitasMax - 1) : PHP_INT_MAX;

$imgPath = !empty($room['gambar_ruangan']) ? $room['gambar_ruangan'] : 'public/assets/image/contohruangan.png';
$imgUrl  = preg_match('#^https?://#i', $imgPath)
          ? $imgPath
          : app_config()['base_url'].'/'.ltrim($imgPath,'/');
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<title>Lengkapi Data Peminjaman - <?= htmlspecialchars($room['nama_ruangan']) ?></title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="<?= app_config()['base_url'] ?>/public/assets/css/stylebooking2.css">

<style>

.modal-warning{
position:fixed;
inset:0;
background:rgba(0,0,0,0.6);
display:none;
align-items:center;
justify-content:center;
z-index:9999;
}

.modal-warning.active{
display:flex;
}

.modal-card{
width:320px;
background:#fff;
border-radius:14px;
padding:20px 18px 16px;
text-align:center;
box-shadow:0 20px 45px rgba(0,0,0,0.18);
}

.modal-icon{
width:58px;
height:58px;
border-radius:50%;
margin:0 auto 12px;
display:grid;
place-items:center;
background:#ff5c5c;
color:#fff;
font-size:28px;
font-weight:700;
}

.modal-title{
font-size:17px;
font-weight:700;
margin:0 0 10px;
}

.modal-text{
font-size:14px;
margin:0 0 16px;
}

.btn-modal-primary{
background:#ff5c5c;
color:#fff;
border:none;
border-radius:10px;
padding:10px 12px;
font-weight:700;
cursor:pointer;
}

</style>

</head>

<body>
<header class="navbar">

<div class="logo">
  <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoPNJ.png" height="40">
  <img src="<?= app_config()['base_url'] ?>/public/assets/image/LogoRudy.png" height="40">
</div>

<div class="profile-dropdown">
<div class="profile-trigger">

<img src="<?= app_config()['base_url'] ?>/public/assets/image/userlogo.png">

<div class="user-name">
<a href="?route=Admin/dataRuangan" style="text-decoration:none;color:black;">
<p><?= htmlspecialchars($adminName) ?></p>
</a>
</div>

</div>
</div>

</header>

<main>

<div class="room-header">

<div class="room-image-container">
<img src="<?= htmlspecialchars($imgUrl) ?>" class="room-image" style="object-fit:cover;">
</div>

<div class="room-details">

<h2><?= htmlspecialchars($room['nama_ruangan']) ?></h2>

<p><?= htmlspecialchars($room['deskripsi'] ?? 'Ruangan Study.') ?></p>

<p class="capacity">
Kapasitas:
<?= htmlspecialchars($room['kapasitas_min']) ?>
-
<?= htmlspecialchars($room['kapasitas_max']) ?>
orang
</p>

<h3>Waktu Peminjaman:</h3>

<p>Tanggal: <strong><?= htmlspecialchars($payload['tanggal']) ? date('d M Y', strtotime($payload['tanggal'])) : '-' ?></strong></p>
<p>Jam: <strong><?= htmlspecialchars($payload['jam_mulai']) ?> </strong> - <strong> <?= htmlspecialchars($payload['jam_selesai']) ?> </strong></p>

<p style="margin-top:8px;font-weight:600;"> Maks anggota:<?= $kapasitasMax > 0 ? $maxAnggota : 'tidak dibatasi' ?>(1 slot penanggung jawab).</p>

</div>
</div>

<div class="card">
    <h1>Lengkapi Data Peminjaman</h1>

    <form action="?route=Booking/adminStore" method="POST" id="bookingForm">

    <input type="hidden" name="room_id" value="<?= htmlspecialchars($payload['room_id']) ?>">
    <input type="hidden" name="tanggal" value="<?= htmlspecialchars($payload['tanggal']) ?>">
    <input type="hidden" name="jam_mulai" value="<?= htmlspecialchars($payload['jam_mulai']) ?>">
    <input type="hidden" name="jam_selesai" value="<?= htmlspecialchars($payload['jam_selesai']) ?>">

    <input type="hidden" name="jumlah_peminjam" id="jumlahPeminjam">

    <div class="form-group">
    <label>Nama penanggung jawab</label>
    <input class="input-line" type="text" name="nama_penanggung_jawab" required>
    </div>

    <div class="form-group">
    <label>NIM/NIP penanggung jawab</label>
    <input class="input-line" type="text" name="nimnip_penanggung_jawab" required>
    </div>

    <div class="form-group">
    <label>Email penanggung jawab</label>
    <input class="input-line" type="email" name="email_penanggung_jawab" required>
    </div>

    <div class="form-group">
    <label>Jumlah peminjam</label>
    <input class="input-line" type="number" name="jumlah_peminjam_display" min="2" value="2">
    </div>

    <div class="anggota-wrap" id="anggotaList">

    <div class="form-group anggota-item">
    <label>NIP Anggota 1</label>
    <input class="input-line anggota-input" type="text" name="nim_anggota[]" required>
    </div>

    </div>

    <button type="button" class="add-btn" id="addAnggota">+ Tambah Anggota</button>

    <div class="actions">

    <a href="?route=Booking/adminStep1/<?= urlencode($payload['room_id']) ?>" class="btn-back">
    Kembali
    </a>

    <button type="submit" class="btn-save">
    Simpan
    </button>
    </div>
    </form>
</div>

</main>

<!-- WARNING MODAL -->

<div id="warningModal" class="modal-warning">

<div class="modal-card">

<div class="modal-icon">!</div>

<div class="modal-title">Perhatian</div>

<p id="warningText" class="modal-text"></p>

<button class="btn-modal-primary" onclick="closeWarning()">OK</button>

</div>

</div>

<!-- SUCCESS MODAL -->

<div id="successModal" class="modal-overlay">

<div class="modal-content">

<div class="success-icon-container">

<svg viewBox="0 0 24 24" fill="none" stroke="currentColor">

<polyline points="20 6 9 17 4 12"></polyline>

</svg>

</div>

<h2 class="modal-title">Booking berhasil disimpan</h2>

<div class="modal-actions">

<a href="?route=Admin/dataFromAdminCreateBooking" class="btn-modal btn-modal-yellow">
Kembali ke data booking
</a>

</div>

</div>

</div>

<script>

const anggotaList = document.getElementById("anggotaList");
const addBtn = document.getElementById("addAnggota");
const bookingForm = document.getElementById("bookingForm");

const jumlahHidden = document.getElementById("jumlahPeminjam");
const jumlahDisplay = document.querySelector('input[name="jumlah_peminjam_display"]');

let anggotaCount = 1;

const kapasitasMax = <?= $kapasitasMax ?>;
const kapasitasMin = <?= $kapasitasMin ?>;
const maxAnggota   = <?= $maxAnggota === PHP_INT_MAX ? 'Infinity' : $maxAnggota ?>;

function showWarning(msg){

document.getElementById("warningText").textContent = msg;

document.getElementById("warningModal").classList.add("active");

}

function closeWarning(){

document.getElementById("warningModal").classList.remove("active");

}

function addAnggotaField(){

if(maxAnggota !== Infinity && anggotaCount >= maxAnggota){

showWarning("Jumlah anggota melebihi kapasitas");

return;

}

anggotaCount++;

const div=document.createElement("div");

div.className="form-group anggota-item";

div.innerHTML=`
<label>NIM Anggota ${anggotaCount}</label>
<input class="input-line anggota-input" type="text" name="nim_anggota[]" required>
`;

anggotaList.appendChild(div);

}

addBtn.addEventListener("click",addAnggotaField);

bookingForm.addEventListener("submit",function(e){

e.preventDefault();

const anggotaInputs = Array.from(document.querySelectorAll(".anggota-input"));

const anyEmpty = anggotaInputs.some(inp => inp.value.trim()==="");

if(anyEmpty){

showWarning("Isi semua NIM anggota.");

return;

}

const filledMembers = anggotaInputs.map(i=>i.value.trim());

const total = 1 + filledMembers.length;

if(kapasitasMax>0 && total>kapasitasMax){

showWarning("Jumlah peminjam melebihi kapasitas ruangan.");

return;

}

if(kapasitasMin>0 && total<kapasitasMin){

showWarning("Jumlah peminjam kurang dari kapasitas minimum.");

return;

}

jumlahHidden.value = total;

jumlahDisplay.value = total;

const formData = new FormData(bookingForm);

fetch(bookingForm.action,{

method:"POST",

body:formData

})
.then(res=>res.json())

.then(data=>{

if(data.success){

document.getElementById("successModal").classList.add("active");

}else{

showWarning(data.message);

}

})

.catch(err=>{

console.error(err);

showWarning("Terjadi kesalahan sistem.");

});

});

</script>

</body>
</html>