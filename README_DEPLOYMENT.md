# Hướng dẫn Deploy AppEvent lên Railway

## Bước 1: Chuẩn bị project

1. Đảm bảo project đã được commit lên Git repository (GitHub, GitLab, hoặc Bitbucket)
2. Các file cấu hình đã được tạo:
   - `railway.json` - Cấu hình Railway
   - `Procfile` - Lệnh khởi chạy
   - `nixpacks.toml` - Cấu hình build

## Bước 2: Tạo tài khoản Railway

1. Truy cập [railway.app](https://railway.app)
2. Đăng ký tài khoản bằng GitHub (khuyến nghị)
3. Xác thực tài khoản

## Bước 3: Deploy project

1. Đăng nhập vào Railway Dashboard
2. Click "New Project"
3. Chọn "Deploy from GitHub repo"
4. Chọn repository chứa project AppEvent
5. Railway sẽ tự động detect Laravel và bắt đầu build

## Bước 4: Cấu hình Database

1. Trong Railway Dashboard, click "Add Service"
2. Chọn "Database" → "MySQL"
3. Railway sẽ tạo MySQL database và cung cấp connection string

## Bước 5: Cấu hình Environment Variables

Trong Railway Dashboard, vào tab "Variables" và thêm các biến sau:

```
APP_NAME=AppEvent
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.railway.app
APP_KEY=base64:your_generated_key

DB_CONNECTION=mysql
DB_HOST=containers-us-west-xxx.railway.app
DB_PORT=xxxx
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=your_db_password

GEMINI_API_KEY=your_gemini_api_key_here
```

**Lưu ý:** Thay thế các giá trị database bằng thông tin từ MySQL service mà Railway cung cấp.

## Bước 6: Generate APP_KEY

1. Trong Railway Dashboard, vào tab "Deployments"
2. Click vào deployment mới nhất
3. Mở terminal và chạy: `php artisan key:generate --show`
4. Copy key và thêm vào biến môi trường `APP_KEY`

## Bước 7: Chạy Migration

1. Trong Railway terminal, chạy:
```bash
php artisan migrate --force
php artisan db:seed --force
```

## Bước 8: Cấu hình Domain (Tùy chọn)

1. Railway sẽ tự động cung cấp subdomain: `your-app-name.railway.app`
2. Để sử dụng domain riêng, vào tab "Settings" → "Domains"
3. Thêm custom domain và cấu hình DNS

## Lưu ý quan trọng

1. **File Storage**: Railway sử dụng ephemeral filesystem, file upload sẽ bị mất khi restart. Khuyến nghị sử dụng cloud storage (AWS S3, Cloudinary)

2. **Database Backup**: Thường xuyên backup database từ Railway dashboard

3. **Environment Variables**: Không commit file `.env` lên Git, chỉ sử dụng Railway Variables

4. **Logs**: Xem logs trong Railway Dashboard tab "Deployments"

## Troubleshooting

### Lỗi 500 Internal Server Error
- Kiểm tra `APP_KEY` đã được set chưa
- Kiểm tra database connection
- Xem logs trong Railway Dashboard

### Lỗi Database Connection
- Kiểm tra các biến DB_* trong Variables
- Đảm bảo MySQL service đang chạy
- Kiểm tra firewall rules

### Lỗi Build Failed
- Kiểm tra `composer.json` và `package.json`
- Xem build logs trong Railway Dashboard
- Đảm bảo PHP version tương thích

## Support

Nếu gặp vấn đề, có thể:
1. Xem Railway documentation: [docs.railway.app](https://docs.railway.app)
2. Tham gia Railway Discord community
3. Kiểm tra Laravel deployment guides 