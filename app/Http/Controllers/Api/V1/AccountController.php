<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use App\Services\AccountService;
use App\Services\Validators\AccountValidator;
use App\Exceptions\ValidationException;


class AccountController extends Controller
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function getBalance($id)
    {
        Log::info('Start get balance for customer ID: ' . $id);

        try {
            $balance = $this->accountService->getBalance($id);
            Log::info('Get balance successful: ' . $balance . ' for customer ID: ' . $id);

            return response()->json(['balance' => $balance], Response::HTTP_OK);
        } catch (ValidationException $e) {
            Log::warning('Validation exception: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());

        } catch (\Exception $e) {
            Log::error('Get balance failed: ' . $e->getMessage());
            return response()->json(['error' => 'Get balance failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deposit($id, Request $request)
    {
        Log::info('Start deposit amount:' . $request->funds . ' to customer ID: ' . $id);

        $validationResult = (new AccountValidator())->validate($request);

        if ($validationResult !== true) {
            Log::error('Validation failed: ' . $validationResult);
            return response()->json(['message' => $validationResult], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->accountService->deposit($id, $request->funds);
            Log::info('Deposit successful: ' . $request->funds . ' to customer ID: ' . $id);

            return response()->json(['message' => 'Deposit successful'], Response::HTTP_OK);
        } catch (ValidationException $e) {
            Log::warning('Validation exception: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());

        } catch (\Exception $e) {
            Log::error('Deposit failed: ' . $e->getMessage());
            return response()->json(['message' => 'Deposit failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function withdraw($id, Request $request)
    {
        Log::info('Start withdraw amount:' . $request->funds . ' to customer ID: ' . $id);

        $validationResult = (new AccountValidator())->validate($request);

        if ($validationResult !== true) {
            Log::error('Validation failed: ' . $validationResult);
            return response()->json(['message' => $validationResult], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->accountService->withdraw($id, $request->funds);
            Log::info('Withdraw successful: ' . $request->funds . ' to customer ID: ' . $id);

            return response()->json(['message' => 'Withdraw successful'], Response::HTTP_OK);
        } catch (ValidationException $e) {
            Log::warning('Validation exception: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());

        } catch (\Exception $e) {
            Log::error('Withdraw failed: ' . $e->getMessage());
            return response()->json(['message' => 'Withdraw failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function transfer(Request $request)
    {
        Log::info('Start transfer amount:' . $request->funds . ' from customer ID: ' . $request->from . ' to customer ID: ' . $request->to);

        $validationResult = (new AccountValidator())->validate($request);

        if ($validationResult !== true) {
            Log::error('Validation failed: ' . $validationResult);
            return response()->json(['message' => $validationResult], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->accountService->transfer($request->only(['from', 'to', 'funds']));
            Log::info('Transfer successful: ' . $request->funds . ' from customer ID: ' . $request->from . ' to customer ID: ' . $request->to);

            return response()->json(['message' => 'Transfer successful'], Response::HTTP_OK);
        } catch (ValidationException $e) {
            Log::warning('Validation exception: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());

        } catch (\Exception $e) {
            Log::error('Transfer failed: ' . $e->getMessage());
            return response()->json(['message' => 'Transfer failed: ' . $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
