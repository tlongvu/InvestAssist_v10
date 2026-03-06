<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    use \App\Traits\SanitizesMoneyInput;

    public function rules(): array
    {
        return [
            'stock_id' => ['required', 'exists:stocks,id'],
            'type' => ['required', 'in:buy,sell'],
            'quantity' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('price')) {
            $this->merge([
                'price' => $this->sanitizeMoney($this->price),
            ]);
        }
    }
}
