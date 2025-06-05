<?php

namespace App\Packages\RpdPayment\Facades;

use Illuminate\Support\Facades\Facade;

class ReportingApiService extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'ReportingApiService';
    }
}
