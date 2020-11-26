<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $user = $this->user;

        $transaction = $user->transactions()->create($validatedData);

        return $transaction;
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

    public function destroy(Transaction $transaction)
    {
        if ($transaction->user->id == $this->user->id) {

            $transaction->delete();

            return $transaction;
        }

        return NULL;
    }

    public function bulk(Request $request)
    {
        $file = $request->file('file');

        // Todo: change this to a job instead of doing it here.

        // File Details
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $tempPath = $file->getRealPath();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

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

                // Import CSV to Database
                $filepath = public_path($location . "/" . $filename);

                // Reading file
                $file = fopen($filepath, "r");

                $importData_arr = [];
                $i = 0;

                while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE):
                    $num = count($filedata);

                    // Skip first row
                    if($i == 0){
                       $i++;
                       continue;
                    }
                    for ($c=0; $c < $num; $c++) {
                        $importData_arr[$i][] = $filedata [$c];
                    }
                    $i++;
                endwhile;

                fclose($file);

                DB::beginTransaction();

                foreach($importData_arr as $importData) {
                    $this->user->transactions()->create([
                        'label' => $importData[0],
                        'amount' => $importData[1],
                        'date' => $importData[2],
                    ]);
                }

                DB::commit();

                return response()->json(["message" => 'Imported ' . $i . ' transactions']);
            }
        }

    }
}
