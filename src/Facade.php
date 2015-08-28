<?php
namespace DragonFly\Nag;

use Illuminate\Support\Facades\Facade as Base;

class Facade extends Base {

    protected static function getFacadeAccessor() { return 'ConvertersContract'; }

}