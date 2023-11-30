<?php

namespace App\Http\Controllers;

use App\Traits\LogMaker;
use App\Traits\ResponseHelper;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Traits\Localizable;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, LogMaker, ResponseHelper, Localizable;
}
