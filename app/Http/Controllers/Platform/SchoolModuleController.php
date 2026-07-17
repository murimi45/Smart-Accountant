<?php

namespace App\Http\Controllers\Platform;

use App\Core\Modules\ModuleRegistry;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Schools;
use Illuminate\Http\Request;

class SchoolModuleController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->isPlatformAdmin(), 403);

        $modules = ModuleRegistry::allModules();
        $schools = Schools::query()
            ->with(['schoolModules.module'])
            ->orderBy('school_name')
            ->paginate(25);

        return view('platform.school-modules.index', compact('schools', 'modules'));
    }

    public function enable(Request $request, Schools $school, string $slug)
    {
        abort_unless(auth()->user()?->isPlatformAdmin(), 403);

        $data = $request->validate([
            'status' => 'nullable|in:active,trial',
        ]);

        ModuleRegistry::enableForSchool(
            (int) $school->id,
            $slug,
            $data['status'] ?? Module::STATUS_ACTIVE
        );

        return back()->with('success', "Enabled [{$slug}] for {$school->school_name}.");
    }

    public function disable(Schools $school, string $slug)
    {
        abort_unless(auth()->user()?->isPlatformAdmin(), 403);

        ModuleRegistry::disableForSchool((int) $school->id, $slug);

        return back()->with('success', "Disabled [{$slug}] for {$school->school_name}.");
    }
}
