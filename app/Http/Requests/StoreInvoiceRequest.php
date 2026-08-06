<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'guardian_id' => 'nullable|exists:users,id',
            'issue_date' => 'nullable|date',
            'due_date' => 'required|date',
            'description' => 'nullable|string|max:500',
            'items' => 'nullable|array',
            'items.*.item_type' => 'required|string|in:Kayıt Ücreti,Kitap,Deneme,Servis,Yemek,Diğer',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'amount' => 'nullable|numeric|min:0',
        ];
    }
}
