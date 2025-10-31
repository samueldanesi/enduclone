# Dockerfile for SportEvents (PHP + Apache)
FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Set the working directory
WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

# Set Apache DocumentRoot to public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}/!g' /etc/apache2/apache2.conf \
    && sed -ri -e 's!Directory /var/www/!Directory ${APACHE_DOCUMENT_ROOT}/!g' /etc/apache2/apache2.conf || true

# Ensure uploads dir exists and is writable
RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 775 /var/www/html/uploads

# Expose port 80
EXPOSE 80

# Healthcheck (optional)
HEALTHCHECK --interval=30s --timeout=3s CMD curl -fsS http://localhost/ || exit 1

CMD ["apache2-foreground"]
