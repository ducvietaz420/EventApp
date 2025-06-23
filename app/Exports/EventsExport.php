<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EventsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct($startDate = null, $endDate = null, $status = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Event::with(['budgets', 'timelines', 'checklists', 'suppliers']);

        if ($this->startDate) {
            $query->whereDate('event_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('event_date', '<=', $this->endDate);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderBy('event_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tên Sự Kiện',
            'Loại Sự Kiện',
            'Trạng Thái',
            'Ngày Diễn Ra',
            'Thời Gian',
            'Địa Điểm',
            'Khách Hàng',
            'Số Khách Dự Kiến',
            'Ngân Sách (VND)',
            'Chi Phí Thực Tế (VND)',
            'Tỷ Lệ Sử Dụng (%)',
            'Tiến Độ (%)',
            'Số Nhà Cung Cấp',
            'Số Công Việc',
            'Ghi Chú'
        ];
    }

    public function map($event): array
    {
        return [
            $event->id,
            $event->name,
            $event->type_display,
            $event->status_display,
            $event->event_date ? $event->event_date->format('d/m/Y') : '',
            ($event->start_time && $event->end_time) ? 
                $event->start_time->format('H:i') . ' - ' . $event->end_time->format('H:i') : '',
            $event->venue,
            $event->client_name,
            $event->expected_guests,
            number_format($event->budget, 0, ',', '.'),
            number_format($event->total_spent, 0, ',', '.'),
            number_format($event->budget_usage_percentage, 2) . '%',
            number_format($event->progress_percentage, 2) . '%',
            $event->suppliers->count(),
            $event->checklists->count(),
            $event->notes
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Thiết lập style cho header
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Style cho tất cả dữ liệu
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:P' . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Căn giữa các cột số
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I2:M' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Wrap text cho ghi chú
        $sheet->getStyle('P2:P' . $highestRow)->getAlignment()->setWrapText(true);

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // ID
            'B' => 25,  // Tên sự kiện
            'C' => 15,  // Loại
            'D' => 12,  // Trạng thái
            'E' => 12,  // Ngày
            'F' => 15,  // Thời gian
            'G' => 20,  // Địa điểm
            'H' => 15,  // Khách hàng
            'I' => 12,  // Số khách
            'J' => 15,  // Ngân sách
            'K' => 15,  // Chi phí thực tế
            'L' => 12,  // Tỷ lệ sử dụng
            'M' => 10,  // Tiến độ
            'N' => 12,  // Số NCC
            'O' => 12,  // Số công việc
            'P' => 30   // Ghi chú
        ];
    }

    public function title(): string
    {
        return 'Danh Sách Sự Kiện';
    }
} 