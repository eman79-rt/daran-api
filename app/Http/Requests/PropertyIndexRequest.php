<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertyIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['province_id' => 'nullable|integer|exists:provinces,id', 'offer_type' => 'nullable|in:sale,rent', 'type' => 'nullable|in:apartment,shop,land,house', 'price_min' => 'nullable|numeric|min:0', 'price_max' => 'nullable|numeric|gte:price_min', 'per_page' => 'nullable|integer|min:1|max:100', 'cursor' => 'nullable|string', 'infinite' => 'sometimes|boolean'];
    }

    public function messages(): array
    {
        return ['province_id.exists' => 'المحافظة المحددة غير موجودة', 'offer_type.in' => 'نوع العرض يجب أن يكون sale أو rent', 'type.in' => 'نوع العقار غير صالح', 'price_min.numeric' => 'أدنى سعر يجب أن يكون رقمًا', 'price_max.gte' => 'أعلى سعر يجب أن يكون أكبر من أدنى سعر', 'per_page.max' => 'الحد الأعلى لحجم الصفحة هو 100', 'per_page.min' => 'حجم الصفحة يجب أن يكون واحدًا على الأقل'];
    }
}
