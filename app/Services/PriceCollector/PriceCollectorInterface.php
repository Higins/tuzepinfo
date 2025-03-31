<?php

namespace App\Services\PriceCollector;

use App\Models\PriceSource;

interface PriceCollectorInterface
{
    public function collect(PriceSource $source): array;

    public function canHandle(PriceSource $source): bool;
}
