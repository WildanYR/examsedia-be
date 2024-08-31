<?php
namespace App\Http\Controllers;

use App\Traits\ApiResponder;
use App\Traits\ValidationMessages;

class ApiController extends Controller {
  use ApiResponder, ValidationMessages;
}