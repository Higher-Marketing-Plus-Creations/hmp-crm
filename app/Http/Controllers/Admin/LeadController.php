<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendLeadNotificationJob;
use App\Models\Form;
use App\Models\Lead;
use App\Models\Website;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $query = Lead::query()->with(['website', 'form']);

        $query
            ->when($request->filled('website_id'), fn ($builder) => $builder->where('website_id', $request->integer('website_id')))
            ->when($request->filled('form_id'), fn ($builder) => $builder->where('form_id', $request->integer('form_id')))
            ->when($request->filled('email_status'), fn ($builder) => $builder->where('email_status', $request->string('email_status')))
            ->when($request->filled('status'), fn ($builder) => $builder->where('status', $request->string('status')))
            ->when($request->filled('date_from'), fn ($builder) => $builder->whereDate('created_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($builder) => $builder->whereDate('created_at', '<=', $request->date('date_to')));

        return view('admin.leads.index', [
            'leads' => $query->latest()->paginate(20)->withQueryString(),
            'websites' => Website::query()->orderBy('website_name')->get(),
            'forms' => Form::query()->orderBy('form_name')->get(),
            'filters' => $request->all(),
        ]);
    }

    public function show(Lead $lead): View
    {
        if ($lead->status === 'new') {
            $lead->update(['status' => 'read']);
        }

        return view('admin.leads.show', [
            'lead' => $lead->load(['website.client', 'form', 'emailLogs' => fn ($query) => $query->latest()]),
        ]);
    }

    public function updateStatus(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,read,spam'],
        ]);

        $lead->update(['status' => $data['status']]);

        return back()->with('status', 'Lead status updated.');
    }

    public function retryEmail(Lead $lead): RedirectResponse
    {
        $lead->update(['email_status' => 'pending']);
        SendLeadNotificationJob::dispatch($lead);

        return back()->with('status', 'Email retry queued successfully.');
    }

    public function export(Request $request): StreamedResponse
    {
        $filename = 'leads-export-' . now()->format('Ymd-His') . '.csv';
        $query = Lead::query()->with(['website', 'form']);

        $query
            ->when($request->filled('website_id'), fn ($builder) => $builder->where('website_id', $request->integer('website_id')))
            ->when($request->filled('form_id'), fn ($builder) => $builder->where('form_id', $request->integer('form_id')))
            ->when($request->filled('email_status'), fn ($builder) => $builder->where('email_status', $request->string('email_status')))
            ->when($request->filled('status'), fn ($builder) => $builder->where('status', $request->string('status')))
            ->when($request->filled('date_from'), fn ($builder) => $builder->whereDate('created_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($builder) => $builder->whereDate('created_at', '<=', $request->date('date_to')));

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID',
                'Website',
                'Form',
                'Name',
                'Email',
                'Phone',
                'Message',
                'Lead Status',
                'Email Status',
                'Page URL',
                'Created At',
            ]);

            $query->latest()->chunk(200, function ($leads) use ($handle) {
                foreach ($leads as $lead) {
                    fputcsv($handle, [
                        $lead->id,
                        $lead->website_name,
                        $lead->form_name,
                        $lead->visitor_name,
                        $lead->visitor_email,
                        $lead->visitor_phone,
                        $lead->message,
                        $lead->status,
                        $lead->email_status,
                        $lead->page_url,
                        $lead->created_at?->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
