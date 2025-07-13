<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BaseApiController extends Controller
{
    use ApiResponseTrait;
    use AuthorizesRequests;
}
