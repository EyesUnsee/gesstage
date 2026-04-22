FROM php:8.2-cli

# Dépendances système
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copier projet
COPY . .

# Installer dépendances Laravel
RUN composer install --no-dev --optimize-autoloader

# Permissions Laravel
RUN chmod -R 775 storage bootstrap/cache

# Script de démarrage sécurisé
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'set -e' >> /start.sh && \
    echo 'echo "Running migrations..."' >> /start.sh && \
    echo 'php artisan migrate --force || true' >> /start.sh && \
    echo 'echo "Starting server..."' >> /start.sh && \
    echo 'php artisan serve --host=0.0.0.0 --port=10000' >> /start.sh && \
    chmod +x /start.sh

EXPOSE 10000

CMD ["/start.sh"]