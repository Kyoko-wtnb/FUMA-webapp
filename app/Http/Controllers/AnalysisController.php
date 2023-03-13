<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tool;

class AnalysisController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($id = null)
    {
        $tools = Tool::orderBy('created_at', 'desc')
                ->get(['name', 'description', 'user_id']);
        
        return view('pages.analysis', ['tools' => collect(json_decode($tools, true))]);
    }
}
