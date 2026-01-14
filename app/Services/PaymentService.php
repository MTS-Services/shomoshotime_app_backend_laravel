<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class PaymentService
{
      public function createPayment(array $data)
    {
        return DB::transaction(function () use ($data) {

            $data['created_by'] = Auth::id();
            return Payment::create($data);
        });
    }
}
