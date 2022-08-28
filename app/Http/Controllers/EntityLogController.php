<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Http\Requests\IndexEntityLogRequest;
use App\Models\EntityLog;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

class EntityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexEntityLogRequest $request)
    {
        $entitylogs = EntityLog::buildEntityLogQuery($request)->paginate(100)->withQueryString();

        $attributes = EntityLog::select('attribute')
            ->groupBy('attribute')
            ->get()
            ->pluck('attribute')
            ->toArray()
        ;

        return view('entitylog.index', [
            'entitylogs' => $entitylogs,
            'attributes' => $attributes,
        ]);
    }

    public function export(IndexEntityLogRequest $request)
    {
        $entitylogs = EntityLog::buildEntityLogQuery($request)->get();

        $sheetsData = [
            [
                __('Index'),
                __('validation.attributes.barcode'),
                __('validation.attributes.user_id'),
                __('validation.attributes.attribute'),
                __('validation.attributes.old_value'),
                __('validation.attributes.new_value'),
                __('validation.attributes.upload_seq'),
                __('validation.attributes.created_at'),
                __('validation.attributes.updated_at'),
            ],
        ];

        foreach ($entitylogs as $entitylog) {
            $newRowIndex = count($sheetsData);
            $sheetsData[] = [
                $newRowIndex + 1,
                $entitylog->barcode,
                $entitylog->user ? $entitylog->user->email : '',
                __('validation.attributes.'.$entitylog->attribute),
                $entitylog->old_value,
                $entitylog->new_value,
                $entitylog->upload_seq,
                $entitylog->jalaliCreatedAt(),
                $entitylog->jalaliUpdatedAt(),
            ];
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('5');
        $sheet->setRightToLeft(true);

        foreach ($sheetsData as $sheetRowKey => $sheetRow) {
            $sheet->setCellValue('A'.($sheetRowKey + 1), $sheetRow[0]);
            $sheet->setCellValue('B'.($sheetRowKey + 1), $sheetRow[1]);
            $sheet->setCellValue('C'.($sheetRowKey + 1), $sheetRow[2]);
            $sheet->setCellValue('D'.($sheetRowKey + 1), $sheetRow[3]);
            $sheet->setCellValue('E'.($sheetRowKey + 1), $sheetRow[4]);
            $sheet->setCellValue('F'.($sheetRowKey + 1), $sheetRow[5]);
            $sheet->setCellValue('G'.($sheetRowKey + 1), $sheetRow[6]);
            $sheet->setCellValue('H'.($sheetRowKey + 1), $sheetRow[7]);
            $sheet->setCellValue('I'.($sheetRowKey + 1), $sheetRow[8]);
        }

        foreach (range('A', 'I') as $columns) {
            $sheet->getColumnDimension($columns)->setAutoSize(true);
        }

        $writer = new XlsxWriter($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="entities '.Helper::unixToJalali(\time(), 'Y-m-d').'.xlsx"');
        $writer->save('php://output');
    }
}
