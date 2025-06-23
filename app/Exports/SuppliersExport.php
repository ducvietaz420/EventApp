<?php

namespace App\Exports;

use App\Models\Supplier;
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

class SuppliersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $type;
    protected $status;
    protected $verified;

    public function __construct($type = null, $status = null, $verified = null)
    {
        $this->type = $type;
        $this->status = $status;
        $this->verified = $verified;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Supplier::with(['events', 'budgets']);

        if ($this->type) {
            $query->where('type', $this->type);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->verified !== null) {
            $query->where('is_verified', $this->verified);
        }

        return $query->orderBy('rating', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tên Nhà Cung Cấp',
            'Tên Công Ty',
            'Loại Dịch Vụ',
            'Người Liên Hệ',
            'Điện Thoại',
            'Email',
            'Địa Chỉ',
            'Website',
            'Ngân Sách Tối Thiểu (VND)',
            'Ngân Sách Tối Đa (VND)',
            'Đánh Giá',
            'Tổng Đánh Giá',
            'Số Sự Kiện Đã Tham Gia',
            'Tổng Doanh Thu (VND)',
            'Chuyên Môn',
            'Khu Vực Phục Vụ',
            'Trạng Thái',
            'Đã Xác Minh',
            'Nhà Cung Cấp Ưu Tiên',
            'Lần Cuối Làm Việc',
            'Ghi Chú'
        ];
    }

    public function map($supplier): array
    {
        return [
            $supplier->id,
            $supplier->name,
            $supplier->company_name,
            $this->getTypeDisplay($supplier->type),
            $supplier->contact_person,
            $supplier->phone,
            $supplier->email,
            $supplier->address,
            $supplier->website,
            $supplier->min_budget ? number_format($supplier->min_budget, 0, ',', '.') : '',
            $supplier->max_budget ? number_format($supplier->max_budget, 0, ',', '.') : '',
            $supplier->rating ? number_format($supplier->rating, 2) : '0',
            $supplier->total_reviews ?? 0,
            $supplier->total_events,
            number_format($supplier->total_revenue, 0, ',', '.'),
            is_array($supplier->specialties) ? implode(', ', $supplier->specialties) : $supplier->specialties,
            is_array($supplier->service_areas) ? implode(', ', $supplier->service_areas) : $supplier->service_areas,
            $this->getStatusDisplay($supplier->status),
            $supplier->is_verified ? 'Có' : 'Không',
            $supplier->is_preferred ? 'Có' : 'Không',
            $supplier->last_worked_date ? $supplier->last_worked_date->format('d/m/Y') : '',
            $supplier->notes
        ];
    }

    private function getTypeDisplay($type)
    {
        return match($type) {
            'venue' => 'Địa điểm',
            'catering' => 'Catering',
            'decoration' => 'Trang trí',
            'equipment' => 'Thiết bị',
            'photography' => 'Chụp ảnh',
            'videography' => 'Quay phim',
            'music' => 'Âm nhạc',
            'security' => 'Bảo vệ',
            'transportation' => 'Vận chuyển',
            'other' => 'Khác',
            default => ucfirst($type)
        };
    }

    private function getStatusDisplay($status)
    {
        return match($status) {
            'active' => 'Đang hoạt động',
            'inactive' => 'Không hoạt động',
            'blacklisted' => 'Danh sách đen',
            'pending' => 'Chờ xét duyệt',
            default => ucfirst($status)
        };
    }

    public function styles(Worksheet $sheet)
    {
        // Thiết lập style cho header
        $sheet->getStyle('A1:V1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '6C5CE7']
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
        $sheet->getStyle('A2:V' . $highestRow)->applyFromArray([
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
        $sheet->getStyle('J2:N' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('S2:U' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('V2:V' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Highlight nhà cung cấp được xác minh
        for ($row = 2; $row <= $highestRow; $row++) {
            $verifiedValue = $sheet->getCell('S' . $row)->getValue();
            if ($verifiedValue === 'Có') {
                $sheet->getStyle('S' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D4EDDA']
                    ]
                ]);
            }
        }

        // Highlight nhà cung cấp ưu tiên
        for ($row = 2; $row <= $highestRow; $row++) {
            $preferredValue = $sheet->getCell('T' . $row)->getValue();
            if ($preferredValue === 'Có') {
                $sheet->getStyle('T' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFE5B4']
                    ]
                ]);
            }
        }

        // Highlight đánh giá cao
        for ($row = 2; $row <= $highestRow; $row++) {
            $ratingValue = $sheet->getCell('L' . $row)->getValue();
            if (is_numeric($ratingValue) && floatval($ratingValue) >= 4.0) {
                $sheet->getStyle('L' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D4EDDA']
                    ]
                ]);
            }
        }

        // Wrap text cho các cột text dài  
        $sheet->getStyle('P2:Q' . $highestRow)->getAlignment()->setWrapText(true);
        $sheet->getStyle('V2:V' . $highestRow)->getAlignment()->setWrapText(true);

        // Thiết lập height cho header
        $sheet->getRowDimension(1)->setRowHeight(50);

        return $sheet;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // ID
            'B' => 20,  // Tên NCC
            'C' => 20,  // Tên công ty
            'D' => 15,  // Loại dịch vụ
            'E' => 15,  // Người liên hệ
            'F' => 12,  // Điện thoại
            'G' => 20,  // Email
            'H' => 25,  // Địa chỉ
            'I' => 20,  // Website
            'J' => 15,  // Ngân sách min
            'K' => 15,  // Ngân sách max
            'L' => 8,   // Đánh giá
            'M' => 10,  // Tổng đánh giá
            'N' => 12,  // Số sự kiện
            'O' => 15,  // Tổng doanh thu
            'P' => 25,  // Chuyên môn
            'Q' => 20,  // Khu vực phục vụ
            'R' => 12,  // Trạng thái
            'S' => 10,  // Đã xác minh
            'T' => 10,  // Ưu tiên
            'U' => 12,  // Lần cuối làm việc
            'V' => 25   // Ghi chú
        ];
    }

    public function title(): string
    {
        return 'Danh Sách Nhà Cung Cấp';
    }
} 