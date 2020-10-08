<?php

namespace DefStudio\Burnout;

use DefStudio\Burnout\Models\BurnoutEntry;
use Facade\FlareClient\Flare;
use Facade\FlareClient\Report;
use Throwable;

class Burnout
{

    private Flare $flare_client;

    public function __construct()
    {
        $this->flare_client = app(Flare::class);
    }

    public function is_enabled(): bool
    {
        return (bool)config('burnout.enabled', false);
    }

    public function handle(Throwable $exception): void
    {

        $report = $this->flare_client->createReport($exception);

        $this->store_report($report);

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
