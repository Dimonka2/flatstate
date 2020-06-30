<?php
namespace dimonka2\flatstate;

use Illuminate\Support\Facades\Facade;

class Flatstate extends Facade
{
    /**
    * Get the registered name of the component.
    *
    * @return string
    */
   protected static function getFacadeAccessor() {
     return 'Flatstate'; 
   }
}
