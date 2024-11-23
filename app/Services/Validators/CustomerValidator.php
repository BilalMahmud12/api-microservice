<?php

namespace App\Services\Validators;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CustomerValidator
{
    public static function validateCreateCustomer(Request $request)
    {
        Log::info('Start validating create customer data' . json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'name' => 'required|filled|not_in:""|string|min:3|max:255',
            'surname' => 'required|filled|not_in:""|string|min:3|max:255',
            'balance' => 'prohibited|sometimes',
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed: ' . json_encode($validator->errors()));
            return response()->json($validator->errors(), 422);
        }

        Log::info('Validation passed');
        return true;
    }

    public static function validateUpdateCustomer(Request $request)
    {
        Log::info('Start validating update customer data' . json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|filled|not_in:""|string|min:3|max:255',
            'surname' => 'sometimes|required|filled|not_in:""|string|min:3|max:255',
            'balance' => 'prohibited|sometimes',
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed: ' . json_encode($validator->errors()));
            return response()->json($validator->errors(), 422);
        }

        Log::info('Validation passed');
        return true;
    }

    public static function validatePaginationParams($page, $per_page)
    {
        Log::info("Validating pagination params: page={$page}, per_page={$per_page}");

        $rules = [
            'page' => 'sometimes|required|numeric|integer|min:1',
            'per_page' => 'sometimes|required|numeric|integer|min:1',
        ];

        $messages = [
            'page.numeric' => 'The page param must be a numeric value.',
            'page.integer' => 'The page param must be an integer.',
            'page.min' => 'The page param must be a positive integer greater than zero.',
            'per_page.numeric' => 'The per_page param must be a numeric value.',
            'per_page.integer' => 'The per_page param must be an integer.',
            'per_page.min' => 'The per_page param must be a positive integer greater than zero.',
        ];

        $validator =
        Validator::make([
            'page' => $page,
            'per_page' => $per_page,
        ], $rules, $messages);


        if ($validator->fails()) {
            Log::info('Validation failed: ' . json_encode($validator->errors()));
            return response()->json($validator->errors(), 422);
        }

        Log::info('Validation passed');
        return true;
    }
}
