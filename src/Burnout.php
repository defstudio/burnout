<?php

namespace DefStudio\Burnout;

use DefStudio\Burnout\Models\BurnoutEntry;
use Facade\FlareClient\Flare;
use Facade\FlareClient\Report;
use Illuminate\Contracts\Container\Container;

class Burnout
{

    private Flare $flare_client;

    private Container $container;

    public function __construct()
    {
        $this->flare_client = app(Flare::class);
        $this->container = app(Container::class);
    }

    public function is_enabled(): bool
    {
        return (bool)config('burnout.enabled', false);
    }


    private function store_report(Report $report): BurnoutEntry
    {
        $throwable = $report->getThrowable();

        $entry = BurnoutEntry::create([
            'message' => $throwable->getMessage(),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'trace' => $throwable->getTraceAsString(),
            'report' => $report->toArray(),
        ]);


        return $entry;
    }
}
