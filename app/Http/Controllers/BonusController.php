<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BonusController extends Controller
{
    public $referidos;

    public function __construct()
    {
        $this->referidos = new TreeController;
    }
    public function checkBonus()
    {
        $total_red = $this->referidos->getTotalUser(Auth::user()->id);
        dd($total_red);
        
    }
}
