<?php

namespace DefStudio\Burnout;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DefStudio\Burnout\Burnout
 */
class BurnoutFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'burnout';
    }
}
