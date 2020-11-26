<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionsController extends Controller
{
    protected $user;

    public function __construct()
    {
        // Could add middleware here, but adding instead on web.php for code cleanliness.

        // Middleware : Auth

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            return $next($request);
        });
    }

    public function index()
    {
        $transactions = Transaction::where('user_id', $this->user->id)->orderBy('date', 'DESC')->get();

        $balance = $transactions->sum('amount');

        $transactions = $transactions->groupBy(function($item) {
            return Carbon::createFromFormat("Y-m-d H:i:s", $item->date)->format('Y-m-d');
        });

        return response()->view('layouts.app', ['transactions' => $transactions]);
    }

    public function store()
    {

    }

    public function update(Transaction $transaction, Request $request)
    {
        if ($transaction->user->id == $this->user->id) {
            $validatedData = $request->validate([
                'label' => 'required',
                'amount' => 'required',
                'date' => 'required',
            ]);

            $transaction->update($validatedData);

            return $transaction;
        }

        return NULL;
    }
}
