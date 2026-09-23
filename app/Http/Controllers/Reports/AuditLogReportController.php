<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogReportController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy('name')->get();

        // Set default dates if not provided
        $dateFrom = $request->date_from ?: now()->format('Y-m-d');
        $dateTo = $request->date_to ?: now()->format('Y-m-d');

        $logs = AuditLog::with('user')
            ->when($request->user_id, function($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })
            ->whereBetween('created_at', [
                $dateFrom . ' 00:00:00',
                $dateTo . ' 23:59:59'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('reports.auditlog.audit-logs', compact('logs', 'users'));
    }

    public function export(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->user_id, function($query) use ($request) {
                return $query->where('user_id', $request->user_id);
            })
            ->when($request->date_from && $request->date_to, function($query) use ($request) {
                return $query->whereBetween('created_at', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59'
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit_logs_'.now()->format('Y-m-d').'.csv"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Date & Time', 'User', 'Action', 'Model', 'IP Address',
                'Device', 'Old Values', 'New Values'
            ]);

            // CSV rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user?->name ?? 'System',
                    ucfirst($log->action),
                    $log->model_type ? class_basename($log->model_type) : 'N/A',
                    $log->ip_address,
                    $log->user_agent,
                    json_encode($log->old_values, JSON_PRETTY_PRINT),
                    json_encode($log->new_values, JSON_PRETTY_PRINT),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
