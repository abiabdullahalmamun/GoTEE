<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HkProdCategoryWebController extends Controller
{
    public function index(Request $request)
    {

        return view('pages.hk.trans_source_type.index');
    }
}
