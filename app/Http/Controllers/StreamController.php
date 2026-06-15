<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Models\Classes;
use App\Support\TenantRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StreamController extends Controller
{
    public function index()
    {
        $streams = Stream::with('class')
            ->latest()
            ->get();

        $classes = Classes::orderBy('order')->get();

        return view('streams.index', compact('streams', 'classes'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Stream::class);

        $request->validate([
            'class_id' => ['required', TenantRules::classes()],
            'name'     => 'required|string|max:10|unique:streams,name,NULL,id,class_id,' . $request->class_id,
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        Stream::create($request->only(['class_id', 'name']));

        return redirect()->route('streams.index')
            ->with('success', 'Stream created successfully.');
    }

    public function update(Request $request, Stream $stream)
    {
        $this->authorize('update', $stream);

        $request->validate([
            'class_id' => ['required', TenantRules::classes()],
            'name'     => 'required|string|max:10|unique:streams,name,' . $stream->id . ',id,class_id,' . $request->class_id,
            'school_id' => TenantRules::prohibitedSchoolId(),
        ]);

        $stream->update($request->only(['class_id', 'name']));

        return redirect()->route('streams.index')
            ->with('success', 'Stream updated successfully.');
    }

    public function destroy(Stream $stream)
    {
        $this->authorize('delete', $stream);

        if ($stream->enrollments()->exists()) {
            return redirect()->route('streams.index')
                ->with('error', 'Cannot delete stream with assigned students.');
        }

        $stream->delete();

        return redirect()->route('streams.index')
            ->with('success', 'Stream deleted successfully.');
    }
}
