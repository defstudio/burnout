<?php

namespace DefStudio\Burnout;

use DefStudio\Burnout\Models\BurnoutEntry;
use Facade\FlareClient\Flare;
use Facade\FlareClient\Report;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;

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

    public function handle($request, Throwable $exception)
    {

        $report = $this->flare_client->createReport($exception);

        $this->store_report($report);

        if (!$this->container->bound(ExceptionHandler::class) || !$request instanceof Request) {
            throw $exception;
        }

        $handler = $this->container->make(ExceptionHandler::class);

        $handler->report($exception);

        return $handler->render($request, $exception);
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
