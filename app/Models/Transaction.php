<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



/**
 * Class Transaction.
 *
 * @property string $payable_type
 * @property int $payable_id
 * @property int $wallet_id
 * @property string $uuid
 * @property string $type
 * @property int|string $amount
 * @property float $amountFloat
 * @property bool $confirmed
 * @property array $meta
 * @property Wallet $payable
 * @property WalletModel $wallet
 */
class Transaction extends Model
{
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_WITHDRAW = 'withdraw';

    /**
     * @var array
     */
    protected $fillable = [
        'payable_type',
        'payable_id',
        'wallet_id',
        'uuid',
        'type',
        'amount',
        'confirmed',
        'meta',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'wallet_id' => 'int',
        'confirmed' => 'bool',
        'meta' => 'json',
    ];

    /**
     * {@inheritdoc}
     */
    
}
