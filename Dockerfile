# Gunakan image PHP dengan Apache
FROM php:8.1-apache

# Install ekstensi PHP yang diperlukan (PDO MySQL umum untuk web)
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Aktifkan modul rewrite Apache (penting untuk routing PHP)
RUN a2enmod rewrite

# Salin source code dari repo ke dalam container
COPY . /var/www/html/

# Berikan izin akses folder
RUN chown -R www-data:www-data /var/www/html
