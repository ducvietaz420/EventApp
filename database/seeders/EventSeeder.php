<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Budget;
use App\Models\Timeline;
use App\Models\Supplier;
use App\Models\Checklist;
use App\Models\AiSuggestion;
use App\Models\EventReport;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo các nhà cung cấp mẫu
        $suppliers = [
            [
                'name' => 'Công ty Trang trí ABC',
                'company_name' => 'Công ty TNHH Trang trí ABC',
                'type' => 'decoration',
                'description' => 'Chuyên cung cấp dịch vụ trang trí sự kiện cao cấp',
                'contact_person' => 'Nguyễn Văn A',
                'phone' => '0901234567',
                'email' => 'contact@abc-decoration.com',
                'address' => '123 Đường ABC, Quận 1, TP.HCM',
                'specialties' => 'Trang trí sân khấu, Backdrop, Lighting',
                'rating' => 4.5,
                'total_reviews' => 25,
                'min_budget' => 5000000,
                'max_budget' => 50000000,
                'is_preferred' => true,
                'status' => 'active'
            ],
            [
                'name' => 'Nhà hàng Tiệc Cưới XYZ',
                'company_name' => 'Công ty CP Nhà hàng XYZ',
                'type' => 'catering',
                'description' => 'Dịch vụ catering chuyên nghiệp cho mọi sự kiện',
                'contact_person' => 'Trần Thị B',
                'phone' => '0912345678',
                'email' => 'info@xyz-catering.com',
                'address' => '456 Đường XYZ, Quận 3, TP.HCM',
                'specialties' => 'Catering, Buffet, Set menu, Đồ uống',
                'rating' => 4.8,
                'total_reviews' => 40,
                'min_budget' => 200000,
                'max_budget' => 2000000,
                'is_preferred' => true,
                'status' => 'active'
            ],
            [
                'name' => 'Studio Chụp Ảnh DEF',
                'company_name' => 'Studio DEF',
                'type' => 'photography',
                'description' => 'Studio chụp ảnh chuyên nghiệp với đội ngũ nhiều kinh nghiệm',
                'contact_person' => 'Lê Văn C',
                'phone' => '0923456789',
                'email' => 'booking@def-studio.com',
                'address' => '789 Đường DEF, Quận 7, TP.HCM',
                'specialties' => 'Chụp ảnh sự kiện, Quay phim, Live streaming',
                'rating' => 4.3,
                'total_reviews' => 18,
                'min_budget' => 3000000,
                'max_budget' => 20000000,
                'is_preferred' => false,
                'status' => 'active'
            ]
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }

        // Tạo các sự kiện mẫu
        $events = [
            [
                'name' => 'Đám cưới Nguyễn Văn A & Trần Thị B',
                'description' => 'Lễ cưới truyền thống kết hợp hiện đại tại khách sạn 5 sao',
                'type' => 'wedding',
                'status' => 'confirmed',
                'event_date' => Carbon::now()->addDays(30)->toDateString(),
                'start_time' => '18:00:00',
                'end_time' => '22:00:00',
                'venue' => 'Khách sạn Grand Palace',
                'venue_address' => '123 Nguyễn Huệ, Quận 1, TP.HCM',
                'client_name' => 'Nguyễn Văn A',
                'client_phone' => '0901111111',
                'client_email' => 'nguyenvana@email.com',
                'expected_guests' => 200,
                'budget' => 150000000,
                'requirements' => json_encode(['Không sử dụng hoa sen', 'Ưu tiên màu đỏ và vàng']),
                'deadline_design' => Carbon::now()->addDays(15)->toDateString(),
                'deadline_booking' => Carbon::now()->addDays(20)->toDateString(),
                'deadline_final' => Carbon::now()->addDays(30)->toDateString()
            ],
            [
                'name' => 'Hội nghị Công nghệ 2024',
                'description' => 'Hội nghị quốc tế về công nghệ thông tin và chuyển đổi số',
                'type' => 'conference',
                'status' => 'planning',
                'event_date' => Carbon::now()->addDays(60)->toDateString(),
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'venue' => 'Trung tâm Hội nghị Quốc gia',
                'venue_address' => '456 Lê Duẩn, Quận 1, TP.HCM',
                'client_name' => 'Công ty TNHH Công nghệ ABC',
                'client_phone' => '0902222222',
                'client_email' => 'events@abc-tech.com',
                'expected_guests' => 500,
                'budget' => 300000000,
                'requirements' => json_encode(['Hệ thống âm thanh chuyên nghiệp', 'Live streaming', 'Wifi tốc độ cao']),
                'deadline_design' => Carbon::now()->addDays(45)->toDateString(),
                'deadline_booking' => Carbon::now()->addDays(50)->toDateString(),
                'deadline_final' => Carbon::now()->addDays(62)->toDateString()
            ],
            [
                'name' => 'Sinh nhật công ty 10 năm',
                'description' => 'Lễ kỷ niệm 10 năm thành lập công ty với chương trình gala dinner',
                'type' => 'corporate',
                'status' => 'in_progress',
                'event_date' => Carbon::now()->addDays(15)->toDateString(),
                'start_time' => '19:00:00',
                'end_time' => '23:00:00',
                'venue' => 'Nhà hàng Riverside',
                'venue_address' => '789 Nguyễn Văn Cừ, Quận 5, TP.HCM',
                'client_name' => 'Công ty XYZ',
                'client_phone' => '0903333333',
                'client_email' => 'hr@xyz-company.com',
                'expected_guests' => 150,
                'budget' => 80000000,
                'requirements' => json_encode(['Trang trí theo chủ đề vàng đồng', 'Chương trình văn nghệ', 'Không gian VIP cho ban lãnh đạo']),
                'deadline_design' => Carbon::now()->addDays(5)->toDateString(),
                'deadline_booking' => Carbon::now()->addDays(8)->toDateString(),
                'deadline_final' => Carbon::now()->addDays(15)->toDateString()
            ]
        ];

        foreach ($events as $eventData) {
            $event = Event::create($eventData);
            
            // Tạo budget cho từng sự kiện
            $budgetItems = [
                [
                    'category' => 'venue',
                    'item_name' => 'Thuê địa điểm',
                    'description' => 'Chi phí thuê địa điểm tổ chức sự kiện',
                    'estimated_cost' => $event->budget * 0.3,
                    'actual_cost' => $event->budget * 0.28,
                    'status' => 'paid',
                    'is_essential' => true,
                    'priority' => 1,
                    'due_date' => Carbon::now()->addDays(20)->toDateString(),
                    'paid_date' => Carbon::now()->subDays(5)->toDateString(),
                    'supplier_name' => 'Khách sạn Grand Palace',
                    'supplier_contact' => '0901234567'
                ],
                [
                    'category' => 'catering',
                    'item_name' => 'Dịch vụ ăn uống',
                    'description' => 'Dịch vụ catering cho sự kiện',
                    'estimated_cost' => $event->budget * 0.4,
                    'actual_cost' => $event->budget * 0.42,
                    'status' => 'booked',
                    'is_essential' => true,
                    'priority' => 2,
                    'due_date' => Carbon::now()->addDays(25)->toDateString(),
                    'supplier_name' => 'Nhà hàng ABC',
                    'supplier_contact' => '0902345678'
                ],
                [
                     'category' => 'decoration',
                     'item_name' => 'Trang trí',
                     'description' => 'Trang trí hoa và backdrop cho sự kiện',
                     'estimated_cost' => $event->budget * 0.2,
                     'actual_cost' => 0,
                     'status' => 'quoted',
                     'is_essential' => false,
                     'priority' => 3,
                     'due_date' => Carbon::now()->addDays(28)->toDateString(),
                     'supplier_name' => 'Cửa hàng hoa XYZ',
                     'supplier_contact' => '0903456789'
                 ],
                 [
                     'category' => 'entertainment',
                     'item_name' => 'Âm thanh ánh sáng',
                     'description' => 'Dịch vụ âm nhạc và giải trí',
                     'estimated_cost' => $event->budget * 0.1,
                     'actual_cost' => 0,
                     'status' => 'planned',
                     'is_essential' => false,
                     'priority' => 4,
                     'due_date' => Carbon::now()->addDays(30)->toDateString(),
                     'supplier_name' => 'Ban nhạc DEF',
                     'supplier_contact' => '0904567890'
                 ]
            ];

            foreach ($budgetItems as $budgetData) {
                $budgetData['event_id'] = $event->id;
                Budget::create($budgetData);
            }

            // Tạo timeline cho mỗi sự kiện
            $eventDate = Carbon::parse($event->event_date);
            $timelineItems = [
                [
                    'title' => 'Khảo sát địa điểm',
                    'description' => 'Đi khảo sát và chọn địa điểm tổ chức sự kiện',
                    'start_time' => $eventDate->copy()->subDays(30)->toDateTimeString(),
                    'end_time' => $eventDate->copy()->subDays(28)->toDateTimeString(),
                    'status' => 'completed',
                    'priority' => 'high',
                    'is_milestone' => true,
                    'responsible_person' => 'Nguyễn Văn A'
                ],
                [
                    'title' => 'Thiết kế backdrop',
                    'description' => 'Thiết kế và sản xuất backdrop chính cho sự kiện',
                    'start_time' => $eventDate->copy()->subDays(25)->toDateTimeString(),
                    'end_time' => $eventDate->copy()->subDays(20)->toDateTimeString(),
                    'status' => $event->status === 'planning' ? 'pending' : 'in_progress',
                    'priority' => 'medium',
                    'is_milestone' => false,
                    'responsible_person' => 'Trần Thị B'
                ],
                [
                    'title' => 'Setup âm thanh',
                    'description' => 'Lắp đặt và test hệ thống âm thanh',
                    'start_time' => $eventDate->copy()->subHours(4)->toDateTimeString(),
                    'end_time' => $eventDate->copy()->subHours(2)->toDateTimeString(),
                    'status' => 'pending',
                    'priority' => 'critical',
                    'is_milestone' => true,
                    'responsible_person' => 'Lê Văn C'
                ]
            ];

            foreach ($timelineItems as $timelineData) {
                $timelineData['event_id'] = $event->id;
                Timeline::create($timelineData);
            }

            // Tạo checklist cho mỗi sự kiện
            $checklistItems = [
                [
                    'title' => 'Xác nhận địa điểm',
                    'description' => 'Ký hợp đồng thuê địa điểm',
                    'category' => 'booking',
                    'priority' => 'high',
                    'status' => 'completed',
                    'due_date' => $eventDate->copy()->subDays(20)->toDateString(),
                    'completed_at' => $eventDate->copy()->subDays(22)->toDateTimeString()
                ],
                [
                    'title' => 'Đặt dịch vụ catering',
                    'description' => 'Chọn menu và đặt dịch vụ ăn uống',
                    'category' => 'booking',
                    'priority' => 'high',
                    'status' => $event->status === 'planning' ? 'pending' : 'in_progress',
                    'due_date' => $eventDate->copy()->subDays(15)->toDateString()
                ],
                [
                    'title' => 'Chuẩn bị danh sách khách mời',
                    'description' => 'Lập danh sách và gửi thiệp mời',
                    'category' => 'planning',
                    'priority' => 'medium',
                    'status' => 'pending',
                    'due_date' => $eventDate->copy()->subDays(10)->toDateString()
                ],
                [
                    'title' => 'Kiểm tra âm thanh',
                    'description' => 'Test hệ thống âm thanh ánh sáng',
                    'category' => 'preparation',
                    'priority' => 'high',
                    'status' => 'pending',
                    'due_date' => $eventDate->copy()->subDay()->toDateString()
                ]
            ];

            foreach ($checklistItems as $checklistData) {
                $checklistData['event_id'] = $event->id;
                Checklist::create($checklistData);
            }

            // Tạo AI suggestions cho mỗi sự kiện
            $aiSuggestions = [
                [
                    'suggestion_type' => 'decoration',
                    'title' => 'Tối ưu chi phí trang trí',
                    'content' => 'Đề xuất sử dụng hoa tươi địa phương thay vì hoa nhập khẩu để tiết kiệm 30% chi phí',
                    'confidence_score' => 85.00,
                    'status' => 'generated',
                    'ai_model' => 'gemini',
                    'estimated_cost' => $event->budget * 0.06,
                    'tags' => ['cost-saving', 'decoration', 'local-sourcing']
                ],
                [
                    'suggestion_type' => 'entertainment',
                    'title' => 'Gợi ý nhà cung cấp âm thanh',
                    'content' => 'Dựa trên quy mô sự kiện, đề xuất sử dụng hệ thống âm thanh Line Array cho chất lượng tốt nhất',
                    'confidence_score' => 92.00,
                    'status' => 'accepted',
                    'ai_model' => 'gemini',
                    'estimated_cost' => $event->budget * 0.08,
                    'tags' => ['audio', 'equipment', 'quality']
                ],
                [
                    'suggestion_type' => 'timeline',
                    'title' => 'Tối ưu lịch trình setup',
                    'content' => 'Đề xuất bắt đầu setup sớm hơn 2 giờ để tránh rush và đảm bảo chất lượng',
                    'confidence_score' => 78.00,
                    'status' => 'reviewed',
                    'ai_model' => 'gemini',
                    'tags' => ['timeline', 'setup', 'risk-management']
                ]
            ];

            foreach ($aiSuggestions as $suggestionData) {
                $suggestionData['event_id'] = $event->id;
                AiSuggestion::create($suggestionData);
            }

            // Gắn suppliers vào event
            $event->suppliers()->attach(1, [
                'role' => 'Trang trí chính',
                'contract_value' => $event->budget * 0.2,
                'status' => 'confirmed'
            ]);
            
            $event->suppliers()->attach(2, [
                'role' => 'Dịch vụ catering',
                'contract_value' => $event->budget * 0.4,
                'status' => 'confirmed'
            ]);
        }

        // Tạo báo cáo cho sự kiện đã hoàn thành (nếu có)
        $completedEvent = Event::where('status', 'completed')->first();
        if ($completedEvent) {
            EventReport::create([
                'event_id' => $completedEvent->id,
                'report_type' => 'post_event',
                'title' => 'Báo cáo tổng kết sự kiện ' . $completedEvent->name,
                'summary' => 'Sự kiện diễn ra thành công với sự tham gia của ' . $completedEvent->actual_attendees . ' khách mời',
                'content' => 'Báo cáo chi tiết về quá trình tổ chức và kết quả của sự kiện',
                'status' => 'published',
                'visibility' => 'public',
                'rating' => 4.5,
                'success_score' => 88.5,
                'roi_percentage' => 15.2,
                'cost_variance' => 5.5,
                'timeline_variance' => -2,
                'stakeholder_satisfaction' => 92.0,
                'published_at' => now(),
                'tags' => ['successful', 'on-budget', 'high-satisfaction']
            ]);
        }
    }
}
