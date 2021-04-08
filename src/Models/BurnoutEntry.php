<?php
/*
 * Copyright (C) 2021. Def Studio
 *  Unauthorized copying of this file, via any medium is strictly prohibited
 *  Authors: Fabio Ivona <fabio.ivona@defstudio.it> & Daniele Romeo <danieleromeo@defstudio.it>
 */

namespace DefStudio\Burnout\Models;


use Carbon\Carbon;
use Closure;
use Exception;
use Facade\Ignition\Ignition;
use Facade\Ignition\IgnitionConfig;
use Illuminate\Database\Eloquent\Model;
use Laravel\Telescope\Http\Controllers\HomeController;
use Laravel\Telescope\IncomingExceptionEntry;
use Laravel\Telescope\Telescope;


/**
 * @property-read Carbon $created_at
 */
class BurnoutEntry extends Model
{
    protected $fillable = [
        'message',
        'file',
        'line',
        'trace',
        'throwable_class',
        'report',
    ];

    protected $casts = [
        'report' => 'json',
    ];


    public function view_data(): array
    {
        return [
            'throwableString'      => $this->throwable_string(),
            'telescopeUrl'         => $this->telescope_url(),
            'shareEndpoint'        => $this->share_endpoint(),
            'title'                => $this->title(),
            'config'               => $this->config(),
            'solutions'            => [],
            'report'               => $this->report,
            'housekeepingEndpoint' => url(config('ignition.housekeeping_endpoint_prefix', '_ignition')),
            'styles'               => $this->styles(),
            'scripts'              => $this->scripts(),
            'tabs'                 => $this->tabs(),
            'jsonEncode'           => Closure::fromCallable([
                $this,
                'json_encode',
            ]),
            'getAssetContents'     => Closure::fromCallable([
                $this,
                'get_asset_contents',
            ]),
            'defaultTab'           => 'stackTab',
            'defaultTabProps'      => [],
            'appEnv'               => config('app.env'),
            'appDebug'             => config('app.debug'),
        ];
    }

    private function throwable_string(): string
    {
        if (empty($this->message)) {
            return '';
        }

        $throwableString = sprintf("%s: %s in file %s on line %d\n\n%s\n", $this->throwable_class, $this->message, $this->file, $this->line, $this->trace);

        return htmlspecialchars($throwableString);
    }

    private function telescope_url(): ?string
    {
        try {
            if (!class_exists(Telescope::class)) {
                return null;
            }

            if (!count(Telescope::$entriesQueue)) {
                return null;
            }

            $telescopeEntry = collect(Telescope::$entriesQueue)->first(function ($entry) {
                return $entry instanceof IncomingExceptionEntry;
            });

            if (is_null($telescopeEntry)) {
                return null;
            }

            $telescopeEntryId = (string)$telescopeEntry->uuid;

            return url(action([
                    HomeController::class,
                    'index',
                ]) . "/exceptions/{$telescopeEntryId}");
        } catch (Exception $exception) {
            return null;
        }
    }

    private function share_endpoint(): string
    {
        try {
            // use string notation as L5.5 and L5.6 don't support array notation yet
            return action('\Facade\Ignition\Http\Controllers\ShareReportController');
        } catch (Exception $exception) {
            return '';
        }
    }

    public function title(): string
    {
        $message = htmlspecialchars($this->report['message']);

        return "🧨 {$message}";
    }

    private function config(): array
    {
        return app()->make(IgnitionConfig::class)->toArray();
    }

    private function styles(): array
    {
        return array_keys(Ignition::styles());
    }

    private function scripts(): array
    {
        return array_keys(Ignition::scripts());
    }

    private function tabs(): string
    {
        return json_encode(Ignition::$tabs);
    }

    private function get_asset_contents(string $asset): string
    {

        $assetPath = base_path("vendor/facade/ignition/resources/compiled/{$asset}");

        return file_get_contents($assetPath);
    }

    private function json_encode($data): string
    {
        $jsonOptions = JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;

        return json_encode($data, $jsonOptions);
    }
}
