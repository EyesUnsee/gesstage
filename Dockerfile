FROM php:8.2-cli

# Installer extensions
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip \
    && docker-php-ext-install zip pdo pdo_mysql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier projet
WORKDIR /app
COPY . .

# Installer Laravel
RUN composer install

# Générer clé
RUN php artisan key:generate

# Exposer port
EXPOSE 10000

# Lancer serveur
CMD php artisan serve --host=0.0.0.0 --port=10000