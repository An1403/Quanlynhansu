<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SalariesExport
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function generate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set tên sheet
        $sheetTitle = 'Luong_' . str_pad($this->month, 2, '0', STR_PAD_LEFT) . '_' . $this->year;
        $sheet->setTitle($sheetTitle);
        
        // Header - CẢ phòng ban VÀ chức vụ
        $headers = [
            'A' => 'Mã NV',
            'B' => 'Họ và tên',
            'C' => 'Phòng ban',
            'D' => 'Chức vụ',
            'E' => 'Tháng',
            'F' => 'Năm',
            'G' => 'Lương cơ bản',
            'H' => 'Phụ cấp',
            'I' => 'Thưởng',
            'J' => 'Khấu trừ',
            'K' => 'Lương thực nhận',
        ];
        
        // Set header values
        foreach ($headers as $col => $value) {
            $sheet->setCellValue($col . '1', $value);
        }
        
        // Style header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ];
        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);
        
        // Set độ cao header
        $sheet->getRowDimension(1)->setRowHeight(25);
        
        // ✅ Lấy dữ liệu - JOIN đúng với departments và positions
        $salaries = DB::table('salaries')
            ->join('employees', 'salaries.employee_id', '=', 'employees.id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
            ->where('salaries.month', $this->month)
            ->where('salaries.year', $this->year)
            ->select(
                'employees.employee_code',
                'employees.full_name',
                'departments.name as department_name',
                'positions.name as position_name',
                'salaries.month',
                'salaries.year',
                'salaries.base_salary',
                'salaries.allowance',
                'salaries.bonus',
                'salaries.deduction',
                'salaries.total_salary'
            )
            ->orderBy('employees.employee_code')
            ->get();
        
        // Điền dữ liệu
        $row = 2;
        foreach ($salaries as $salary) {
            $sheet->setCellValue('A' . $row, $salary->employee_code);
            $sheet->setCellValue('B' . $row, $salary->full_name);
            $sheet->setCellValue('C' . $row, $salary->department_name ?? '-');
            $sheet->setCellValue('D' . $row, $salary->position_name ?? '-');
            $sheet->setCellValue('E' . $row, str_pad($salary->month, 2, '0', STR_PAD_LEFT));
            $sheet->setCellValue('F' . $row, $salary->year);
            $sheet->setCellValue('G' . $row, number_format($salary->base_salary, 0, ',', '.') . ' đ');
            $sheet->setCellValue('H' . $row, number_format($salary->allowance, 0, ',', '.') . ' đ');
            $sheet->setCellValue('I' . $row, number_format($salary->bonus, 0, ',', '.') . ' đ');
            $sheet->setCellValue('J' . $row, number_format($salary->deduction, 0, ',', '.') . ' đ');
            $sheet->setCellValue('K' . $row, number_format($salary->total_salary, 0, ',', '.') . ' đ');
            
            // Style cho row
            $sheet->getStyle('A' . $row . ':K' . $row)->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER);
            
            // Center align cho cột Tháng và Năm
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Right align cho các cột tiền
            $sheet->getStyle('G' . $row . ':K' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            $row++;
        }
        
        // Thêm dòng tổng (nếu có nhiều bản ghi)
        if ($row > 3) {
            $totalRow = $row;
            $sheet->setCellValue('A' . $totalRow, 'TỔNG CỘNG');
            $sheet->mergeCells('A' . $totalRow . ':F' . $totalRow);
            
            // Tính tổng
            $sheet->setCellValue('G' . $totalRow, '=SUM(G2:G' . ($totalRow - 1) . ')');
            $sheet->setCellValue('H' . $totalRow, '=SUM(H2:H' . ($totalRow - 1) . ')');
            $sheet->setCellValue('I' . $totalRow, '=SUM(I2:I' . ($totalRow - 1) . ')');
            $sheet->setCellValue('J' . $totalRow, '=SUM(J2:J' . ($totalRow - 1) . ')');
            $sheet->setCellValue('K' . $totalRow, '=SUM(K2:K' . ($totalRow - 1) . ')');
            
            // Style cho dòng tổng
            $sheet->getStyle('A' . $totalRow . ':K' . $totalRow)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => '1F2937']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F3F4F6']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ]);
            
            // Right align cho số tiền trong dòng tổng
            $sheet->getStyle('G' . $totalRow . ':K' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            
            // Format number cho dòng tổng
            $sheet->getStyle('G' . $totalRow . ':K' . $totalRow)->getNumberFormat()->setFormatCode('#,##0" đ"');
        }
        
        // Set độ rộng cột
        $sheet->getColumnDimension('A')->setWidth(12);  // Mã NV
        $sheet->getColumnDimension('B')->setWidth(25);  // Họ tên
        $sheet->getColumnDimension('C')->setWidth(20);  // Phòng ban
        $sheet->getColumnDimension('D')->setWidth(20);  // Chức vụ
        $sheet->getColumnDimension('E')->setWidth(10);  // Tháng
        $sheet->getColumnDimension('F')->setWidth(10);  // Năm
        $sheet->getColumnDimension('G')->setWidth(18);  // Lương cơ bản
        $sheet->getColumnDimension('H')->setWidth(15);  // Phụ cấp
        $sheet->getColumnDimension('I')->setWidth(15);  // Thưởng
        $sheet->getColumnDimension('J')->setWidth(15);  // Khấu trừ
        $sheet->getColumnDimension('K')->setWidth(20);  // Lương thực
        
        // Thêm border cho toàn bộ bảng
        $lastRow = $row - 1;
        if (isset($totalRow)) {
            $lastRow = $totalRow;
        }
        
        $sheet->getStyle('A1:K' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);
        
        return $spreadsheet;
    }
}