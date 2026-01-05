<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function showBalance(Request $request)
    {
        $wallet = Wallet::where('user_id', $request->user()->id)->first();

        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $request->user()->id,
                'balance' => 0.00
            ]);
        }

        return response()->json([
            'balance' => $wallet->balance
        ]);
    }

    public function addFunds(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        $wallet = Wallet::where('user_id', $request->user()->id)->first();

        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $request->user()->id,
                'balance' => 0.00
            ]);
        }

        $wallet->balance += $request->amount;
        $wallet->save();

        return response()->json([
            'message' => 'تم إضافة الرصيد بنجاح',
            'balance' => $wallet->balance
        ]);
    }
}
