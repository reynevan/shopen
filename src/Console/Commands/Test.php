<?php

namespace Shopen\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Shopen\Models\Order\Order;
use src\Mail\Order\OrderPlaced;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopen:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $order = Order::query()->latest()->first();
        Mail::to('test@example.com')->send(new OrderPlaced($order));

    }
}
