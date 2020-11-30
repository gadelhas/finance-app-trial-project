<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceController extends Controller
{
    public function __construct()
    {
        // Could add middleware here, but adding instead on web.php for code cleanliness.

        // Middleware : Auth
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $transactions = $user->transactions()->orderBy('date', 'DESC')->get();

        $balance = $transactions->sum('amount');

        return $balance;
    }
}
