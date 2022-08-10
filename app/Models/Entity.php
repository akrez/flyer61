<?php

namespace App\Models;

use App\Helper;
use App\Http\Requests\IndexEntityRequest;
use App\Traits\JalaliDateFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Entity extends Model
{
    use HasFactory;
    use JalaliDateFormat;

    public const IMPORT_MODES = [
        'upsert',
        'update',
        'insert',
    ];

    public static $sortable = [
        'barcode',
        'entity_type',
        'title',
        'qty',
        'place',
        'created_at',
        'updated_at',
    ];
    public $incrementing = false;

    protected $primaryKey = 'barcode';
    protected $keyType = 'string';
    protected $fillable = ['title', 'qty', 'place', 'entity_type', 'description'];

    public static function getEntityRule($ruleKey, $appendRequired = true)
    {
        $rule = [];

        if ('barcode' == $ruleKey) {
            $rule = ['max:32'];
        } elseif ('entity_type' == $ruleKey) {
            $rule = ['max:32'];
        } elseif ('title' == $ruleKey) {
            $rule = ['max:512'];
        } elseif ('qty' == $ruleKey) {
            $rule = ['nullable', 'numeric', 'gt:0'];
        } elseif ('place' == $ruleKey) {
            $rule = ['max:32'];
        } elseif ('description' == $ruleKey) {
            $rule = ['max:2048'];
        } elseif ('import_mode' == $ruleKey) {
            $rule = [Rule::in(static::IMPORT_MODES)];
        } elseif ('file' == $ruleKey) {
            $rule = ['mimes:xls,xlsx'];
        } elseif ('created_at_since' == $ruleKey) {
            $rule = ['regex:/^\d{4}-\d{2}-\d{2}$/'];
        } elseif ('created_at_until' == $ruleKey) {
            $rule = ['regex:/^\d{4}-\d{2}-\d{2}$/'];
        } elseif ('updated_at_since' == $ruleKey) {
            $rule = ['regex:/^\d{4}-\d{2}-\d{2}$/'];
        } elseif ('updated_at_until' == $ruleKey) {
            $rule = ['regex:/^\d{4}-\d{2}-\d{2}$/'];
        } else {
            exit($ruleKey);
        }

        if ($appendRequired) {
            array_unshift($rule, 'required');
        } else {
            array_unshift($rule, 'nullable');
        }

        return $rule;
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

        if ($request->upload_seq) {
            $entities = $entities->where('upload_seq', '=', $request->upload_seq);
        }
        if ($request->qty) {
            $entities = $entities->where('qty', '=', $request->qty);
        }

        if ($request->entity_type) {
            $entities = $entities->where('entity_type', 'LIKE', '%'.$request->entity_type.'%');
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
