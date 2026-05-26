<?php

namespace app\controllers\api;

use app\controllers\ManagerController;
use app\helpers\ChartHelpers;
use app\models\Roles;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Yii;
class XlsController extends ManagerController
{
    public function actionUsage(string $type_period, string $date_from, string $date_to, int $usage_only_unic, int $role): void
    {
        $chart_filters['role'] = $role;
        $chart_filters['start_date'] = $date_from;
        $chart_filters['end_date'] = $date_to;
        $chart_filters['usage_only_unic'] = $usage_only_unic;
        $chart_filters['type_period'] = $type_period;
        $res = ChartHelpers::getDataChart('chart_usage', $chart_filters, false);
       // debug($res);
        $exel_header[0] = 'Параметры';
        $exel_header[1] = 'Сумма';
        $exel_header = (array_merge($exel_header,$res['categories']));
       // debug($exel_header);
        $exel_data = [];
        $sum_sum=0;
        //echo '<pre>';
        //var_dump($res['series']);
        //echo '</pre>';
        foreach($res['series'] as $key => $val){
            $sum = array_sum($val['series']);
            $sum_sum +=$sum;
            $exel_data[$key]['name'] = $val['name'];
            $exel_data[$key]['sum'] = $sum ;
            $exel_data[$key] = array_merge($exel_data[$key],$val['series']);

        }

        //debug($exel_data);

        $search_date1 = date('d.m.Y', strtotime($date_from));
        $search_date2 = date('d.m.Y', strtotime($date_to));
        $unic = $usage_only_unic?'Да':'Нет';
        $role_text = $role?Roles::getSgRoleName($role):'Без учета';
        $type_period_text = 'По дням';
        switch ($type_period) {
            case 'week':
                $type_period_text = 'По неделям';
                break;
            case 'month':
                $type_period_text = 'По месяцам';
                break;
            case 'quart':
                $type_period_text = 'По кварталам';
                break;
            case 'year':
                $type_period_text = 'По годам';
                break;
            default;
        }
// Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

// Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Smartius Guide')
            ->setLastModifiedBy('Smartius Guide')
            ->setTitle('Office 2007 XLSX Document')
            ->setSubject('Office 2007 XLSX Document')
            ->setDescription('Document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Report');

        ///////////////////////////
        $namefile = "Отчёт_Статистика_использования";
        $spreadsheet->getActiveSheet()
            ->mergeCells('A1:B1')->setCellValue('A1', 'Выбранные фильтры')
            ->setCellValue('A2', 'Тип периода:')
                ->setCellValue('B2', $type_period_text)

            ->setCellValue('A3', 'Период:')     ->setCellValue('B3', $search_date1.' - '.$search_date2)
            ->setCellValue('A4', 'Уникальность:')     ->setCellValue('B4', $unic)
            ->setCellValue('A5', 'Учитывать роль:')     ->setCellValue('B5', $role_text);

        $i_column=0;
        foreach($exel_header as $head_text){
            $colString = Coordinate::stringFromColumnIndex(($i_column+1));
            $spreadsheet->getActiveSheet()
            ->setCellValue($colString.'7', $head_text);
            $i_column ++;

        }

        $i_row = 8;
        foreach($exel_data as $key => $data){

            $i_column=0;
            foreach ($data as $value){
                $colString = Coordinate::stringFromColumnIndex(($i_column+1));
                $spreadsheet->getActiveSheet()->setCellValue($colString.($i_row+$key),  $value);
                $i_column ++;
            }
            $spreadsheet->getActiveSheet()->getStyle('A'.($i_row+$key).':B'.($i_row+$key))->getFont()->setBold(true);
        }
        $i_row+=count($exel_data);
        $spreadsheet->getActiveSheet()
            ->setCellValue('A'.($i_row), 'ИТОГО:')
            ->setCellValue('B'.($i_row), $sum_sum);
        //debug($exel_data,1);

        // доп свойства
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(11);
        $spreadsheet->getActiveSheet()->getStyle('B3')->getAlignment()->setWrapText(true);
        $spreadsheet->getActiveSheet()->getStyle('7')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1:B1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('A7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);



        $numbers = range(8, ($i_row-1));


        for ($col = 'C'; $col <= $spreadsheet->getActiveSheet()->getHighestColumn(); $col++) {
            $spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            $str=0;
            foreach($numbers as $val){
                $str += $spreadsheet->getActiveSheet()->getCell($col.$val)->getValue();
            }
            $spreadsheet->getActiveSheet()->setCellValue($col.$i_row, $str);
        }
        $spreadsheet->getActiveSheet()->getStyle($i_row)->getFont()->setBold(true);
        /////////////////////
        $writer = new xls($spreadsheet);
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$namefile.'.xls"');

        $writer->save('php://output');
        //$writer->save('../web/files/temp.xls');
        exit;
    }
}


