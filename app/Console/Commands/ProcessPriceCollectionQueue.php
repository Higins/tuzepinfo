<?php

namespace App\Console\Commands;

use App\Services\RabbitMQ\RabbitMQService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessPriceCollectionQueue extends Command
{
    protected $signature = 'queue:process-price-collection';

    protected $description = 'Process the price collection queue';

    private RabbitMQService $rabbitMQ;

    public function __construct()
    {
        parent::__construct();
        $this->rabbitMQ = new RabbitMQService();
    }

    public function handle()
    {
        $this->info('Starting to process price collection queue...');

        $this->rabbitMQ->consume(function ($message) {
            try {
                $data = json_decode($message->body, true);

                switch ($data['type']) {
                    case 'price_collection_success':
                        Log::info("Successfully collected prices from source {$data['source_id']}");
                        break;
                    case 'price_collection_error':
                        Log::error("Failed to collect prices from source {$data['source_id']}: {$data['error']}");
                        break;
                    default:
                        Log::warning("Unknown message type: {$data['type']}");
                }

                $message->ack();
            } catch (\Exception $e) {
                Log::error("Error processing message: {$e->getMessage()}");
                $message->reject();
            }
        });

        $this->info('Queue processing completed.');
    }

    public function __destruct()
    {
        $this->rabbitMQ->close();
    }
}
