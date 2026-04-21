<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StreamController extends Controller
{
    /**
     * Display a listing of all Streams
     */
    public function index()
    {
        $streams = Stream::with('class')
                         ->latest()
                         ->get();

        $classes = Classes::all();   // Needed for Add & Edit modals

        return view('streams.index', compact('streams', 'classes'));
    }

    /**
     * Store a newly created Stream (from Modal)
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name'     => 'required|string|max:10|unique:streams,name,NULL,id,class_id,' . $request->class_id,
        ]);

        Stream::create($request->only(['class_id', 'name']));

        return redirect()->route('streams.index')
                         ->with('success', 'Stream created successfully.');
    }

    /**
     * Update the specified Stream (from Modal)
     */
    public function update(Request $request, Stream $stream)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name'     => 'required|string|max:10|unique:streams,name,' . $stream->id . ',id,class_id,' . $request->class_id,
        ]);

        $stream->update($request->only(['class_id', 'name']));

        return redirect()->route('streams.index')
                         ->with('success', 'Stream updated successfully.');
    }

    /**
     * Remove the specified Stream
     */
    public function destroy(Stream $stream)
    {
        // Prevent deletion if students are assigned
        if ($stream->students()->count() > 0) {
            return redirect()->route('streams.index')
                             ->with('error', 'Cannot delete stream with assigned students.');
        }

        $stream->delete();

        return redirect()->route('streams.index')
                         ->with('success', 'Stream deleted successfully.');
    }
}