# Gunakan gambar PHP dengan Apache yang sudah menyertakan GD Library
FROM php:7.4-apache

# Install ekstensi PDO dan PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Install paket libpng-dev, libjpeg-dev, dan freetype untuk ekstensi GD
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev

# Aktifkan ekstensi GD dengan konfigurasi freetype
RUN docker-php-ext-configure gd --with-jpeg --with-freetype && docker-php-ext-install gd

# Atur direktori kerja ke folder Laravel
WORKDIR /var/www/html

# Salin file proyek Laravel ke dalam kontainer
COPY . .

# Setel hak akses agar web server dapat mengakses file
RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R 775 /var/www/html/storage
RUN sed -i '/<Directory \/var\/www\/html\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
RUN a2enmod rewrite
RUN service apache2 restart