<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuditController extends Controller
{
    private $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function auditAllAccounts(): JsonResponse
    {
        Log::info('Start auditing balances for all customers.');

        try {
            $auditResults = $this->accountService->auditAllAccounts();

            Log::info('Audit completed successfully for all customers.');

            return response()->json(['message' => 'Audit completed', 'results' => $auditResults], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Audit failed: ' . $e->getMessage());

            return response()->json(['message' => 'Audit failed', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function auditSingleAccount(int $id): JsonResponse
    {
        Log::info('Start auditing balance for customer ID: ' . $id);

        try {
            $balance = $this->accountService->auditAccountBalance($id);
            Log::info('Audit completed successfully for customer ID: ' . $id);

            return response()->json(['message' => 'Audit completed', 'customer_id' => $id, 'balance' => $balance], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Audit failed for customer ID ' . $id . ': ' . $e->getMessage());

            return response()->json(['message' => 'Audit failed for customer', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
