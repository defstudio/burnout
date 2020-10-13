<?php


namespace DefStudio\Burnout\Middleware;


use Closure;
use DefStudio\Burnout\Burnout;
use Exception;

class StoresExceptionsToBurnout
{
    /**
     * @var Burnout
     */
    private Burnout $burnout;

    public function __construct(Burnout $burnout)
    {
        $this->burnout = $burnout;
    }


    public function handle($request, Closure $next)
    {

        if (!$this->burnout->is_enabled()) {
            return $next($request);
        }

        try {
            $response = $next($request);
        } catch (Exception $exception) {
            $this->burnout->handle($request, $exception);
            throw $exception;
        }

        if (!empty($response->exception)) {
            $response = $this->burnout->handle($request, $response->exception);
        }

        return $response;
    }
}
