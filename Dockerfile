FROM php:8.2-cli

# Installer dépendances système + extensions PHP
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copier les fichiers du projet
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Permissions Laravel
RUN chmod -R 775 storage bootstrap/cache

# Exposer port Render
EXPOSE 10000

# Lancer Laravel
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000