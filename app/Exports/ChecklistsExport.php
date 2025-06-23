<?php

namespace App\Exports;

use App\Models\Checklist;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ChecklistsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $eventId;
    protected $status;
    protected $category;
    protected $priority;

    public function __construct($eventId = null, $status = null, $category = null, $priority = null)
    {
        $this->eventId = $eventId;
        $this->status = $status;
        $this->category = $category;
        $this->priority = $priority;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Checklist::with(['event']);

        if ($this->eventId) {
            $query->where('event_id', $this->eventId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        if ($this->priority) {
            $query->where('priority', $this->priority);
        }

        return $query->orderBy('priority', 'desc')
                    ->orderBy('due_date', 'asc')
                    ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Sự Kiện',
            'Tiêu Đề',
            'Mô Tả',
            'Danh Mục',
            'Độ Ưu Tiên',
            'Trạng Thái',
            'Ngày Hết Hạn',
            'Ngày Nhắc Nhở',
            'Người Được Giao',
            'Chi Phí Ước Tính (VND)',
            'Chi Phí Thực Tế (VND)',
            'Chênh Lệch Chi Phí (VND)',
            'Số Ngày Còn Lại',
            'Trễ Hạn',
            'Cần Nhắc Nhở',
            'Cần Phê Duyệt',
            'Trạng Thái Phê Duyệt',
            'Người Phê Duyệt',
            'Ngày Phê Duyệt',
            'Hoàn Thành Lúc',
            'Người Hoàn Thành',
            'Ghi Chú'
        ];
    }

    public function map($checklist): array
    {
        return [
            $checklist->id,
            $checklist->event ? $checklist->event->name : '',
            $checklist->title,
            $checklist->description,
            $this->getCategoryDisplay($checklist->category),
            $this->getPriorityDisplay($checklist->priority),
            $this->getStatusDisplay($checklist->status),
            $checklist->due_date ? $checklist->due_date->format('d/m/Y') : '',
            $checklist->reminder_date ? $checklist->reminder_date->format('d/m/Y') : '',
            $checklist->assigned_to,
            $checklist->estimated_cost ? number_format($checklist->estimated_cost, 0, ',', '.') : '',
            $checklist->actual_cost ? number_format($checklist->actual_cost, 0, ',', '.') : '',
            number_format($checklist->cost_variance, 0, ',', '.'),
            $checklist->days_until_due !== null ? $checklist->days_until_due : '',
            $checklist->is_overdue ? 'Có' : 'Không',
            $checklist->needs_reminder ? 'Có' : 'Không',
            $checklist->requires_approval ? 'Có' : 'Không',
            $this->getApprovalStatusDisplay($checklist->approval_status),
            $checklist->approved_by,
            $checklist->approved_at ? $checklist->approved_at->format('d/m/Y H:i') : '',
            $checklist->completed_at ? $checklist->completed_at->format('d/m/Y H:i') : '',
            $checklist->completed_by,
            $checklist->notes
        ];
    }

    private function getCategoryDisplay($category)
    {
        return match($category) {
            'venue' => 'Địa điểm',
            'catering' => 'Catering',
            'decoration' => 'Trang trí',
            'equipment' => 'Thiết bị',
            'marketing' => 'Marketing',
            'logistics' => 'Logistics',
            'documentation' => 'Tài liệu',
            'staff' => 'Nhân sự',
            'other' => 'Khác',
            default => ucfirst($category)
        };
    }

    private function getPriorityDisplay($priority)
    {
        return match($priority) {
            'low' => 'Thấp',
            'medium' => 'Trung bình',
            'high' => 'Cao',
            'urgent' => 'Khẩn cấp',
            default => ucfirst($priority)
        };
    }

    private function getStatusDisplay($status)
    {
        return match($status) {
            'pending' => 'Chờ thực hiện',
            'in_progress' => 'Đang thực hiện',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy',
            'on_hold' => 'Tạm dừng',
            default => ucfirst($status)
        };
    }

    private function getApprovalStatusDisplay($status)
    {
        return match($status) {
            'pending' => 'Chờ phê duyệt',
            'approved' => 'Đã phê duyệt',
            'rejected' => 'Từ chối',
            default => $status ? ucfirst($status) : 'Không cần'
        };
    }

    public function styles(Worksheet $sheet)
    {
        // Thiết lập style cho header
        $sheet->getStyle('A1:W1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E74C3C']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
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
        $sheet->getStyle('A2:W' . $highestRow)->applyFromArray([
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

        // Căn giữa các cột cụ thể
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K2:N' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('O2:R' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Highlight theo độ ưu tiên
        for ($row = 2; $row <= $highestRow; $row++) {
            $priorityValue = $sheet->getCell('F' . $row)->getValue();
            $color = match($priorityValue) {
                'Khẩn cấp' => 'FFCDD2',
                'Cao' => 'FFE0B2',
                'Trung bình' => 'FFF9C4',
                'Thấp' => 'E8F5E8',
                default => null
            };
            
            if ($color) {
                $sheet->getStyle('F' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $color]
                    ]
                ]);
            }
        }

        // Highlight trạng thái
        for ($row = 2; $row <= $highestRow; $row++) {
            $statusValue = $sheet->getCell('G' . $row)->getValue();
            $color = match($statusValue) {
                'Đã hoàn thành' => 'D4EDDA',
                'Đang thực hiện' => 'CCE5FF',
                'Tạm dừng' => 'FFF3CD',
                'Đã hủy' => 'F8D7DA',
                default => null
            };
            
            if ($color) {
                $sheet->getStyle('G' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $color]
                    ]
                ]);
            }
        }

        // Highlight các task trễ hạn
        for ($row = 2; $row <= $highestRow; $row++) {
            $overdueValue = $sheet->getCell('O' . $row)->getValue();
            if ($overdueValue === 'Có') {
                $sheet->getStyle('O' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8D7DA']
                    ]
                ]);
            }
        }

        // Highlight các task cần nhắc nhở
        for ($row = 2; $row <= $highestRow; $row++) {
            $reminderValue = $sheet->getCell('P' . $row)->getValue();
            if ($reminderValue === 'Có') {
                $sheet->getStyle('P' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF3CD']
                    ]
                ]);
            }
        }

        // Wrap text cho các cột text dài
        $sheet->getStyle('C2:D' . $highestRow)->getAlignment()->setWrapText(true);
        $sheet->getStyle('W2:W' . $highestRow)->getAlignment()->setWrapText(true);

        // Thiết lập height cho header
        $sheet->getRowDimension(1)->setRowHeight(50);

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // ID
            'B' => 20,  // Sự kiện
            'C' => 25,  // Tiêu đề
            'D' => 30,  // Mô tả
            'E' => 12,  // Danh mục
            'F' => 10,  // Độ ưu tiên
            'G' => 12,  // Trạng thái
            'H' => 12,  // Ngày hết hạn
            'I' => 12,  // Ngày nhắc nhở
            'J' => 15,  // Người được giao
            'K' => 15,  // Chi phí ước tính
            'L' => 15,  // Chi phí thực tế
            'M' => 15,  // Chênh lệch
            'N' => 10,  // Ngày còn lại
            'O' => 8,   // Trễ hạn
            'P' => 10,  // Cần nhắc nhở
            'Q' => 10,  // Cần phê duyệt
            'R' => 12,  // Trạng thái phê duyệt
            'S' => 12,  // Người phê duyệt
            'T' => 15,  // Ngày phê duyệt
            'U' => 15,  // Hoàn thành lúc
            'V' => 12,  // Người hoàn thành
            'W' => 25   // Ghi chú
        ];
    }

    public function title(): string
    {
        return 'Danh Sách Checklist';
    }
} 