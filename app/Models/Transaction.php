<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = ['customer_id', 'type', 'amount', 'balance_before', 'balance_after'];

    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_WITHDRAWAL = 'withdrawal';
    public const TYPE_TRANSFER = 'transfer';

    public const TRANSACTION_TYPES = [
        self::TYPE_DEPOSIT,
        self::TYPE_WITHDRAWAL,
        self::TYPE_TRANSFER,
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
