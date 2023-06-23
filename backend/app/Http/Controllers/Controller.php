<?php

namespace App\Http\Controllers;

use App\Libraries\Core;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected $core;
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->core = new Core();
    }
}
