<?php

namespace Homeful\Mailmerge\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Homeful\Mailmerge\Mailmerge
 */
class Mailmerge extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Homeful\Mailmerge\Mailmerge::class;
    }
}
