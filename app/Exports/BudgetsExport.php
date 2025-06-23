<?php

namespace App\Exports;

use App\Models\Budget;
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

class BudgetsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $eventId;
    protected $category;
    protected $paymentStatus;

    public function __construct($eventId = null, $category = null, $paymentStatus = null)
    {
        $this->eventId = $eventId;
        $this->category = $category;
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Budget::with(['event', 'supplier']);

        if ($this->eventId) {
            $query->where('event_id', $this->eventId);
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        if ($this->paymentStatus) {
            $query->where('payment_status', $this->paymentStatus);
        }

        return $query->orderBy('event_id')->orderBy('category')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Sự Kiện',
            'Danh Mục',
            'Tên Khoản Mục',
            'Mô Tả',
            'Chi Phí Ước Tính (VND)',
            'Chi Phí Thực Tế (VND)',
            'Chênh Lệch (VND)',
            'Tỷ Lệ Chênh Lệch (%)',
            'Nhà Cung Cấp',
            'Trạng Thái Thanh Toán',
            'Ngày Thanh Toán',
            'Phương Thức Thanh Toán',
            'Số Hóa Đơn',
            'Deadline',
            'Độ Ưu Tiên',
            'Được Phê Duyệt',
            'Ghi Chú'
        ];
    }

    public function map($budget): array
    {
        return [
            $budget->id,
            $budget->event ? $budget->event->name : '',
            $budget->category_display,
            $budget->item_name,
            $budget->description,
            number_format($budget->estimated_cost, 0, ',', '.'),
            number_format($budget->actual_cost ?? 0, 0, ',', '.'),
            number_format($budget->variance, 0, ',', '.'),
            $budget->variance_percentage ? number_format($budget->variance_percentage, 2) . '%' : '0%',
            $budget->supplier ? $budget->supplier->name : '',
            $this->getPaymentStatusDisplay($budget->payment_status),
            $budget->payment_date ? $budget->payment_date->format('d/m/Y') : '',
            $this->getPaymentMethodDisplay($budget->payment_method),
            $budget->invoice_number,
            $budget->deadline ? $budget->deadline->format('d/m/Y') : '',
            $this->getPriorityDisplay($budget->priority),
            $budget->is_approved ? 'Có' : 'Không',
            $budget->notes
        ];
    }

    private function getPaymentStatusDisplay($status)
    {
        return match($status) {
            'pending' => 'Chờ thanh toán',
            'partial' => 'Thanh toán một phần',
            'paid' => 'Đã thanh toán',
            'overdue' => 'Quá hạn',
            default => ucfirst($status)
        };
    }

    private function getPaymentMethodDisplay($method)
    {
        return match($method) {
            'cash' => 'Tiền mặt',
            'bank_transfer' => 'Chuyển khoản',
            'credit_card' => 'Thẻ tín dụng',
            'check' => 'Séc',
            default => ucfirst($method)
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
                'startColor' => ['rgb' => '28A745']
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

        // Căn giữa các cột số và ngày
        $sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:I' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('L2:L' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('O2:O' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('Q2:Q' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Highlight các khoản vượt ngân sách
        for ($row = 2; $row <= $highestRow; $row++) {
            $varianceValue = $sheet->getCell('H' . $row)->getValue();
            if (is_numeric(str_replace(['.', ','], '', $varianceValue)) && 
                floatval(str_replace(['.', ','], '', $varianceValue)) > 0) {
                $sheet->getStyle('H' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFE6E6']
                    ]
                ]);
            }
        }

        // Wrap text cho các cột mô tả và ghi chú
        $sheet->getStyle('E2:E' . $highestRow)->getAlignment()->setWrapText(true);
        $sheet->getStyle('R2:R' . $highestRow)->getAlignment()->setWrapText(true);

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // ID
            'B' => 20,  // Sự kiện
            'C' => 12,  // Danh mục
            'D' => 20,  // Tên khoản mục
            'E' => 25,  // Mô tả
            'F' => 15,  // Chi phí ước tính
            'G' => 15,  // Chi phí thực tế
            'H' => 15,  // Chênh lệch
            'I' => 12,  // Tỷ lệ chênh lệch
            'J' => 15,  // Nhà cung cấp
            'K' => 15,  // Trạng thái thanh toán
            'L' => 12,  // Ngày thanh toán
            'M' => 15,  // Phương thức thanh toán
            'N' => 12,  // Số hóa đơn
            'O' => 12,  // Deadline
            'P' => 10,  // Độ ưu tiên
            'Q' => 10,  // Được phê duyệt
            'R' => 25   // Ghi chú
        ];
    }

    public function title(): string
    {
        return 'Báo Cáo Ngân Sách';
    }
} 