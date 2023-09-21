<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Update;

class UpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // $tools = User::with(['tools:id,user_id,name,version,description', 'tools.toolParams:id,tool_id,param_name'])->get(['id', 'name', 'email']);

        // return view('admin.analysis', [
        //     'data' => collect(json_decode($tools, true)),
        //     // 'tools_parameters' => collect(json_decode($tool_parameters, true)),
        // ]);

        return view('admin.updates', [
            'updates' => Update::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.updates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('admin.updates.edit', [
            'id' => $id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
