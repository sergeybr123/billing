<?php

namespace App\Http\Controllers;

use App\AdditionalSubscribesType;
use Illuminate\Http\Request;

class AdditionalTypeController extends Controller
{
    public function all()
    {
        return response()->json(['type' => AdditionalSubscribesType::all()]);
    }
}
