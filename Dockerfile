FROM php:8.3-cli

# Instalar extensiones necesarias para ClariFi
RUN docker-php-ext-install pdo pdo_mysql intl

# Directorio de trabajo
WORKDIR /app

# Copiar el proyecto
COPY . .

# Exponer el puerto que Railway asigna
EXPOSE 8080

# Iniciar el servidor PHP built-in
CMD php -S 0.0.0.0:${PORT:-8080} -t .
