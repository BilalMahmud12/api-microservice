<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use App\Services\AccountService;

class AccountController extends Controller
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function getBalance($id)
    {
        //
    }

    public function deposit($id, Request $request)
    {
        //return $this->accountService->deposit($id, 0);
    }

    public function withdraw($id, Request $request)
    {
        //return $this->accountService->withdraw($id, 0);
    }

    public function transfer(Request $request)
    {
        //return $this->accountService->transfer();
    }
}
