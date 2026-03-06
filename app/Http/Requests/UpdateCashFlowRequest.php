<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashFlowRequest extends FormRequest
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
            'exchange_id' => ['required', 'exists:exchanges,id'],
            'type' => ['required', 'in:deposit,withdraw'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('amount')) {
            $this->merge([
                'amount' => $this->sanitizeMoney($this->amount),
            ]);
        }
    }
}
