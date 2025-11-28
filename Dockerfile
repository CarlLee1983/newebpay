# PHP 7.4 開發環境
FROM php:7.4-cli

# 安裝系統依賴
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# 安裝 PHP 擴展
RUN docker-php-ext-install zip

# 安裝 Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# 設定工作目錄
WORKDIR /app

# 設定 Composer 允許以 root 執行（開發環境用）
ENV COMPOSER_ALLOW_SUPERUSER=1

# 預設命令
CMD ["php", "-v"]

