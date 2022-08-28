<?php

namespace App\Http\Requests;

use App\Models\Entity;
use Illuminate\Foundation\Http\FormRequest;

class IndexEntityLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'barcode' => Entity::getEntityRule('barcode', false),
            'upload_seq' => Entity::getEntityRule('upload_seq', false),
            'attribute' => Entity::getEntityRule('attribute', false),
            'old_value' => Entity::getEntityRule('old_value', false),
            'new_value' => Entity::getEntityRule('new_value', false),
            'created_at_since' => Entity::getEntityRule('created_at_since', false),
            'created_at_until' => Entity::getEntityRule('created_at_until', false),
            'updated_at_since' => Entity::getEntityRule('updated_at_since', false),
            'updated_at_until' => Entity::getEntityRule('updated_at_until', false),
        ];
    }
}
