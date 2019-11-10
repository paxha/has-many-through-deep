<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model as Base;
use Paxha\HasManyThroughDeep\HasRelationships;

abstract class Model extends Base
{
    use HasRelationships;

    public $timestamps = false;
}
