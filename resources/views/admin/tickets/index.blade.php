@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Tickets Management') }}</h4>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border table-responsive-md">
                        <thead>
                            <tr>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Client') }}</th>
                                <th>{{ __('Inspector') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Priority') }}</th>
                                <th>{{ __('Due Date') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket['code'] }}</td>
                                    <td>{{ $ticket['title'] }}</td>
                                    <td>{{ $ticket['client']['name'] ?? 'N/A' }}</td>
                                    <td>{{ $ticket['inspector']['name'] ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $ticket['status'] === 'OPEN' ? 'warning' : ($ticket['status'] === 'IN_PROGRESS' ? 'info' : 'success') }}">
                                            {{ $ticket['status'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $ticket['priority'] === 'HIGH' ? 'danger' : ($ticket['priority'] === 'AVERAGE' ? 'warning' : 'secondary') }}">
                                            {{ $ticket['priority'] }}
                                        </span>
                                    </td>
                                    <td>{{ $ticket['due_date'] ? date('Y-m-d', strtotime($ticket['due_date'])) : 'N/A' }}</td>
                                    <td>{{ date('Y-m-d', strtotime($ticket['createdAt'])) }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                onclick="openEditModal({{ json_encode($ticket) }})">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="deleteTicket({{ $ticket['id'] }})">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="9">{{ __('No tickets found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Ticket Modal -->
    <form action="" id="editForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal fade" id="editTicket" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Update Ticket') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select class="form-control" id="editStatus" name="status">
                                <option value="OPEN">Open</option>
                                <option value="IN_PROGRESS">In Progress</option>
                                <option value="CLOSED">Closed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Priority') }}</label>
                            <select class="form-control" id="editPriority" name="priority">
                                <option value="LOW">Low</option>
                                <option value="AVERAGE">Average</option>
                                <option value="HIGH">High</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Due Date') }}</label>
                            <input type="date" class="form-control" id="editDueDate" name="due_date">
                        </div>
                        <div class="mb-3" id="noteSection" style="display: none;">
                            <label class="form-label">{{ __('Note') }} *</label>
                            <textarea class="form-control" id="editNote" name="note" rows="3"></textarea>
                        </div>
                        <div class="mb-3" id="closeReasonSection" style="display: none;">
                            <label class="form-label">{{ __('Close Reason') }} *</label>
                            <textarea class="form-control" id="editCloseReason" name="close_reason" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
<script>
    function openEditModal(ticket) {
        $('#editStatus').val(ticket.status);
        $('#editPriority').val(ticket.priority);
        $('#editDueDate').val(ticket.due_date ? ticket.due_date.split('T')[0] : '');
        $('#editNote').val(ticket.note || '');
        $('#editCloseReason').val(ticket.close_reason || '');
        
        $('#editForm').attr('action', `{{ route('admin.ticket.update', ':id') }}`.replace(':id', ticket.id));
        
        // Show/hide note and close reason fields based on status
        toggleRequiredFields(ticket.status);
        
        $('#editTicket').modal('show');
    }

    // Show/hide required fields when status changes
    $('#editStatus').on('change', function() {
        toggleRequiredFields($(this).val());
    });

    function toggleRequiredFields(status) {
        if (status === 'CLOSED') {
            $('#noteSection').show();
            $('#closeReasonSection').show();
            $('#editNote').attr('required', true);
            $('#editCloseReason').attr('required', true);
        } else {
            $('#noteSection').hide();
            $('#closeReasonSection').hide();
            $('#editNote').removeAttr('required');
            $('#editCloseReason').removeAttr('required');
        }
    }

    function deleteTicket(id) {
        if (confirm('Are you sure you want to delete this ticket?')) {
            $('#deleteForm').attr('action', `{{ route('admin.ticket.destroy', ':id') }}`.replace(':id', id));
            $('#deleteForm').submit();
        }
    }
</script>
@endpush