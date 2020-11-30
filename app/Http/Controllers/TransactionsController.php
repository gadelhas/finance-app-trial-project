<?php

namespace App\Http\Controllers;

use App\Jobs\TransactionImporter;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TransactionsController extends Controller
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

        $transactions = $transactions->groupBy(function ($item) {
            return Carbon::createFromFormat("Y-m-d H:i:s", $item->date)->format('Y-m-d');
        });

        return response()->view('layouts.app', ['transactions' => $transactions]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'label' => 'required',
            'amount' => 'required',
            'date' => 'required',
        ]);

        $user = $request->user();

        $transaction = $user->transactions()->create($validatedData);

        return $transaction;
    }

    public function update(Transaction $transaction, Request $request)
    {
        if ($transaction->user->id == $request->user()->id) {
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

    public function destroy(Request $request, Transaction $transaction)
    {
        if ($transaction->user->id == $request->user()->id) {

            $transaction->delete();

            return $transaction;
        }

        return NULL;
    }

    public function bulk(Request $request)
    {
        $file = $request->file('file');

        // File Details
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileSize = $file->getSize();

        // Valid File Extensions
        $valid_extension = array("csv");

        // 2MB in Bytes
        $maxFileSize = 2097152;

        // Check file extension
        if (in_array(strtolower($extension), $valid_extension)) {

            // Check file size
            if ($fileSize <= $maxFileSize) {

                // File upload location
                $location = 'uploads';

                // Upload file
                $file->move($location, $filename);

                // Save CSV in disk
                $filename = public_path($location . "/" . $filename);

                TransactionImporter::dispatch($this->user, $filename);

                // set an user flag, to know that a job is running.
                // so we can disable buttons and show message
                $this->user->jobRunning = true;
                $this->user->save();
            }
        }
    }

    public function apiIndex(Request $request)
    {
        $user = $request->user();
        $transactions = $user->transactions()->orderBy('date', 'DESC')->get();

        $transactions = $transactions->groupBy(function ($item) {
            return Carbon::createFromFormat("Y-m-d H:i:s", $item->date)->format('Y-m-d');
        });

        return $transactions;
    }
}
