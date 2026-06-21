<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = EmailLog::query()
            ->with('lead.website')
            ->when($request->filled('status'), fn ($builder) => $builder->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.email-logs.index', [
            'logs' => $logs,
            'filters' => $request->all(),
        ]);
    }
}
