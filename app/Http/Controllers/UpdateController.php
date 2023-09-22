<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Update;
use Session;

class UpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        $request['user_id'] = auth()->user()->id;

        if (isset($request['is_visible'])) {
            if ($request['is_visible'] == "on") {
                $request['is_visible'] = 1;
            }
        } else {
            $request['is_visible'] = 0;
        }

        $validated = $request->validate([
            'title' => 'required|max:255',
            'version' => 'required|max:255',
            'writer' => 'required|max:255',
            'is_visible' => 'required|boolean',
            'description' => 'required',
            'user_id' => 'required|integer',
        ]);

        Update::create($validated);

        return redirect('/admin/updates');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function showUpdates()
    {

        $updates = Update::all(['created_at', 'title', 'version', 'description', 'is_visible'])
            ->where('is_visible', 1)
            ->sortByDesc('created_at');

        return view('pages.updates', [
            'updates' => $updates,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $update = Update::findOrFail($id);

        return view('admin.updates.edit', [
            'update' => $update,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $update = Update::findOrFail($id);

        $request['user_id'] = auth()->user()->id;

        if (isset($request['is_visible'])) {
            if ($request['is_visible'] == "on") {
                $request['is_visible'] = 1;
            }
        } else {
            $request['is_visible'] = 0;
        }

        $validated = $request->validate([
            'title' => 'required|max:255',
            'version' => 'required|max:255',
            'writer' => 'required|max:255',
            'is_visible' => 'required|boolean',
            'description' => 'required',
            'user_id' => 'required|integer',
        ]);

        $update->update($validated);

        return redirect('/admin/updates')->with(['status' => 'Successfully updated the update!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $update = Update::find($id);

        $update->delete();

        // redirect
        return redirect()->back()->with(['status' => 'Successfully deleted the update!']);
    }
}
