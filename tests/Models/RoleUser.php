<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Paxha\HasManyThroughDeep\HasTableAlias;

class RoleUser extends Pivot
{
    use HasTableAlias;
}
