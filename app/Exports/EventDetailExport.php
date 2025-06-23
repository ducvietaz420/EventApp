<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class EventDetailExport implements WithMultipleSheets
{
    use Exportable;

    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        // Sheet 1: Thông tin tổng quan sự kiện
        $sheets[] = new EventOverviewSheet($this->event);

        // Sheet 2: Ngân sách
        if ($this->event->budgets()->count() > 0) {
            $sheets[] = new EventBudgetSheet($this->event);
        }

        // Sheet 3: Timeline
        if ($this->event->timelines()->count() > 0) {
            $sheets[] = new EventTimelineSheet($this->event);
        }

        // Sheet 4: Checklist
        if ($this->event->checklists()->count() > 0) {
            $sheets[] = new EventChecklistSheet($this->event);
        }

        // Sheet 5: Nhà cung cấp
        if ($this->event->suppliers()->count() > 0) {
            $sheets[] = new EventSupplierSheet($this->event);
        }

        return $sheets;
    }
}

// Sheet thông tin tổng quan sự kiện
class EventOverviewSheet implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithTitle, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithColumnWidths
{
    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function array(): array
    {
        return [
            ['THÔNG TIN TỔNG QUAN SỰ KIỆN'],
            [''],
            ['Tên sự kiện:', $this->event->name],
            ['Loại sự kiện:', $this->event->type_display],
            ['Trạng thái:', $this->event->status_display],
            ['Ngày diễn ra:', $this->event->event_date ? $this->event->event_date->format('d/m/Y') : ''],
            ['Thời gian:', ($this->event->start_time && $this->event->end_time) ? 
                $this->event->start_time->format('H:i') . ' - ' . $this->event->end_time->format('H:i') : ''],
            ['Địa điểm:', $this->event->venue],
            ['Địa chỉ:', $this->event->venue_address],
            [''],
            ['THÔNG TIN KHÁCH HÀNG'],
            [''],
            ['Tên khách hàng:', $this->event->client_name],
            ['Số điện thoại:', $this->event->client_phone],
            ['Email:', $this->event->client_email],
            ['Số khách dự kiến:', $this->event->expected_guests],
            [''],
            ['THÔNG TIN TÀI CHÍNH'],
            [''],
            ['Ngân sách:', number_format($this->event->budget, 0, ',', '.') . ' VND'],
            ['Chi phí thực tế:', number_format($this->event->total_spent, 0, ',', '.') . ' VND'],
            ['Chênh lệch:', number_format($this->event->total_spent - $this->event->budget, 0, ',', '.') . ' VND'],
            ['Tỷ lệ sử dụng:', number_format($this->event->budget_usage_percentage, 2) . '%'],
            [''],
            ['TIẾN ĐỘ DỰ ÁN'],
            [''],
            ['Tiến độ hoàn thành:', number_format($this->event->progress_percentage, 2) . '%'],
            ['Số nhà cung cấp:', $this->event->suppliers->count()],
            ['Số công việc:', $this->event->checklists->count()],
            ['Số công việc hoàn thành:', $this->event->checklists->where('status', 'completed')->count()],
            [''],
            ['DEADLINE'],
            [''],
            ['Deadline thiết kế:', $this->event->deadline_design ? $this->event->deadline_design->format('d/m/Y') : ''],
            ['Deadline booking:', $this->event->deadline_booking ? $this->event->deadline_booking->format('d/m/Y') : ''],
            ['Deadline cuối cùng:', $this->event->deadline_final ? $this->event->deadline_final->format('d/m/Y') : ''],
            [''],
            ['YÊU CẦU ĐẶC BIỆT'],
            [''],
            ['Yêu cầu:', is_array($this->event->requirements) ? implode(', ', $this->event->requirements) : $this->event->requirements],
            [''],
            ['GHI CHÚ'],
            [''],
            ['Ghi chú:', $this->event->notes],
        ];
    }

    public function title(): string
    {
        return 'Tổng quan';
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        return [
            1 => ['font' => ['size' => 16, 'bold' => true]],
            11 => ['font' => ['size' => 14, 'bold' => true]],
            18 => ['font' => ['size' => 14, 'bold' => true]],
            25 => ['font' => ['size' => 14, 'bold' => true]],
            32 => ['font' => ['size' => 14, 'bold' => true]],
            37 => ['font' => ['size' => 14, 'bold' => true]],
            41 => ['font' => ['size' => 14, 'bold' => true]],
            'A:A' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 40,
        ];
    }
}

// Sheet ngân sách
class EventBudgetSheet extends BudgetsExport
{
    public function __construct(Event $event)
    {
        parent::__construct($event->id);
    }

    public function title(): string
    {
        return 'Ngân sách';
    }
}

// Sheet timeline
class EventTimelineSheet extends TimelinesExport
{
    public function __construct(Event $event)
    {
        parent::__construct($event->id);
    }

    public function title(): string
    {
        return 'Timeline';
    }
}

// Sheet checklist
class EventChecklistSheet extends ChecklistsExport
{
    public function __construct(Event $event)
    {
        parent::__construct($event->id);
    }

    public function title(): string
    {
        return 'Checklist';
    }
}

// Sheet nhà cung cấp
class EventSupplierSheet implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithColumnWidths, \Maatwebsite\Excel\Concerns\WithTitle
{
    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function collection()
    {
        return $this->event->suppliers;
    }

    public function headings(): array
    {
        return [
            'Tên Nhà Cung Cấp',
            'Loại Dịch Vụ',
            'Vai Trò',
            'Giá Trị Hợp Đồng (VND)',
            'Trạng Thái',
            'Người Liên Hệ',
            'Điện Thoại',
            'Email',
            'Đánh Giá',
            'Ghi Chú'
        ];
    }

    public function map($supplier): array
    {
        return [
            $supplier->name,
            $supplier->type_display ?? $supplier->type,
            $supplier->pivot->role ?? '',
            $supplier->pivot->contract_value ? number_format($supplier->pivot->contract_value, 0, ',', '.') : '',
            $supplier->pivot->status ?? '',
            $supplier->contact_person,
            $supplier->phone,
            $supplier->email,
            $supplier->rating ? number_format($supplier->rating, 2) : '0',
            $supplier->pivot->notes ?? $supplier->notes
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '6C5CE7']
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 15,
            'C' => 15,
            'D' => 15,
            'E' => 12,
            'F' => 15,
            'G' => 12,
            'H' => 20,
            'I' => 8,
            'J' => 25
        ];
    }

    public function title(): string
    {
        return 'Nhà cung cấp';
    }
} 