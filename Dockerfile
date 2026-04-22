FROM php:8.2-apache

# Install required PHP extensions (mysqli, pdo, gd)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Enable Apache mod_rewrite for .htaccess support
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html/

# Copy project files
COPY . /var/www/html/

# Set proper permissions for the uploads folder
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Replace standard Apache port 80 with the PORT environment variable provided by Render
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Set a default value for PORT
ENV PORT=10000

# Expose the defined port
EXPOSE $PORT

# Start Apache
CMD ["apache2-foreground"]
