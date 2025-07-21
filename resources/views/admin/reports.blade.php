@extends('layouts.app')

@section('title', 'Admin Reports Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Admin Reports Dashboard</h1>

            <!-- Filter Form -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Leak Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="investigating" {{ request('status') == 'investigating' ? 'selected' : '' }}>Investigating</option>
                                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="severity" class="form-label">Leak Severity</label>
                                <select name="severity" id="severity" class="form-select">
                                    <option value="">All Severities</option>
                                    <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" name="date_from" id="date_from"
                                       value="{{ request('date_from') }}" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" name="date_to" id="date_to"
                                       value="{{ request('date_to') }}" class="form-control">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label for="user_id" class="form-label">User (Goals)</label>
                                <select name="user_id" id="user_id" class="form-select">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                                <a href="{{ route('admin.reports') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Leaks Section -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5>Reported Leaks ({{ $leaks->total() }})</h5>
                </div>
                <div class="card-body">
                    @if($leaks->isEmpty())
                        <div class="alert alert-info">No leak reports found</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date Reported</th>
                                        <th>Location</th>
                                        <th>Description</th>
                                        <th>Severity</th>
                                        <th>Status</th>
                                        <th>Image</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaks as $leak)
                                    <tr>
                                        <td>{{ $leak->created_at->format('d M Y H:i') }}</td>
                                        <td>{{ $leak->location }}</td>
                                        <td>{{ Str::limit($leak->description, 50) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $leak->severity == 'high' ? 'danger' : ($leak->severity == 'medium' ? 'warning' : 'success') }}">
                                                {{ ucfirst($leak->severity) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $leak->status == 'pending' ? 'secondary' : ($leak->status == 'investigating' ? 'primary' : 'success') }}">
                                                {{ ucfirst($leak->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($leak->image)
                                                <a href="{{ asset('storage/'.$leak->image) }}" target="_blank" class="btn btn-sm btn-info">
                                                    View Image
                                                </a>
                                            @else
                                                No Image
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <button class="btn btn-sm btn-primary me-2" data-bs-toggle="modal"
                                                    data-bs-target="#statusModal" data-id="{{ $leak->id }}">
                                                    Update Status
                                                </button>
                                                <a href="{{ route('leaks.show', $leak->id) }}" class="btn btn-sm btn-info">
                                                    Details
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $leaks->withQueryString()->links() }}
                    @endif
                </div>
            </div>

            <!-- Goals Section -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5>User Conservation Goals ({{ $goals->total() }})</h5>
                </div>
                <div class="card-body">
                    @if($goals->isEmpty())
                        <div class="alert alert-info">No conservation goals found</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Target Usage</th>
                                        <th>Current Progress</th>
                                        <th>Completion</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($goals as $goal)
                                    <tr>
                                        <td>{{ $goal->user->name }}</td>
                                        <td>{{ $goal->target_usage }} liters</td>
                                        <td>{{ $goal->current_progress ?? 0 }} liters</td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: {{ min(100, ($goal->current_progress / $goal->target_usage) * 100) }}%"
                                                    aria-valuenow="{{ ($goal->current_progress / $goal->target_usage) * 100 }}"
                                                    aria-valuemin="0" aria-valuemax="100">
                                                    {{ round(($goal->current_progress / $goal->target_usage) * 100, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $goal->created_at->format('d M Y') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#progressModal"
                                                data-id="{{ $goal->id }}"
                                                data-current="{{ $goal->current_progress ?? 0 }}">
                                                Update Progress
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $goals->withQueryString()->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="statusForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Update Leak Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="pending">Pending</option>
                            <option value="investigating">Investigating</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Progress Update Modal -->
<div class="modal fade" id="progressModal" tabindex="-1" aria-labelledby="progressModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="progressForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="progressModalLabel">Update Goal Progress</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_progress" class="form-label">Current Progress (liters)</label>
                        <input type="number" step="0.01" class="form-control" id="current_progress" name="current_progress" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Status Modal Handler
    document.getElementById('statusModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const leakId = button.getAttribute('data-id');
        const form = document.getElementById('statusForm');
        form.action = `/admin/leaks/${leakId}/status`;
    });

    // Progress Modal Handler
    document.getElementById('progressModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const goalId = button.getAttribute('data-id');
        const currentProgress = button.getAttribute('data-current');
        const form = document.getElementById('progressForm');
        form.action = `/admin/goals/${goalId}/progress`;
        document.getElementById('current_progress').value = currentProgress;
    });
</script>
@endpush
