<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tool;
use App\Models\User;
use App\Models\ToolParameter;


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
        $tools = User::with(['tools:id,user_id,name,version,description', 'tools.toolParams:id,tool_id,param_name'])->get(['id','name','email']);

        return view('pages.analysis', [
            'data' => collect(json_decode($tools, true)),
            // 'tools_parameters' => collect(json_decode($tool_parameters, true)),
        ]);
    }
}
