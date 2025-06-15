# ApEvent - Hệ thống quản lý sự kiện

## Tổng quan

ApEvent là một hệ thống quản lý sự kiện toàn diện được xây dựng bằng Laravel, giúp quản lý và tổ chức các sự kiện một cách hiệu quả.

## Các chức năng chính đã được implement

### 1. 📅 **Timeline Management** (Quản lý timeline)

**Chức năng:**
- Tạo và quản lý các mốc thời gian quan trọng của sự kiện
- Theo dõi tiến độ thực hiện
- Cảnh báo những task bị trễ hạn
- Quản lý phụ thuộc giữa các task

**Các tính năng:**
- ✅ CRUD đầy đủ cho timeline items
- ✅ Cập nhật trạng thái timeline (pending, in_progress, completed, cancelled, delayed)
- ✅ Tính toán thời gian overdue
- ✅ Milestone tracking
- ✅ Ước tính và thực tế thời gian thực hiện

**API Endpoints:**
- `GET /timelines` - Danh sách timeline
- `POST /timelines` - Tạo timeline mới
- `GET /timelines/{id}` - Chi tiết timeline
- `PUT /timelines/{id}` - Cập nhật timeline
- `DELETE /timelines/{id}` - Xóa timeline
- `PATCH /timelines/{id}/status` - Cập nhật trạng thái

### 2. 🏢 **Supplier Management** (Quản lý nhà cung cấp)

**Chức năng:**
- Quản lý database nhà cung cấp dịch vụ
- Đánh giá và xếp hạng nhà cung cấp
- Quản lý hợp đồng và thỏa thuận
- Tìm kiếm nhà cung cấp theo tiêu chí

**Các tính năng:**
- ✅ CRUD đầy đủ cho suppliers
- ✅ Hệ thống đánh giá rating
- ✅ Quản lý trạng thái (verified, preferred)
- ✅ Tìm kiếm theo category, price range
- ✅ Attach/detach suppliers vào events
- ✅ Quản lý thông tin hợp đồng

**API Endpoints:**
- `GET /suppliers` - Danh sách nhà cung cấp
- `POST /suppliers` - Tạo nhà cung cấp mới
- `GET /suppliers/{id}` - Chi tiết nhà cung cấp
- `PUT /suppliers/{id}` - Cập nhật nhà cung cấp
- `PATCH /suppliers/{id}/toggle-verified` - Toggle trạng thái verified
- `PATCH /suppliers/{id}/toggle-preferred` - Toggle trạng thái preferred
- `GET /api/suppliers/search` - Tìm kiếm nhà cung cấp

### 3. ✅ **Checklist Management** (Quản lý checklist)

**Chức năng:**
- Tạo và quản lý danh sách công việc cần thực hiện
- Theo dõi tiến độ hoàn thành
- Quản lý deadline và reminder
- Sắp xếp thứ tự ưu tiên

**Các tính năng:**
- ✅ CRUD đầy đủ cho checklist items
- ✅ Drag & drop reordering
- ✅ Quản lý due date và reminder
- ✅ Phân loại theo category và priority
- ✅ Tracking chi phí estimated vs actual
- ✅ Duplicate checklist items
- ✅ Quản lý approval workflow

**API Endpoints:**
- `GET /checklists` - Danh sách checklist
- `POST /checklists` - Tạo checklist mới
- `GET /checklists/{id}` - Chi tiết checklist
- `PUT /checklists/{id}` - Cập nhật checklist
- `PATCH /checklists/{id}/status` - Cập nhật trạng thái
- `POST /checklists/reorder` - Sắp xếp lại thứ tự
- `POST /checklists/{id}/duplicate` - Sao chép checklist

### 4. 🤖 **AI Suggestions** (Gợi ý AI với Gemini)

**Chức năng:**
- Tích hợp Gemini API để tạo gợi ý thông minh
- Phân tích context sự kiện để đưa ra đề xuất
- Quản lý và đánh giá các gợi ý AI
- Hỗ trợ multiple loại gợi ý

**Các tính năng:**
- ✅ Integration với Gemini Pro API
- ✅ Tự động tạo gợi ý dựa trên event context
- ✅ Hỗ trợ nhiều loại: budget, timeline, checklist, supplier, general
- ✅ Confidence scoring system
- ✅ User rating và feedback
- ✅ Favorite suggestions
- ✅ Accept/reject workflow

**Loại gợi ý:**
- **Budget**: Gợi ý ngân sách chi tiết, optimization
- **Timeline**: Gợi ý lịch trình, milestone quan trọng
- **Checklist**: Gợi ý công việc cần làm
- **Supplier**: Gợi ý nhà cung cấp phù hợp
- **General**: Gợi ý tổng quát cải thiện sự kiện

**API Endpoints:**
- `GET /ai-suggestions` - Danh sách gợi ý AI
- `POST /ai-suggestions` - Tạo gợi ý manual
- `POST /events/{id}/ai-suggestions/generate` - Tạo gợi ý từ AI
- `PATCH /ai-suggestions/{id}/status` - Cập nhật trạng thái
- `PATCH /ai-suggestions/{id}/rate` - Đánh giá gợi ý
- `PATCH /ai-suggestions/{id}/favorite` - Toggle yêu thích

### 5. 📊 **Event Reports** (Báo cáo sự kiện)

**Chức năng:**
- Tạo báo cáo tự động dựa trên dữ liệu sự kiện
- Phân tích performance và ROI
- Export báo cáo ra nhiều format
- Quản lý workflow approval

**Các tính năng:**
- ✅ Tự động generate reports từ event data
- ✅ Multiple report types: summary, financial, final
- ✅ Tính toán metrics: success score, ROI, budget variance
- ✅ Export to PDF/HTML
- ✅ Report status workflow
- ✅ Duplicate và template system

**Loại báo cáo:**
- **Summary**: Tổng quan tiến độ và hoạt động
- **Financial**: Phân tích chi tiết ngân sách và chi phí  
- **Final**: Báo cáo tổng kết toàn diện
- **Custom**: Báo cáo tùy chỉnh theo yêu cầu

**API Endpoints:**
- `GET /event-reports` - Danh sách báo cáo
- `POST /event-reports` - Tạo báo cáo manual
- `POST /events/{id}/reports/generate` - Tạo báo cáo tự động
- `PATCH /event-reports/{id}/status` - Cập nhật trạng thái
- `GET /event-reports/{id}/export-pdf` - Export PDF
- `POST /event-reports/{id}/duplicate` - Sao chép báo cáo

## Cấu hình cần thiết

### Environment Variables

Thêm vào file `.env`:

```bash
# Gemini AI API
GEMINI_API_KEY=your_gemini_api_key_here
```

### Lấy Gemini API Key

1. Truy cập [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Tạo API key mới
3. Copy và dán vào file `.env`

## Database Setup

```bash
# Chạy migrations
php artisan migrate

# Seed dữ liệu mẫu (tuỳ chọn)
php artisan db:seed
```

## Usage Examples

### Tạo Timeline cho Event

```php
Timeline::create([
    'event_id' => 1,
    'title' => 'Thiết kế backdrop',
    'description' => 'Hoàn thiện thiết kế backdrop chính',
    'start_time' => '2024-01-15 09:00:00',
    'end_time' => '2024-01-15 17:00:00',
    'priority' => 'high',
    'is_milestone' => true
]);
```

### Tạo gợi ý AI

```javascript
// Gọi API để tạo gợi ý
fetch('/events/1/ai-suggestions/generate', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        suggestion_type: 'budget',
        prompt: 'Tối ưu ngân sách cho sự kiện cưới 200 khách'
    })
})
```

### Tạo báo cáo tự động

```javascript
// Generate financial report
fetch('/events/1/reports/generate', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        report_type: 'financial'
    })
})
```

## Dashboard Features

Dashboard đã được cập nhật để hiển thị thống kê từ tất cả 5 chức năng:

- **Events**: Tổng số sự kiện, trạng thái, upcoming events
- **Timeline**: Tasks completed, overdue, upcoming milestones  
- **Suppliers**: Verified, preferred, available suppliers
- **Checklist**: Tasks completion rate, overdue items
- **AI Suggestions**: Total suggestions, acceptance rate, confidence
- **Reports**: Published reports, drafts, analytics

## Technical Details

### Models & Relationships

- `Event` hasMany `Timeline`, `Checklist`, `AiSuggestion`, `EventReport`
- `Event` belongsToMany `Supplier` (many-to-many)
- Tất cả models đều có đầy đủ relationships và scopes

### Controllers

- Tất cả controllers đã implement đầy đủ CRUD operations
- JSON API support cho frontend integrations
- Proper validation và error handling
- Consistent response format

### Database

- Đầy đủ migrations cho tất cả tables
- Proper indexing và foreign keys
- Support cho soft deletes nếu cần

## Security & Performance

- Input validation cho tất cả endpoints
- Rate limiting cho AI API calls
- Proper error handling
- Database query optimization với eager loading

## Next Steps

Để hoàn thiện hệ thống:

1. **Frontend Views**: Tạo đầy đủ Blade templates cho UI
2. **Authentication**: Implement user authentication system  
3. **Permissions**: Role-based access control
4. **Notifications**: Email/SMS notifications for deadlines
5. **File Upload**: Support upload files cho attachments
6. **API Documentation**: Swagger/OpenAPI documentation
7. **Testing**: Unit tests và integration tests

Tất cả backend logic đã hoàn thành và sẵn sàng để tích hợp với frontend! 