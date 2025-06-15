Write-Host "🚀 Chuẩn bị deploy AppEvent lên Railway..." -ForegroundColor Green

# Kiểm tra xem có file .env không
if (-not (Test-Path ".env")) {
    Write-Host "📝 Tạo file .env từ .env.example..." -ForegroundColor Yellow
    Copy-Item ".env.example" ".env"
    Write-Host "✅ Đã tạo file .env" -ForegroundColor Green
} else {
    Write-Host "✅ File .env đã tồn tại" -ForegroundColor Green
}

# Generate APP_KEY nếu chưa có
$envContent = Get-Content ".env" -Raw
if ($envContent -notmatch "APP_KEY=base64:") {
    Write-Host "🔑 Generating APP_KEY..." -ForegroundColor Yellow
    php artisan key:generate
    Write-Host "✅ Đã generate APP_KEY" -ForegroundColor Green
} else {
    Write-Host "✅ APP_KEY đã tồn tại" -ForegroundColor Green
}

# Install dependencies
Write-Host "📦 Installing PHP dependencies..." -ForegroundColor Yellow
composer install --optimize-autoloader

Write-Host "📦 Installing Node.js dependencies..." -ForegroundColor Yellow
npm install

# Build assets
Write-Host "🏗️ Building assets..." -ForegroundColor Yellow
npm run build

# Clear và cache config
Write-Host "🧹 Clearing caches..." -ForegroundColor Yellow
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

Write-Host "📋 Caching configurations..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Kiểm tra database connection
Write-Host "🔍 Kiểm tra database connection..." -ForegroundColor Yellow
php artisan migrate:status

Write-Host ""
Write-Host "✅ Project đã sẵn sàng để deploy!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Các bước tiếp theo:" -ForegroundColor Cyan
Write-Host "1. Commit tất cả changes lên Git repository" -ForegroundColor White
Write-Host "2. Push lên GitHub/GitLab" -ForegroundColor White
Write-Host "3. Tạo project mới trên Railway" -ForegroundColor White
Write-Host "4. Connect với Git repository" -ForegroundColor White
Write-Host "5. Cấu hình environment variables" -ForegroundColor White
Write-Host "6. Deploy!" -ForegroundColor White
Write-Host ""
Write-Host "📖 Xem chi tiết trong file README_DEPLOYMENT.md" -ForegroundColor Cyan 