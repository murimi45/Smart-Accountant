<?php

namespace App\Http\Controllers;
use App\Models\SmsLog;
use Illuminate\Http\Request;

class SmsLogController extends Controller
{
    public function index()
{
$logs = SmsLog::latest()->paginate(50);
return view('sms.logs', compact('logs'));
}
}
