<?php

namespace App\Services\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AccountValidator {
    public function validate(Request $request, string $operation = "") {
        $rules = [
            'funds' => [
                'required',
                'numeric',
                'regex:/^(?!0[1-9])\d+(\.\d{1,2})?$/',
                'min:0.01'
            ],
        ];

        if ($operation === 'transfer') {
            $rules = array_merge($rules, [
                'from' => ['required', 'integer', 'different:to'],
                'to' => ['required', 'integer']
            ]);
        }

        $validator = Validator::make($request->all(), $rules, $this->customMessages());

        if ($validator->fails()) {
            Log::error('Validation failed: ', $validator->errors()->toArray());
            return response()->json([
                'error' => 'Invalid input',
                'details' => $validator->errors()
            ], 400);
        }

        return true;
    }

    private function customMessages()
    {
        return [
            'funds.required' => 'The funds field is required',
            'funds.regex' => 'The funds field must not start with zeros',
            'funds.numeric' => 'The funds must be a valid number',
            'funds.min' => 'The funds must be at least 0.01',
            'from.required' => 'The sender ID is required',
            'from.integer' => 'The sender ID must be an integer',
            'from.different' => 'The sender and receiver IDs must be different',
            'to.required' => 'The receiver ID is required',
            'to.integer' => 'The receiver ID must be an integer',
        ];
    }
}
