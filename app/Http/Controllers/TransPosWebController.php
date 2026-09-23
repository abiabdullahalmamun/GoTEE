<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransPosWebController extends Controller
{
    public function create(Request $request)
    {

        return view('pages.trans.sell.pos');
    }
}
