<?php

namespace App\Http\Requests\Api\V1\AuthorTickets;

use Illuminate\Foundation\Http\FormRequest;

class BaseAuthorTicketRequest extends FormRequest
{
    public function messages(): array
    {
        return [
            'data.attributes.status' => 'The status is not one of A, C, H or X.',
        ];
    }

    public function mappedAttributes(): array
    {
        $attributeMap = [
            'data.attributes.title' => 'title',
            'data.attributes.description' => 'description',
            'data.attributes.status' => 'status',
            'data.attributes.createdAt' => 'created_at',
            'data.attributes.updatedAt' => 'updated_at',
            'data.relationships.author.data.id' => 'user_id'
        ];

        $attributesToUpdate = [];

        foreach ($attributeMap as $key => $value)
        {
            if ($this->has($key)) {
                $attributesToUpdate[$value] = $this->input($key);
            }
        }

        return $attributesToUpdate;
    }
}
