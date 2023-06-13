<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class WriteParcedData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $data;
    protected $key;

    public function __construct($data, $key)
    {
        $this->data = $data;
        $this->key = $key;
    }

    public function handle()
    {
        try {
            DB::beginTransaction();
            DB::table('rows')->insert($this->data);
            DB::commit();
            Redis::set($this->key, count($this->data));
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
