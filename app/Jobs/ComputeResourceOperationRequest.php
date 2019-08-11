<?php

namespace App\Jobs;

use App\ComputeResourceOperationRequestLog;
use App\Constants\ComputeInstance\OperationRequest\StatusCode;
use App\Constants\ComputeResourceOperation\LogLevel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use YunInternet\CCMSCommon\Network\Exception\APIRequestException;

abstract class ComputeResourceOperationRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    /**
     * Must as protected property
     * @var \App\ComputeResourceOperationRequest
     */
    protected $computeResourceOperationRequest;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(\App\ComputeResourceOperationRequest $computeResourceOperationRequest)
    {
        $this->computeResourceOperationRequest = $computeResourceOperationRequest;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Throwable
     */
    public function handle()
    {
        try {
            $returnValue = $this->operationRequestHandle();
            // Is this is the last job in the chain?
            if (!count($this->chained)) {
                if (is_null($returnValue))
                    $returnValue = StatusCode::STATUS_SUCCESS;
                $this->getComputeResourceOperationRequest()->update([
                    "operation_status" => $returnValue,
                ]);

                if ($returnValue === StatusCode::STATUS_FAILED) {
                    try {
                        $this->onFailed();
                    } catch (\Exception $e) {
                    }
                }
                try {
                    $this->completed();
                } catch (\Exception $e) {
                }
            }
        } catch (APIRequestException $APIRequestException) {
            $this->getComputeResourceOperationRequest()->update(["operation_status" => StatusCode::STATUS_WAIT_FOR_RETRY]);
            $this->log(LogLevel::LEVEL_ERROR, $APIRequestException->getRawResponse());
            $this->releaseOrNot($APIRequestException);
        } catch (\Throwable $throwable) {
            $this->getComputeResourceOperationRequest()->update(["operation_status" => StatusCode::STATUS_WAIT_FOR_RETRY]);
            $this->log(LogLevel::LEVEL_ERROR, $throwable->getMessage());
            $this->releaseOrNot($throwable);
        } finally {
        }
    }

    /**
     * @return mixed
     * @throws \Throwable
     */
    abstract protected function operationRequestHandle();

    /**
     * @return \App\ComputeResourceOperationRequest
     */
    public function getComputeResourceOperationRequest(): \App\ComputeResourceOperationRequest
    {
        return $this->computeResourceOperationRequest;
    }

    public function getData()
    {
        return $this->getComputeResourceOperationRequest()->data;
    }

    public function getJSONDecodedData()
    {
        return json_decode($this->getData());
    }

    public function log($level, $log)
    {
        ComputeResourceOperationRequestLog::query()->create([
            "operation_request_id" => $this->getComputeResourceOperationRequest()->id,
            "log_level" => $level,
            "log" => $log,
        ]);
    }

    protected function onCompleted()
    {
    }

    protected function onFailed()
    {
    }

    final protected function completed()
    {
        $this->getComputeResourceOperationRequest()->update([
            "is_processed" => $this->getComputeResourceOperationRequest()->id,
        ]);

        try {
            $this->onCompleted();
        } catch (\Exception $e) {
        }
    }

    /**
     * The job failed to process.
     *
     * @param  \Exception  $exception
     * @return void
     */
    final public function failed(\Exception $exception)
    {
        $this->getComputeResourceOperationRequest()->update([
            "operation_status" => StatusCode::STATUS_FAILED,
        ]);

        try {
            $this->onFailed();
        } catch (\Exception $e) {
        }

        try {
            $this->completed();
        } catch (\Exception $e) {
        }
    }

    /**
     * @param \Throwable $throwable
     * @throws \Throwable
     */
    protected function releaseOrNot(\Throwable $throwable)
    {
        if ($this->attempts() < $this->tries) {
            $this->release(10);
        } else {
            throw $throwable;
        }
    }
}
