<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExportJob extends Model
{
    protected $fillable = ["status", "download_url", "type", "fields"];
    protected $casts = [
        "fields" => "array"
    ];
}
