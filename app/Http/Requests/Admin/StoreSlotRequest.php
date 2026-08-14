<?php

namespace App\Http\Requests\Admin;

use App\Models\Slot;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => ['required', 'exists:services,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $exists = Slot::where('service_id', $this->input('service_id'))
                ->whereDate('date', $this->input('date'))
                ->where('start_time', $this->input('start_time'))
                ->exists();

            if ($exists) {
                $validator->errors()->add('start_time', 'A slot already exists for this service at this date and time.');
            }
        });
    }
}
