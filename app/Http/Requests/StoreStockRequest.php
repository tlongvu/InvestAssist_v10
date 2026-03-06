<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'symbol' => [
                'required', 
                'string', 
                'max:10', 
                \Illuminate\Validation\Rule::unique('stocks')->where(function ($query) {
                    return $query
                        ->where('exchange_id', $this->exchange_id)
                        ->where('user_id', $this->user()->id);
                })
            ],
            'exchange_id' => ['required', 'exists:exchanges,id'],
            'industry_id' => ['required', 'exists:industries,id'],
            'quantity' => ['required', 'integer', 'min:0'],
            'avg_price' => ['required', 'numeric', 'min:0'],
            'current_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    use \App\Traits\SanitizesMoneyInput;

    protected function prepareForValidation()
    {
        if ($this->has('symbol')) {
            $this->merge([
                'symbol' => strtoupper($this->symbol),
            ]);
        }

        // Clean commas and convert x1000 price input (e.g. 1.85) to raw DB value (e.g. 1850)
        if ($this->has('avg_price')) {
            $this->merge([
                'avg_price' => $this->sanitizeMoney($this->avg_price) * 1000,
            ]);
        }

        if ($this->has('current_price')) {
            $this->merge([
                'current_price' => $this->sanitizeMoney($this->current_price) * 1000,
            ]);
        }
    }
}
