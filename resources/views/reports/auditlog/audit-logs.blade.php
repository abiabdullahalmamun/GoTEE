@extends('layouts.app')

@push('style')
    <style>
        /* Audit Log Report Styles */
        .audit-log-table {
            font-size: 0.875rem;
        }

        .audit-log-table th {
            background-color: #f8fafc;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .audit-log-table td {
            vertical-align: top;
        }

        .log-action {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-weight: 500;
            font-size: 0.75rem;
        }

        .log-action.created, .log-action.login {
            background-color: #dcfce7;
            color: #166534;
        }

        .log-action.updated {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .log-action.deleted, .log-action.logout {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .log-action.failed_login {
            background-color: #fef9c3;
            color: #854d0e;
        }

        /* User avatar styling */
        .user-avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #e0e7ff;
            color: #4f46e5;
            font-weight: 600;
            margin-right: 0.5rem;
        }

        /* JSON pretty print styling */
        pre.json-pretty {
            background-color: #f8fafc;
            padding: 0.5rem;
            border-radius: 0.25rem;
            border: 1px solid #e2e8f0;
            font-size: 0.75rem;
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .filter-form .flex-wrap > div {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .filter-form button {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .value-grid {
                grid-template-columns: 1fr !important;
            }
        }

        .value-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .value-section {
            background-color: #f8fafc;
            border-radius: 0.375rem;
            padding: 0.5rem;
        }

        .value-title {
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
        }

        .value-title i {
            margin-right: 0.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto">
        <x-page-header title="Audit Log Report" />

        <div class="bg-white shadow rounded p-6 mt-1 mx-auto">
            <form method="GET" class="flex gap-4 items-end flex-wrap mb-6 filter-form">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" name="date_from" id="date_from"
                           value="{{ request('date_from', now()->format('Y-m-d')) }}"
                           class="px-3 py-2 border border-gray-300 rounded w-full focus:ring-primary focus:outline-none">
                </div>

                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" name="date_to" id="date_to"
                           value="{{ request('date_to', now()->format('Y-m-d')) }}"
                           class="px-3 py-2 border border-gray-300 rounded w-full focus:ring-primary focus:outline-none">
                </div>

                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">User</label>
                    <select name="user_id" id="user_id" class="px-3 py-2 border border-gray-300 rounded w-full focus:ring-primary focus:outline-none">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">
                        <i class="fas fa-search mr-2"></i> Filter
                    </button>
                    <button type="button" id="resetBtn"
                            class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">
                        <i class="fas fa-undo mr-2"></i> Reset
                    </button>
                    @if(request()->hasAny(['date_from', 'date_to', 'user_id']))
                        <button type="button" id="printBtn"
                                class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark hidden-print">
                            <i class="fas fa-print mr-2"></i> Print
                        </button>
                    @endif
                </div>
            </form>

            @if($logs->count() > 0)
                <div id="print-area">
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 audit-log-table">
                            <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-4 py-2 text-left">Date & Time</th>
                                <th class="border px-4 py-2 text-left">User</th>
                                <th class="border px-4 py-2 text-left">Action</th>
                                <th class="border px-4 py-2 text-left">Model</th>
                                <th class="border px-4 py-2 text-left">IP Address</th>
                                <th class="border px-4 py-2 text-left">Device</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-4 py-2 whitespace-nowrap">
                                        {{ $log->created_at->format('d M Y H:i:s') }}
                                    </td>
                                    <td class="border px-4 py-2">
                                        @if($log->user)
                                            <div class="flex items-center">
                                                <span class="user-avatar">{{ substr($log->user->name, 0, 1) }}</span>
                                                <div>
                                                    <div class="font-medium">{{ $log->user->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $log->user->email }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-500">System</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2">
                                    <span class="log-action {{ $log->action }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                    </td>
                                    <td class="border px-4 py-2">
                                        @if($log->model_type)
                                            <span class="font-medium">{{ class_basename($log->model_type) }}</span>
                                            <div class="text-xs text-gray-500">#{{ $log->model_id }}</div>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 font-mono">
                                        {{ $log->ip_address }}
                                    </td>
                                    <td class="border px-4 py-2 text-xs">
                                        {{ $log->user_agent }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="border px-4 py-2 bg-gray-50">
                                        <div class="value-grid">
                                            <div class="value-section">
                                                <div class="value-title">
                                                    <i class="fas fa-history"></i> Old Values
                                                </div>
                                                <pre class="json-pretty">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                            <div class="value-section">
                                                <div class="value-title">
                                                    <i class="fas fa-sync-alt"></i> New Values
                                                </div>
                                                <pre class="json-pretty">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->withQueryString()->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-10 text-gray-500">
                    <i class="fas fa-clipboard-list fa-3x mb-4"></i>
                    <p class="text-lg">No audit logs found matching your criteria</p>
                    @if(request()->hasAny(['date_from', 'date_to', 'user_id']))
                        <button onclick="window.location.href='{{ route('audit-logs.index') }}'"
                                class="mt-4 bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">
                            <i class="fas fa-undo mr-2"></i> Reset Filters
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Reset form
                document.getElementById('resetBtn')?.addEventListener('click', function() {
                    // Reset to today's date
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('date_from').value = today;
                    document.getElementById('date_to').value = today;
                    document.getElementById('user_id').value = '';
                    window.location.href = "{{ route('audit-logs.index') }}?date_from=" + today + "&date_to=" + today;
                });

                // Print functionality
                document.getElementById('printBtn')?.addEventListener('click', function() {
                    window.print();
                });

                // Date validation
                const dateFrom = document.getElementById('date_from');
                const dateTo = document.getElementById('date_to');

                if (dateFrom && dateTo) {
                    dateFrom.addEventListener('change', function() {
                        dateTo.min = this.value;
                    });

                    dateTo.addEventListener('change', function() {
                        dateFrom.max = this.value;
                    });
                }

                // Initialize with today's date if no dates set
                if (!dateFrom.value && !dateTo.value) {
                    const today = new Date().toISOString().split('T')[0];
                    dateFrom.value = today;
                    dateTo.value = today;
                }
            });
        </script>
    @endpush
@endsection
