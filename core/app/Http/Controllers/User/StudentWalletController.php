<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WalletWithdrawRequest;
use App\Support\SafeSchema;
use Illuminate\Http\Request;

class StudentWalletController extends Controller
{
    public const MIN_WITHDRAW_BALANCE = 500;

    public function index()
    {
        if (! walletSchemaReady()) {
            $notify[] = ['warning', __('Wallet is not available yet. Ask the administrator to run database migrations (php artisan migrate).')];

            return to_route('user.home')->withNotify($notify);
        }

        $pageTitle = __('My Wallet');
        $user = auth()->user();
        $user->refresh();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'total_earned' => 0, 'total_withdrawn' => 0]
        );

        $uBal = (float) $user->balance;
        $wBal = (float) $wallet->balance;
        if (abs($uBal - $wBal) > 1e-8) {
            if ($wBal > $uBal) {
                $user->balance = $wBal;
                $user->save();
            } else {
                $wallet->balance = $uBal;
                $wallet->save();
            }
        }

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(20);

        $withdrawals = WalletWithdrawRequest::where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'wpage');

        return view('Template::user.wallet.referral', compact('pageTitle', 'user', 'wallet', 'transactions', 'withdrawals'));
    }

    public function withdrawRequest(Request $request)
    {
        if (! walletSchemaReady()) {
            $notify[] = ['warning', __('Wallet is not available yet. Ask the administrator to run database migrations (php artisan migrate).')];

            return back()->withNotify($notify);
        }

        $request->validate([
            'amount' => 'required|numeric|min:'.self::MIN_WITHDRAW_BALANCE,
            'note' => 'nullable|string|max:2000',
        ]);

        $user = auth()->user();
        $user->refresh();
        if (SafeSchema::hasColumn('users', 'upi_id') && ! $user->hasWithdrawalPayoutDetails()) {
            $notify[] = ['error', __('Add your UPI ID and/or bank account number with IFSC in Profile → Bank details before withdrawing.')];

            return back()->withNotify($notify);
        }
        $wallet = Wallet::where('user_id', $user->id)->first();

        if ((float) $user->balance < self::MIN_WITHDRAW_BALANCE) {
            $notify[] = ['error', __('Withdrawals require a balance of at least :min.', ['min' => self::MIN_WITHDRAW_BALANCE])];

            return back()->withNotify($notify);
        }

        $amount = (float) $request->amount;
        if ($amount > (float) $user->balance) {
            $notify[] = ['error', __('Amount exceeds your available balance.')];

            return back()->withNotify($notify);
        }

        if (WalletWithdrawRequest::where('user_id', $user->id)->where('status', 'pending')->exists()) {
            $notify[] = ['error', __('You already have a pending withdraw request.')];

            return back()->withNotify($notify);
        }

        WalletWithdrawRequest::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => 'pending',
            'note' => $request->note,
        ]);

        $notify[] = ['success', __('Withdraw request submitted. Our team will process it.')];

        return back()->withNotify($notify);
    }
}
