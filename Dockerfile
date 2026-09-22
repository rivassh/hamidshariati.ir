# hamidshariati.ir - WordPress + Node.js games
# Multi-stage: base WordPress + Node.js for games

FROM php:8.3-apache

# Install WordPress dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libjpeg-dev libfreetype6-dev libzip-dev libicu-dev \
    libonig-dev libxml2-dev libpq-dev unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql zip bcmath gd intl mysqli

# Install Node.js for games
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    rm -rf /var/lib/apt/lists/*

# Copy WordPress files
COPY . /var/www/html/

# Configure Apache
RUN a2enmod rewrite

# Install composer for any PHP deps
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]