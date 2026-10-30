<?php

namespace App\Http\Requests;

class UpdatePropertyRequest extends StorePropertyRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        foreach ($rules as $key => $rule) {
            if ($key === 'images.*') {
                continue;
            }
            $rules[$key] = preg_replace('/(^|\\|)required(\\||$)/', '$1sometimes$2', $rule);
        }
        $rules['status'] = 'sometimes|in:available,reserved,sold';

        return $rules;
    }
}
