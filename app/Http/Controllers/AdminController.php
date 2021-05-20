<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Admin};
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Cache;

class AdminController extends Controller
{

	 /**
     * Create a new controller instance. 
     * Only admin user can access the functions below except for show and filter function
     *
     * @return void
     */
    public function __construct(Request $request) {
        $this->middleware(['auth:adminApi'])->except(['index']);
    }


    public function index() {
		return view('admin');
	}
}
