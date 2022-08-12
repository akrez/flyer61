<?php

namespace App\Models;

use App\Helper;
use App\Http\Requests\IndexEntityLogRequest;
use App\Traits\JalaliDateFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntityLog extends Model
{
    use SoftDeletes;
    use HasFactory;
    use JalaliDateFormat;

    public static function getEntityRule($ruleKey, $prependRequired = true)
    {
        $rule = [];

        $isEntityBase = in_array($ruleKey, [
            'barcode',
            'upload_seq',
            'created_at_since',
            'created_at_until',
            'updated_at_since',
            'updated_at_until',
        ]);

        if ($isEntityBase) {
            $rule = Entity::getEntityRule($ruleKey, $prependRequired);
        } else {
            if ('attribute' == $ruleKey) {
                $rule = ['max:32'];
            } elseif ('old_value' == $ruleKey) {
                $rule = ['max:2048'];
            } elseif ('new_value' == $ruleKey) {
                $rule = ['max:2048'];
            }

            if ($prependRequired) {
                array_unshift($rule, 'required');
            } else {
                array_unshift($rule, 'nullable');
            }
        }

        return $rule;
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * buildEntityQuery.
     *
     * @param mixed $request
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    protected static function buildEntityLogQuery(IndexEntityLogRequest $request): \Illuminate\Database\Eloquent\Builder
    {
        $entities = EntityLog::query();

        if ($request->upload_seq) {
            $entities = $entities->where('upload_seq', '=', $request->upload_seq);
        }

        if ($request->attribute) {
            $entities = $entities->where('attribute', 'LIKE', $request->attribute);
        }

        if ($request->barcode) {
            $entities = $entities->where('barcode', 'LIKE', '%'.$request->barcode.'%');
        }
        if ($request->old_value) {
            $entities = $entities->where('old_value', 'LIKE', '%'.$request->old_value.'%');
        }
        if ($request->new_value) {
            $entities = $entities->where('new_value', 'LIKE', '%'.$request->new_value.'%');
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
            ->orderBy('barcode', 'DESC')
            ->orderBy('attribute', 'DESC')
            ->orderBy('upload_seq', 'ASC')
        ;

        return $entities;
    }
}
