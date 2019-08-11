<?php

namespace App\Console\Commands\ComputeInstance\SetupRequest;

use App\ComputeInstanceSetupRequest;
use App\Constants\ComputeInstance\SetupRequestStatusCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Dispatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ci:sr:dispatch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch compute instance setup requests.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $excepts = [];
        while (true) {
            DB::beginTransaction();
            $setupRequest = ComputeInstanceSetupRequest::query()
                ->where("status", "=", SetupRequestStatusCode::STATUS_WAIT_FOR_DISPATCHING)
                ->whereNotIn("id", $excepts)
                ->lockForUpdate()
                ->limit(1)
                ->first()
            ;
            if (!$setupRequest)
                break;
            try {
                $setupRequest->status = SetupRequestStatusCode::STATUS_WAIT_FOR_SUBMITTING_TO_NODE;
                $setupRequest->save();
                $excepts[] = $setupRequest->id;
                /**
                 * @var ComputeInstanceSetupRequest $setupRequest
                 */
                $this->info("Dispatching $setupRequest->id.");
                $setupRequest->dispatch();
                DB::commit();
            } catch (\Throwable $throwable){
                DB::rollback();
                $this->error($throwable->getMessage());
            }
        }

        return 0;
    }
}
