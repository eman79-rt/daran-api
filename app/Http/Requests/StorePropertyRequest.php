<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->agency_id;
    }

    public function rules(): array
    {
        return ['title' => 'required|string|max:180', 'description' => 'required|string|max:10000', 'type' => 'required|in:apartment,shop,land,house', 'offer_type' => 'required|in:sale,rent', 'province_id' => 'required|exists:provinces,id', 'price_amount' => 'required|numeric|min:0', 'currency' => 'required|in:USD,EUR,SYP,TRY,SAR', 'address' => 'required|string|max:255', 'bedrooms' => 'nullable|integer|min:0|max:30', 'bathrooms' => 'nullable|integer|min:0|max:30', 'area_sqm' => 'required|numeric|gt:0', 'owner_phone' => 'required|string|max:30', 'images' => 'sometimes|array|max:20', 'images.*' => 'required|string|max:2048'];
    }

    public function messages(): array
    {
        return ['title.required' => 'عنوان العقار مطلوب', 'description.required' => 'وصف العقار مطلوب', 'province_id.exists' => 'المحافظة المحددة غير موجودة', 'price_amount.required' => 'السعر مطلوب', 'currency.required' => 'العملة مطلوبة', 'area_sqm.gt' => 'المساحة يجب أن تكون أكبر من صفر', 'images.max' => 'يمكن إضافة 20 صورة كحد أقصى'];
    }
}
