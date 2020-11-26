<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BalanceController extends Controller
{
    /**
     * @var User
     */
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

        return $balance;
    }
}
