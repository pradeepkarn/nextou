<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Excel_ctrl
{
    function generate_excel($event_id = 33717)
    {
        $obj = new Events_ctrl;
        $event = $obj->event_report($event_id);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB("D3D3D3");

        // Set headers
        $sheet->setCellValue('A1', 'S NO');
        $sheet->setCellValue('B1', 'NAME');
        $sheet->setCellValue('C1', 'IQMA NO');
        $sheet->setCellValue('D1', 'NATIONALITY');
        $sheet->setCellValue('E1', 'POSITION');
        $sheet->setCellValue('F1', 'COMPANY');
        $sheet->setCellValue('G1', 'MOB NO');
        $sheet->setCellValue('H1', 'CHECK IN');
        $sheet->setCellValue('I1', 'CHECK OUT');
        $sheet->setCellValue('J1', 'FOOD CATEGORY');

        // Set headers in cells K1 to AF1
        for ($i = 0; $i < 31; $i++) {
            $cellCoordinate = Coordinate::stringFromColumnIndex(11 + $i) . '1';
            $sheet->setCellValue($cellCoordinate, $i + 1);
            // Align cell content to the center
            $sheet->getStyle($cellCoordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        $sheet->setCellValue('AP1', 'TOTAL DAYS');

        // Set data
        $row = 2; // Start from row 2
        $emps = array_merge($event['employees'], $event['managers']);
        foreach ($emps as $key => $employee) {
            $mobile = strval($employee['isd_code'] . $employee['mobile']);
            $days = [];
            if (isset($employee['attendence'])) {
                foreach ($employee['attendence'] as $atn) {
                    $days[] = $atn['day'];
                }
            }
            // print_r($days);

            $color = $row % 2 == 0 ?  'FFFFFF' : 'D3D3D3'; // Alternate row colors (yellow and white)
            $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);

            $sheet->setCellValue('A' . $row, $key + 1);
            $sheet->setCellValue('B' . $row, $employee['first_name'] . " " . $employee['last_name']);
            $sheet->setCellValue('C' . $row, $employee['nid_no']);
            $sheet->setCellValue('D' . $row, $employee['country']);
            $sheet->setCellValue('E' . $row, $employee['position']);
            $sheet->setCellValue('F' . $row, $employee['company']);
            $sheet->setCellValue('G' . $row, $mobile);
            $sheet->setCellValue('H' . $row, "");
            $sheet->setCellValue('I' . $row, "");
            $sheet->setCellValue('J' . $row, $employee['food_category']);
            $attendence_count = 0;
            for ($i = 0; $i < 31; $i++) {
                $attend = in_array($i + 1, $days);
                $attenedence = $attend ? "P" : "A";
                $cellCoordinate = Coordinate::stringFromColumnIndex(11 + $i) . $row;
                $sheet->setCellValue($cellCoordinate, $attenedence);
                if ($attend) {
                    $sheet->getStyle($cellCoordinate)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00'); // Yellow
                    $attendence_count += 1;
                }
                $sheet->getStyle($cellCoordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
            $sheet->setCellValue('AP' . $row, $attendence_count);
            $attendence_count = 0;
            $row++;
        }

        // Save the Excel file
        $writer = new Xlsx($spreadsheet);
        $writer->save('example.xlsx');
        // echo 'Excel file generated successfully.';
    }
}