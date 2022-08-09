<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
class WalletController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $user = User::where('id', Auth::id())->first();
        $user->deposit(10);
        $transaction = $user->deposit(100, null, false); // not confirm
        $history_trans = Transaction::where('wallet_id', $user->wallet->id)->get();
    
        $user->wallet->refreshBalance();
        $unconfirmed_balance = $history_trans->where('confirmed', '0')->sum('amount');
        
        return view('wallet',compact('user','unconfirmed_balance','history_trans'));

    }
}
