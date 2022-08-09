<?php

namespace App\Http\Requests;

use App\Models\Entity;
use Illuminate\Foundation\Http\FormRequest;

class IndexEntityRequest extends FormRequest
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
            'entity_type' => Entity::getEntityRule('entity_type', false),
            'title' => Entity::getEntityRule('title', false),
            'qty' => Entity::getEntityRule('qty', false),
            'place' => Entity::getEntityRule('place', false),
            'description' => Entity::getEntityRule('description', false),
            'created_at_since' => Entity::getEntityRule('created_at_since', false),
            'created_at_until' => Entity::getEntityRule('created_at_until', false),
            'updated_at_since' => Entity::getEntityRule('updated_at_since', false),
            'updated_at_until' => Entity::getEntityRule('updated_at_until', false),
        ];
    }
}
