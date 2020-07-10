<?php

namespace App\Models;
//http://www.mcdesignpro.com/blog/laravel/issue-deleting-role-zizacoentrust

use Zizaco\Entrust\Contracts\EntrustRoleInterface;
use Zizaco\Entrust\Traits\EntrustRoleTrait;
use Illuminate\Database\Eloquent\Model;

class Role extends Model  implements EntrustRoleInterface{
    use EntrustRoleTrait;
    public $timestamps = true;
}
