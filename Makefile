# Makefile - Docker 開發環境便捷指令
.PHONY: help build up down shell composer-install test cs php-version

# 預設顯示說明
help:
	@echo "藍新金流 SDK - Docker 開發環境指令"
	@echo ""
	@echo "使用方式: make [指令]"
	@echo ""
	@echo "可用指令:"
	@echo "  build            建構 Docker 映像檔"
	@echo "  up               啟動容器（背景執行）"
	@echo "  down             停止並移除容器"
	@echo "  shell            進入容器 shell"
	@echo "  composer-install 安裝 Composer 依賴"
	@echo "  composer-update  更新 Composer 依賴"
	@echo "  test             執行測試"
	@echo "  cs               執行程式碼風格檢查"
	@echo "  cs-fix           自動修正程式碼風格"
	@echo "  php-version      顯示 PHP 版本"

# 建構 Docker 映像檔
build:
	docker-compose build

# 啟動容器
up:
	docker-compose up -d

# 停止容器
down:
	docker-compose down

# 進入容器 shell
shell:
	docker-compose exec php bash

# 安裝 Composer 依賴
composer-install:
	docker-compose run --rm php composer install

# 更新 Composer 依賴
composer-update:
	docker-compose run --rm php composer update

# 執行測試
test:
	docker-compose run --rm php vendor/bin/phpunit

# 程式碼風格檢查
cs:
	docker-compose run --rm php vendor/bin/phpcs --standard=PSR12 src/

# 自動修正程式碼風格
cs-fix:
	docker-compose run --rm php vendor/bin/phpcbf --standard=PSR12 src/

# 顯示 PHP 版本
php-version:
	docker-compose run --rm php php -v

