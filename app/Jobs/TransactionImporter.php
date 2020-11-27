<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TransactionImporter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $filename;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, string $filename)
    {
        //
        $this->user = $user;
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Reading file
        $file = fopen($this->filename, "r");

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


        $this->user->jobRunning = false;
        $this->user->save();
    }
}
