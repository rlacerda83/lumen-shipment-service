<?php

namespace App\Models;

class Country extends BaseModel
{
    protected $table = 'country';

    protected $fillable = ['code', 'name'];
}
