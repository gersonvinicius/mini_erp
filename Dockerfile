# Imagem oficial do PHP com Apache
FROM php:7.4-apache

# Extensões necessárias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Módulo de reescrita do Apache
RUN a2enmod rewrite

# Copiar o projeto para o diretório padrão do Apache
COPY . /var/www/html

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Expor a porta 80 para o servidor Apache
EXPOSE 80