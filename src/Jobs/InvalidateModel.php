<?php

namespace A17\CDN\Jobs;

use A17\CDN\CDN;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class InvalidateModel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $model;

    /**
     * Create a new job instance.
     *
     * @param array|null $model
     */
    public function __construct($model = null)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CDN::tags()->invalidateModel($this->model);
    }
}
