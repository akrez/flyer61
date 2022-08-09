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
        $entities = static::buildEntityQuery($request)->paginate(100)->withQueryString();

        return view('entity.index', [
            'entities' => $entities,
            'entity_types' => Entity::ENTITY_TYPES,
            'sortable' => Entity::$sortable,
        ]);
    }

    public function export(IndexEntityRequest $request)
    {
        $entities = static::buildEntityQuery($request)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setRightToLeft(true);

        $sheet->setCellValue('A1', __('Index'));
        $sheet->setCellValue('B1', __('validation.attributes.title'));
        $sheet->setCellValue('C1', __('validation.attributes.barcode'));
        $sheet->setCellValue('D1', __('validation.attributes.place'));
        $sheet->setCellValue('E1', __('validation.attributes.qty'));
        $sheet->setCellValue('F1', __('validation.attributes.description'));
        $sheet->setCellValue('G1', __('validation.attributes.entity_type'));
        $sheet->setCellValue('H1', __('validation.attributes.upload_seq'));
        $sheet->setCellValue('I1', __('validation.attributes.created_at'));
        $sheet->setCellValue('J1', __('validation.attributes.updated_at'));

        $rows = 2;
        foreach ($entities as $entity) {
            $sheet->setCellValue('A'.$rows, $rows - 1);
            $sheet->setCellValue('B'.$rows, $entity->title);
            $sheet->setCellValue('C'.$rows, $entity->barcode);
            $sheet->setCellValue('D'.$rows, $entity->place);
            $sheet->setCellValue('E'.$rows, $entity->qty);
            $sheet->setCellValue('F'.$rows, $entity->description);
            $sheet->setCellValue('G'.$rows, Entity::getEntityTypeName($entity->entity_type));
            $sheet->setCellValue('H'.$rows, $entity->upload_seq);
            $sheet->setCellValue('I'.$rows, $entity->jalaliCreatedAt());
            $sheet->setCellValue('J'.$rows, $entity->jalaliUpdatedAt());
            ++$rows;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new XlsxWriter($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="entities '.Helper::unixToJalali(\time(), 'Y-m-d').'.xlsx"');
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
            'entity_types' => Entity::ENTITY_TYPES,
        ]);
    }

    public function import(ImportEntityRequest $request)
    {
        $reader = new XlsxReader();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file('file')->getRealPath());
        $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
        $rows = $sheet->toArray();

        $maxUploadSeq = (int) Entity::max('upload_seq') + 1;

        $qtyIsRequired = (in_array($request->entity_type, ['mavad']));
        $rules = [
            'title' => Entity::getEntityRule('title'),
            'barcode' => Entity::getEntityRule('barcode'),
            'place' => Entity::getEntityRule('place'),
            'qty' => Entity::getEntityRule('qty', $qtyIsRequired),
            'description' => Entity::getEntityRule('description', false),
        ];

        $datas = [];
        foreach ($rows as $rowIndex => $row) {
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
            if (empty($barcode)) {
                continue;
            }

            $data = [
                'title' => $row[1],
                'barcode' => $barcode,
                'place' => $row[3],
                'qty' => $row[4],
                'description' => $row[5],
            ];

            $validator = Validator::make($data, $rules);
            if ($validator->fails()) {
                Session::flash('errors_header', (__('validation.attributes.barcode').' '.$barcode));

                return redirect()->route('entity-upload')->withErrors($validator);
            }

            $datas[$barcode] = $data;
        }

        $oldAttributesArray = [];
        $newAttributesArray = [];
        foreach ($datas as $barcode => $data) {
            $model = Entity::query()->firstWhere('barcode', '=', $barcode);
            if ($model) {
                $oldAttributes = $model->toArray();
                if (!$request->rewrite) {
                    continue;
                }
            } else {
                $model = new Entity();
                $oldAttributes = $model->toArray();
                $model->entity_type = $request->entity_type;
            }
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
                if ($entityLog->save()) {
                }
            }
        }

        return redirect()->route('entity-upload')->with('success', array_keys($newAttributesArray));
    }

    /**
     * buildEntityQuery.
     *
     * @param mixed $request
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected static function buildEntityQuery(IndexEntityRequest $request): \Illuminate\Database\Eloquent\Builder
    {
        $entities = Entity::query();

        if ($request->entity_type) {
            $entities = $entities->where('entity_type', '=', $request->entity_type);
        }
        if ($request->upload_seq) {
            $entities = $entities->where('upload_seq', '=', $request->upload_seq);
        }
        if ($request->qty) {
            $entities = $entities->where('qty', '=', $request->qty);
        }

        if ($request->barcode) {
            $entities = $entities->where('barcode', 'LIKE', '%'.$request->barcode.'%');
        }
        if ($request->title) {
            $entities = $entities->where('title', 'LIKE', '%'.$request->title.'%');
        }
        if ($request->place) {
            $entities = $entities->where('place', 'LIKE', '%'.$request->place.'%');
        }
        if ($request->description) {
            $entities = $entities->where('description', 'LIKE', '%'.$request->description.'%');
        }

        if ($request->created_at_since and $createdAtSince = Helper::jalaliToGregorian($request->created_at_since.' 00:00:00')) {
            $entities = $entities->where('created_at', '>', $createdAtSince);
        }
        if ($request->created_at_until and $createdAtUntil = Helper::jalaliToGregorian($request->created_at_until.' 23:59:59')) {
            $entities = $entities->where('created_at', '<', $createdAtUntil);
        }
        if ($request->updated_at_since and $updatedAtSince = Helper::jalaliToGregorian($request->updated_at_since.' 00:00:00')) {
            $entities = $entities->where('updated_at', '>', $updatedAtSince);
        }
        if ($request->updated_at_until and $updatedAtUntil = Helper::jalaliToGregorian($request->updated_at_until.' 23:59:59')) {
            $entities = $entities->where('updated_at', '<', $updatedAtUntil);
        }

        $entities
            ->orderBy('upload_seq', 'DESC')
            ->orderBy('barcode', 'DESC');

        return $entities;
    }
}
