@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Inspection Visits') }}</h4>
    </div>

    <div class="container-fluid mt-3">
        <div class="mb-3 card">
            <div class="card-body">
                <div class="cardTitleBox">
                    <h5 class="card-title chartTitle">{{ __('All Visits') }}</h5>
                </div>
                <div class="table-responsive">
                    <table class="table border table-responsive-md">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Company') }}</th>
                                <th>{{ __('Inspector') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Visit Result') }}</th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visits as $visit)
                                <tr>
                                    <td>{{ $visit['id'] }}</td>
                                    <td>{{ $visit['customer_name'] ?? '-' }}</td>
                                    <td>{{ $visit['company_name'] ?? '-' }}</td>
                                    <td>{{ $visit['inspector_name'] ?? '-' }}</td>
                                    <td>{{ $visit['date'] ? \Carbon\Carbon::parse($visit['date'])->format('Y-m-d H:i') : '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $visit['status'] === 'APPROVED' ? 'success' : ($visit['status'] === 'REJECTED' ? 'danger' : 'warning') }}">
                                            {{ $visit['status'] }}
                                        </span>
                                    </td>
                                    <td>{{ $visit['visit_result'] ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('admin.inspectionVisit.show', $visit['id']) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-eye"></i> View
                                            </a>
                                            @if($visit['status'] === 'PENDING')
                                                <button type="button" class="btn btn-outline-success btn-sm"
                                                        onclick="updateStatus({{ $visit['id'] }}, 'APPROVED')">
                                                    <i class="fa-solid fa-check"></i> Approve
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                        onclick="updateStatus({{ $visit['id'] }}, 'REJECTED')">
                                                    <i class="fa-solid fa-times"></i> Reject
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="8">{{ __('No visits found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(isset($pagination) && $pagination['totalPages'] > 1)
            <div class="d-flex justify-content-center">
                <nav>
                    <ul class="pagination">
                        @if($pagination['page'] > 1)
                            <li class="page-item">
                                <a class="page-link" href="?page={{ $pagination['page'] - 1 }}">Previous</a>
                            </li>
                        @endif
                        
                        @for($i = 1; $i <= $pagination['totalPages']; $i++)
                            <li class="page-item {{ $i == $pagination['page'] ? 'active' : '' }}">
                                <a class="page-link" href="?page={{ $i }}">{{ $i }}</a>
                            </li>
                        @endfor
                        
                        @if($pagination['page'] < $pagination['totalPages'])
                            <li class="page-item">
                                <a class="page-link" href="?page={{ $pagination['page'] + 1 }}">Next</a>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        @endif
    </div>

    <!-- Status Update Form -->
    <form id="statusForm" method="POST" style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="status" id="statusInput">
    </form>
@endsection

@push('scripts')
<script>
    function updateStatus(visitId, status) {
        if (confirm(`Are you sure you want to ${status.toLowerCase()} this visit?`)) {
            const form = document.getElementById('statusForm');
            const statusInput = document.getElementById('statusInput');
            
            form.action = `{{ route('admin.inspectionVisit.updateStatus', ':id') }}`.replace(':id', visitId);
            statusInput.value = status;
            form.submit();
        }
    }
</script>
@endpush