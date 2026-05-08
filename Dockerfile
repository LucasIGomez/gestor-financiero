FROM php:8.3-cli

# Instalar dependencias del sistema para las extensiones PHP
RUN apt-get update && apt-get install -y libicu-dev && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql intl

WORKDIR /app
COPY . .
EXPOSE 8080
CMD php -S 0.0.0.0:${PORT:-8080} -t .
