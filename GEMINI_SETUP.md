# Hướng dẫn cấu hình Gemini AI API

## Tổng quan
Chức năng **AI Suggestions** sử dụng Google Gemini 2.0 Flash để tạo ra các gợi ý thông minh cho sự kiện. Tính năng này yêu cầu API key từ Google AI Studio.

## Bước 1: Lấy Gemini API Key

1. **Truy cập Google AI Studio**
   - Mở trình duyệt và truy cập: https://makersuite.google.com/app/apikey
   - Đăng nhập bằng tài khoản Google của bạn

2. **Tạo API Key mới**
   - Click vào nút "Create API Key"
   - Chọn Google Cloud project (hoặc tạo mới nếu chưa có)
   - Copy API key được tạo

3. **Lưu API Key**
   - API key có dạng: `AIzaSyA...` (khoảng 39 ký tự)
   - Lưu trữ an toàn, không chia sẻ công khai

## Bước 2: Cấu hình trong Laravel

1. **Thêm vào file .env**
   ```bash
   # Gemini AI API Configuration
   GEMINI_API_KEY=AIzaSyA_your_actual_api_key_here
   ```

2. **Restart Laravel server**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan serve
   ```

## Bước 3: Test kết nối

1. **Chạy command test**
   ```bash
   php artisan gemini:test
   ```

2. **Kết quả mong đợi**
   - ✅ Kết nối thành công!
   - Hiển thị response từ Gemini AI

## Troubleshooting

### Lỗi 400 - Bad Request
- **Nguyên nhân**: API key không hợp lệ hoặc model không tồn tại
- **Giải pháp**: 
  - Kiểm tra lại API key
  - Đảm bảo sử dụng model `gemini-2.0-flash`

### Lỗi 403 - Forbidden
- **Nguyên nhân**: API key không có quyền truy cập hoặc hết quota
- **Giải pháp**:
  - Kiểm tra quota trong Google Cloud Console
  - Kích hoạt Generative AI API

### Lỗi 429 - Too Many Requests
- **Nguyên nhân**: Vượt quá rate limit
- **Giải pháp**: Đợi một lúc và thử lại

### API Key chưa được cấu hình
- **Triệu chứng**: Thông báo "GEMINI_API_KEY chưa được cấu hình"
- **Giải pháp**: Thêm API key vào file .env và restart server

## Tính năng AI Suggestions

Sau khi cấu hình thành công, bạn có thể:

1. **Tạo gợi ý từ trang Events**
   - Vào chi tiết sự kiện
   - Tab "AI Suggestions"
   - Click "Tạo gợi ý mới"

2. **Các loại gợi ý**
   - **Budget**: Gợi ý ngân sách chi tiết
   - **Timeline**: Gợi ý lịch trình thực hiện
   - **Checklist**: Gợi ý công việc cần làm
   - **Supplier**: Gợi ý nhà cung cấp
   - **General**: Gợi ý tổng quát

3. **Quản lý gợi ý**
   - Chấp nhận/từ chối gợi ý
   - Đánh giá và phản hồi
   - Đánh dấu yêu thích

## API Endpoint

```
POST https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={API_KEY}
```

## Cấu hình API

```json
{
  "contents": [...],
  "generationConfig": {
    "temperature": 0.7,
    "topP": 0.8,
    "topK": 40,
    "maxOutputTokens": 2048
  },
  "safetySettings": [...]
}
```

## Lưu ý bảo mật

1. **Không commit API key** vào source code
2. **Sử dụng .env** để lưu trữ API key
3. **Giới hạn quyền** của API key chỉ cho Generative AI
4. **Monitor usage** để tránh vượt quota

## Hỗ trợ

Nếu gặp vấn đề, vui lòng:
1. Chạy `php artisan gemini:test` để kiểm tra kết nối
2. Kiểm tra logs trong `storage/logs/laravel.log`
3. Đọc documentation tại: https://ai.google.dev/docs 