# 🚀 Deploy Nhanh AppEvent lên Railway

## Bước 1: Chạy script chuẩn bị (Windows)
```powershell
.\deploy-setup.ps1
```

## Bước 2: Commit và Push code
```bash
git add .
git commit -m "Chuẩn bị deploy lên Railway"
git push origin main
```

## Bước 3: Deploy trên Railway
1. Truy cập [railway.app](https://railway.app)
2. Đăng nhập bằng GitHub
3. Click "New Project" → "Deploy from GitHub repo"
4. Chọn repository AppEvent
5. Railway sẽ tự động build và deploy

## Bước 4: Thêm Database
1. Click "Add Service" → "Database" → "MySQL"
2. Copy thông tin database từ Variables tab

## Bước 5: Cấu hình Environment Variables
Vào tab "Variables" và thêm:
```
APP_NAME=AppEvent
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.railway.app

# Database (copy từ MySQL service)
DB_CONNECTION=mysql
DB_HOST=containers-us-west-xxx.railway.app
DB_PORT=xxxx
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=xxxxxxxx

# Gemini AI
GEMINI_API_KEY=your_gemini_api_key_here
```

## Bước 6: Chạy Migration
1. Vào tab "Deployments" → Click deployment mới nhất
2. Mở terminal và chạy:
```bash
php artisan migrate --force
php artisan db:seed --force
```

## ✅ Hoàn thành!
App sẽ có sẵn tại: `https://your-app-name.railway.app`

---
📖 **Hướng dẫn chi tiết:** Xem file `README_DEPLOYMENT.md` 