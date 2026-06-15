<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentChannel;
use Illuminate\Validation\Rule;
class PaymentChannelController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', PaymentChannel::class);

        $channels = PaymentChannel::where('school_id', auth()->user()->school_id)->get();
        return view('payment_channels.index', compact('channels'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', PaymentChannel::class);

        $request->validate([
            'type' => 'required|in:paybill,till,send_money',
            'identifier' => ['required', Rule::unique('payment_channels', 'identifier')],
            'account_pattern' => 'nullable',
            'school_id' => 'prohibited',
        ]);

        PaymentChannel::create([
            'type' => $request->type,
            'identifier' => $request->identifier,
            'account_pattern' => $request->account_pattern,
        ]);

        return redirect()->back()->with('success', 'Payment channel added successfully.');
    }

    public function update(Request $request, $id)
    {
        $channel = PaymentChannel::where('school_id', auth()->user()->school_id)->findOrFail($id);
        $this->authorize('update', $channel);

        $request->validate([
            'type' => 'required|in:paybill,till,send_money',
            'identifier' => [
                'required',
                Rule::unique('payment_channels', 'identifier')->ignore($id),
            ],
            'account_pattern' => 'nullable',
            'school_id' => 'prohibited',
        ]);

        $channel->update([
            'type' => $request->type,
            'identifier' => $request->identifier,
            'account_pattern' => $request->account_pattern,
        ]);

        return redirect()->back()->with('success', 'Payment channel updated successfully.');
    }

    public function deactivate($id)
    {
        $channel = PaymentChannel::where('school_id', auth()->user()->school_id)->findOrFail($id);
        $this->authorize('update', $channel);
        $channel->update(['is_active' => 0]);
        return redirect()->back()->with('success', 'Payment channel deactivated.');
    }

    public function activate($id)
    {
        $channel = PaymentChannel::where('school_id', auth()->user()->school_id)->findOrFail($id);
        $this->authorize('update', $channel);
        $channel->update(['is_active' => 1]);
        return redirect()->back()->with('success', 'Payment channel activated.');
    }
}
