<?php

namespace App\Http\Requests;

use App\Models\Entity;
use Illuminate\Foundation\Http\FormRequest;

class ImportEntityRequest extends FormRequest
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
            'entity_type' => Entity::getEntityRule('entity_type'),
            'rewrite' => Entity::getEntityRule('rewrite'),
            'file' => Entity::getEntityRule('file'),
        ];
    }
}
