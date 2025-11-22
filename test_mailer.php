<?php
require 'vendor/autoload.php';
require 'core/helper.php';

sendMail('test@example.com', 'Test Mailpit', 'Ini email uji coba.');

echo "Cek Mailpit di http://127.0.0.1:8025";
