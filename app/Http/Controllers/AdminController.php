<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('pages.admin');
    }

    public function showJobs()
    {
        return view('admin.jobs');
    }

    public function showAnalysis()
    {
        $tools = User::with(['tools:id,user_id,name,version,description', 'tools.toolParams:id,tool_id,param_name'])->get(['id', 'name', 'email']);

        return view('admin.analysis', [
            'data' => collect(json_decode($tools, true)),
            // 'tools_parameters' => collect(json_decode($tool_parameters, true)),
        ]);
    }
}
