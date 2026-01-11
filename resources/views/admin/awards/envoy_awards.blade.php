@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Envoy Awards Assignments') }}</h4>
        <a href="{{ route('admin.award.assign.form') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> {{ __('Assign Award') }}
        </a>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-header">
                <form action="{{ route('admin.award.envoy-awards') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Search by name or phone...') }}" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary">{{ __('Search') }}</button>
                        <a href="{{ route('admin.award.envoy-awards') }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border table-responsive-md">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Envoy') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Award') }}</th>
                                <th>{{ __('Reason') }}</th>
                                <th>{{ __('Assigned At') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['data'] as $assignment)
                                <tr>
                                    <td>{{ $assignment['id'] }}</td>
                                    <td>{{ $assignment['envoy']['name'] ?? 'N/A' }}</td>
                                    <td>{{ $assignment['envoy']['phone'] ?? 'N/A' }}</td>
                                    <td>{{ $assignment['award']['title'] ?? 'N/A' }}</td>
                                    <td>{{ $assignment['reason'] ?? 'N/A' }}</td>
                                    <td>{{ date('Y-m-d', strtotime($assignment['created_at'])) }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                onclick="openEditModal({{ json_encode($assignment) }})">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="deleteAssignment({{ $assignment['id'] }})">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="7">{{ __('No assignments found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        @for($i = 1; $i <= ($data['totalPages'] ?? 1); $i++)
                            <li class="page-item {{ $data['page'] == $i ? 'active' : '' }}">
                                <a class="page-link" href="{{ route('admin.award.envoy-awards', array_merge(request()->query(), ['page' => $i])) }}">{{ $i }}</a>
                            </li>
                        @endfor
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Edit Assignment Modal -->
    <div class="modal fade" id="editAssignment" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Update Assignment') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Envoy') }}</label>
                            <input type="text" class="form-control" id="editEnvoyName" disabled>
                            <input type="hidden" name="envoy_id" id="editEnvoyId">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Select Award') }} *</label>
                            <select class="form-control" id="editAwardId" name="award_id" required>
                                <!-- Awards will be populated via JS or passed to view -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Reason') }}</label>
                            <textarea class="form-control" id="editReason" name="reason" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
<script>
    function openEditModal(assignment) {
        $('#editEnvoyName').val(assignment.envoy ? `${assignment.envoy.name} (${assignment.envoy.phone})` : 'N/A');
        $('#editEnvoyId').val(assignment.envoy_id);
        $('#editReason').val(assignment.reason || '');
        
        // Populate awards dropdown
        let awards = @json($awards);
        let awardSelect = $('#editAwardId');
        awardSelect.empty();
        awards.forEach(award => {
            let selected = award.id == assignment.award_id ? 'selected' : '';
            awardSelect.append(`<option value="${award.id}" ${selected}>${award.title}</option>`);
        });
        
        $('#editForm').attr('action', `{{ route('admin.award.assignment.update', ':id') }}`.replace(':id', assignment.id));
        $('#editAssignment').modal('show');
    }
    function deleteAssignment(id) {
        if (confirm('Are you sure you want to delete this assignment?')) {
            $('#deleteForm').attr('action', `{{ route('admin.award.assignment.destroy', ':id') }}`.replace(':id', id));
            $('#deleteForm').submit();
        }
    }
</script>
@endpush
