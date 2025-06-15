#!/bin/bash

echo "🚀 Chuẩn bị deploy AppEvent lên Railway..."

# Kiểm tra xem có file .env không
if [ ! -f .env ]; then
    echo "📝 Tạo file .env từ .env.example..."
    cp .env.example .env
    echo "✅ Đã tạo file .env"
else
    echo "✅ File .env đã tồn tại"
fi

# Generate APP_KEY nếu chưa có
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate
    echo "✅ Đã generate APP_KEY"
else
    echo "✅ APP_KEY đã tồn tại"
fi

# Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --optimize-autoloader

echo "📦 Installing Node.js dependencies..."
npm install

# Build assets
echo "🏗️ Building assets..."
npm run build

# Clear và cache config
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "📋 Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Kiểm tra database connection
echo "🔍 Kiểm tra database connection..."
php artisan migrate:status

echo ""
echo "✅ Project đã sẵn sàng để deploy!"
echo ""
echo "📋 Các bước tiếp theo:"
echo "1. Commit tất cả changes lên Git repository"
echo "2. Push lên GitHub/GitLab"
echo "3. Tạo project mới trên Railway"
echo "4. Connect với Git repository"
echo "5. Cấu hình environment variables"
echo "6. Deploy!"
echo ""
echo "📖 Xem chi tiết trong file README_DEPLOYMENT.md" 