<?php

namespace App\Exports;

use App\Models\Timeline;
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

class TimelinesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $eventId;
    protected $status;
    protected $priority;

    public function __construct($eventId = null, $status = null, $priority = null)
    {
        $this->eventId = $eventId;
        $this->status = $status;
        $this->priority = $priority;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Timeline::with(['event']);

        if ($this->eventId) {
            $query->where('event_id', $this->eventId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->priority) {
            $query->where('priority', $this->priority);
        }

        return $query->orderBy('start_time')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Sự Kiện',
            'Tiêu Đề',
            'Mô Tả',
            'Thời Gian Bắt Đầu',
            'Thời Gian Kết Thúc',
            'Địa Điểm',
            'Người Phụ Trách',
            'Trạng Thái',
            'Độ Ưu Tiên',
            'Là Mốc Quan Trọng',
            'Thời Gian Ước Tính (phút)',
            'Thời Gian Thực Tế (phút)',
            'Chênh Lệch Thời Gian',
            'Hoàn Thành Lúc',
            'Người Hoàn Thành',
            'Ghi Chú Hoàn Thành',
            'Ghi Chú'
        ];
    }

    public function map($timeline): array
    {
        return [
            $timeline->id,
            $timeline->event ? $timeline->event->name : '',
            $timeline->title,
            $timeline->description,
            $timeline->start_time ? $timeline->start_time->format('d/m/Y H:i') : '',
            $timeline->end_time ? $timeline->end_time->format('d/m/Y H:i') : '',
            $timeline->location,
            $timeline->responsible_person,
            $this->getStatusDisplay($timeline->status),
            $this->getPriorityDisplay($timeline->priority),
            $timeline->is_milestone ? 'Có' : 'Không',
            $timeline->estimated_duration,
            $timeline->actual_duration,
            $timeline->duration_variance ? $this->getDurationVarianceDisplay($timeline->duration_variance) : '',
            $timeline->completed_at ? $timeline->completed_at->format('d/m/Y H:i') : '',
            $timeline->completed_by,
            $timeline->completion_notes,
            $timeline->notes
        ];
    }

    private function getStatusDisplay($status)
    {
        return match($status) {
            'pending' => 'Chờ thực hiện',
            'in_progress' => 'Đang thực hiện',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy',
            'delayed' => 'Bị trễ',
            default => ucfirst($status)
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

    private function getDurationVarianceDisplay($variance)
    {
        if ($variance > 0) {
            return '+' . $variance . ' phút (Vượt)';
        } elseif ($variance < 0) {
            return $variance . ' phút (Tiết kiệm)';
        }
        return '0 phút (Đúng kế hoạch)';
    }

    public function styles(Worksheet $sheet)
    {
        // Thiết lập style cho header
        $sheet->getStyle('A1:R1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FF6B35']
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
        $sheet->getStyle('A2:R' . $highestRow)->applyFromArray([
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
        $sheet->getStyle('I2:K' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('L2:N' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Highlight các milestone
        for ($row = 2; $row <= $highestRow; $row++) {
            $milestoneValue = $sheet->getCell('K' . $row)->getValue();
            if ($milestoneValue === 'Có') {
                $sheet->getStyle('A' . $row . ':R' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF3CD']
                    ]
                ]);
            }
        }

        // Highlight trạng thái
        for ($row = 2; $row <= $highestRow; $row++) {
            $statusValue = $sheet->getCell('I' . $row)->getValue();
            $color = match($statusValue) {
                'Đã hoàn thành' => 'D4EDDA',
                'Đang thực hiện' => 'CCE5FF',
                'Bị trễ' => 'F8D7DA',
                'Đã hủy' => 'F5F5F5',
                default => null
            };
            
            if ($color) {
                $sheet->getStyle('I' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $color]
                    ]
                ]);
            }
        }

        // Wrap text cho các cột text dài
        $sheet->getStyle('C2:D' . $highestRow)->getAlignment()->setWrapText(true);
        $sheet->getStyle('Q2:R' . $highestRow)->getAlignment()->setWrapText(true);

        // Thiết lập height cho header
        $sheet->getRowDimension(1)->setRowHeight(40);

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // ID
            'B' => 18,  // Sự kiện
            'C' => 20,  // Tiêu đề
            'D' => 25,  // Mô tả
            'E' => 15,  // Thời gian bắt đầu
            'F' => 15,  // Thời gian kết thúc
            'G' => 15,  // Địa điểm
            'H' => 15,  // Người phụ trách
            'I' => 12,  // Trạng thái
            'J' => 10,  // Độ ưu tiên
            'K' => 10,  // Là milestone
            'L' => 12,  // Thời gian ước tính
            'M' => 12,  // Thời gian thực tế
            'N' => 15,  // Chênh lệch
            'O' => 15,  // Hoàn thành lúc
            'P' => 12,  // Người hoàn thành
            'Q' => 20,  // Ghi chú hoàn thành
            'R' => 20   // Ghi chú
        ];
    }

    public function title(): string
    {
        return 'Báo Cáo Timeline';
    }
} 