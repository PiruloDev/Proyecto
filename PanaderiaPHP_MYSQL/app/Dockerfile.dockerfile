# Usa una imagen base de PHP con Apache del repositorio de devcontainers
FROM mcr.microsoft.com/devcontainers/php:8.2-apache

# Instalar extensiones PHP necesarias para MySQL y otras comunes
# El 'export DEBIAN_FRONTEND=noninteractive' evita que apt-get pida interacción
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get -y install --no-install-recommends \
    php-mysql \        # Extensión para conectar a MySQL
    php-mbstring \     # Extensión para funciones de cadena multibyte (común)
    php-gd \           # Extensión para manipulación de imágenes (común)
    php-curl \         # Para hacer peticiones HTTP
    php-json \         # Para trabajar con JSON
    php-xml \          # Para trabajar con XML
    php-zip \          # Para trabajar con archivos ZIP
    unzip \            # Utilidad para descomprimir (útil para Composer)
    git \              # Git (ya suele estar, pero para asegurar)
    # Agrega otras extensiones que tu proyecto necesite
    && apt-get clean && rm -rf /var/lib/apt/lists/* # Limpia la caché de APT

# Copiar el archivo de configuración de Apache personalizado al contenedor
# Esto reemplaza el archivo de configuración por defecto de Apache
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Habilitar el módulo rewrite de Apache (esencial para URLs amigables en muchos frameworks PHP)
RUN a2enmod rewrite

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# El CMD final para Apache, asegurando que se ejecute en primer plano
# Esto es necesario para que el contenedor se mantenga activo
CMD ["apache2ctl", "-D", "FOREGROUND"]