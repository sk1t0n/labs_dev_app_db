<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Db\Download;
use DB;

class MainController extends Controller
{
	public function index() {
		$rows = DB::table('product')->paginate(50);
		return view('index', ['rows' => $rows]);
    }

    public function download() {
		$answer = Download::download();
    	return view('download', ['answer' => $answer]);
    }
}
