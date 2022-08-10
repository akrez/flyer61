<?php

namespace App\Models;

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
}
