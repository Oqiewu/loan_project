<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Loan extends Model
{
    protected $fillable = [
        'borrower_name',
        'amount',
        'interest_rate',
        'term',
        'status',
        'last_payment_date',
        'total_paid_amount',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function getRemainingAmountAttribute()
    {
        // Сумма ежемесячного платежа
        $monthly_payment = $this->amount * ($this->interest_rate / 12) *
                           pow(1 + $this->interest_rate / 12, $this->term) /
                           (pow(1 + $this->interest_rate / 12, $this->term) - 1);

        // Количество уже выполненных платежей
        $paid_payments_count = 0;
        if ($this->last_payment_date) {
            $paid_payments_count = floor(Carbon::now()->diffInDays($this->last_payment_date) / 30);
        }

        // Остаток по кредиту
        $remaining_amount = $this->amount - $this->total_paid_amount - $monthly_payment * $paid_payments_count;
        $remaining_amount = round($remaining_amount, 2);

        return $remaining_amount;
    }
}
