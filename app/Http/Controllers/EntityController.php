<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Http\Requests\ImportEntityRequest;
use App\Http\Requests\IndexEntityRequest;
use App\Models\Entity;
use App\Models\EntityLog;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;

class EntityController extends Controller
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
    public function index(IndexEntityRequest $request)
    {
        $entities = Entity::buildEntityQuery($request)->paginate(100)->withQueryString();

        return view('entity.index', [
            'entities' => $entities,
        ]);
    }

    public function export(IndexEntityRequest $request)
    {
        $sheetsData = [];
        $entities = Entity::buildEntityQuery($request)->get();
        foreach ($entities as $entity) {
            $sheetTitle = $entity->entity_type;
            if (!isset($sheetsData[$sheetTitle])) {
                $sheetsData[$sheetTitle] = [];
            }
            $newRowIndex = count($sheetsData[$sheetTitle]);
            $sheetsData[$sheetTitle][$newRowIndex] = [
                0 => $newRowIndex + 1,
                1 => $entity->title,
                2 => $entity->barcode,
                3 => $entity->place,
                4 => $entity->qty,
                5 => $entity->description,
                6 => $entity->entity_type,
                7 => $entity->upload_seq,
                8 => $entity->jalaliCreatedAt(),
                9 => $entity->jalaliUpdatedAt(),
            ];
        }

        $headerRow = [
            __('Index'),
            __('validation.attributes.title'),
            __('validation.attributes.barcode'),
            __('validation.attributes.place'),
            __('validation.attributes.qty'),
            __('validation.attributes.description'),
            __('validation.attributes.entity_type'),
            __('validation.attributes.upload_seq'),
            __('validation.attributes.created_at'),
            __('validation.attributes.updated_at'),
        ];

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach ($sheetsData as $sheetTitle => $sheetRows) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($sheetTitle);
            $sheet->setRightToLeft(true);

            array_unshift($sheetRows, $headerRow);
            foreach ($sheetRows as $sheetRowKey => $sheetRow) {
                $sheet->setCellValue('A' . ($sheetRowKey + 1), $sheetRow[0]);
                $sheet->setCellValue('B' . ($sheetRowKey + 1), $sheetRow[1]);
                $sheet->setCellValue('C' . ($sheetRowKey + 1), $sheetRow[2]);
                $sheet->setCellValue('D' . ($sheetRowKey + 1), $sheetRow[3]);
                $sheet->setCellValue('E' . ($sheetRowKey + 1), $sheetRow[4]);
                $sheet->setCellValue('F' . ($sheetRowKey + 1), $sheetRow[5]);
                $sheet->setCellValue('G' . ($sheetRowKey + 1), $sheetRow[6]);
                $sheet->setCellValue('H' . ($sheetRowKey + 1), $sheetRow[7]);
                $sheet->setCellValue('I' . ($sheetRowKey + 1), $sheetRow[8]);
                $sheet->setCellValue('J' . ($sheetRowKey + 1), $sheetRow[9]);
            }
            foreach (range('A', 'J') as $columns) {
                $sheet->getColumnDimension($columns)->setAutoSize(true);
            }
        }

        $writer = new XlsxWriter($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="entities ' . Helper::unixToJalali(\time(), 'Y-m-d') . '.xlsx"');
        $writer->save('php://output');
    }

    /**
     * Show the form for upload new resources.
     *
     * @return Response
     */
    public function upload()
    {
        return view('entity.upload', [
            'import_modes' => Entity::IMPORT_MODES,
        ]);
    }

    public function import(ImportEntityRequest $request)
    {
        $reader = new XlsxReader();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file('file')->getRealPath());

        $maxUploadSeq = (int) Entity::max('upload_seq') + 1;

        $rules = [
            'title' => Entity::getEntityRule('title'),
            'barcode' => Entity::getEntityRule('barcode'),
            'place' => Entity::getEntityRule('place', false),
            'qty' => Entity::getEntityRule('qty', false),
            'entity_type' => Entity::getEntityRule('entity_type'),
            'description' => Entity::getEntityRule('description', false),
        ];

        $datas = [];
        $duplicateBarcodes = [];
        $duplicateBarcodesAlerts = [];
        foreach ($spreadsheet->getAllSheets() as $sheetIndex => $sheet) {

            $sheetTitle = $sheet->getTitle();

            foreach ($sheet->toArray() as $rowIndex => $row) {
                if (0 == $rowIndex) {
                    continue;
                }

                $row = $row + array_fill(0, 6, null);
                array_walk($row, function (&$item, $key) {
                    if (is_scalar($item) and null !== $item) {
                        $item = trim($item);
                        if (0 == mb_strlen($item)) {
                            $item = null;
                        }
                    }
                });

                $barcode = $row[2];

                if ($this->isAllCellEmpty($row)) {
                    continue;
                }

                if (isset($datas[$barcode])) {
                    if (!isset($duplicateBarcodes[$barcode])) {
                        $duplicateBarcodes[$barcode] = 1;
                    }
                    $duplicateBarcodes[$barcode]++;
                    $duplicateBarcodesAlerts[$barcode] = $barcode . ' ✖ ' . $duplicateBarcodes[$barcode];
                    continue;
                }

                $data = [
                    'title' => $row[1],
                    'barcode' => $barcode,
                    'place' => $row[3],
                    'entity_type' => $sheetTitle,
                    'qty' => $row[4],
                    'description' => $row[5],
                ];

                $validator = Validator::make($data, $rules);
                if ($validator->fails()) {
                    Session::flash('errors_header', $sheetTitle . ', ' . __('Index') . ' ' . ($rowIndex + 1));
                    return redirect()->route('entity-upload')->withErrors($validator);
                }

                $datas[$barcode] = $data;
            }
        }

        if ($duplicateBarcodesAlerts) {
            ksort($duplicateBarcodesAlerts);
            Session::flash('errors_header', __('Duplicate barcode') . ' (' . count($duplicateBarcodesAlerts) . ' - ' . array_sum($duplicateBarcodes) . ')');
            return redirect()->route('entity-upload')->withErrors([
                'file' => $duplicateBarcodesAlerts,
            ]);
        }

        $oldAttributesArray = [];
        $newAttributesArray = [];
        foreach ($datas as $barcode => $data) {
            $model = Entity::query()->firstWhere('barcode', '=', $barcode);
            if ($model) {
                if (!in_array($request->import_mode, ['update', 'upsert'])) {
                    continue;
                }
            } else {
                $model = new Entity();
                if (!in_array($request->import_mode, ['insert', 'upsert'])) {
                    continue;
                }
            }
            $oldAttributes = $model->toArray();
            $model->fill($data);
            $newAttributes = $model->getDirty();
            $model->barcode = $barcode;
            $model->upload_seq = $maxUploadSeq;
            if ($newAttributes and $model->save()) {
                $oldAttributesArray[$barcode] = $oldAttributes;
                $newAttributesArray[$barcode] = $newAttributes;
            }
        }

        foreach ($newAttributesArray as $barcode => $newAttributes) {
            foreach ($newAttributes as $newAttributeName => $newAttributevalue) {
                $entityLog = new EntityLog();
                $entityLog->barcode = $barcode;
                $entityLog->user_id = Auth::id();
                $entityLog->attribute = $newAttributeName;
                $entityLog->old_value = (isset($oldAttributesArray[$barcode][$newAttributeName]) ? $oldAttributesArray[$barcode][$newAttributeName] : null);
                $entityLog->new_value = $newAttributevalue;
                $entityLog->upload_seq = $maxUploadSeq;
                if ($entityLog->save()) {
                }
            }
        }

        return redirect()->route('entity-upload')->with('success', array_keys($newAttributesArray));
    }

    private function isAllCellEmpty($row)
    {
        foreach ($row as $cell) {
            if ($cell) {
                return false;
            }
        }
        return true;
    }
}
