@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Awards Management') }}</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAward">
            <i class="fa-solid fa-plus"></i> {{ __('Add Award') }}
        </button>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border table-responsive-md">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Created') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($awards as $award)
                                <tr>
                                    <td>{{ $award['id'] }}</td>
                                    <td>{{ $award['title'] }}</td>
                                    <td>{{ $award['description'] ?? 'N/A' }}</td>
                                    <td>{{ date('Y-m-d', strtotime($award['created_at'])) }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                onclick="openEditModal({{ json_encode($award) }})">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="deleteAward({{ $award['id'] }})">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="5">{{ __('No awards found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Award Modal -->
    <div class="modal fade" id="createAward" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.award.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Add Award') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Title') }} *</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Award Modal -->
    <div class="modal fade" id="editAward" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="" id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Update Award') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Title') }} *</label>
                            <input type="text" class="form-control" id="editTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
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
    function openEditModal(award) {
        $('#editTitle').val(award.title);
        $('#editDescription').val(award.description || '');
        $('#editForm').attr('action', `{{ route('admin.award.update', ':id') }}`.replace(':id', award.id));
        $('#editAward').modal('show');
    }

    function deleteAward(id) {
        if (confirm('Are you sure you want to delete this award?')) {
            $('#deleteForm').attr('action', `{{ route('admin.award.destroy', ':id') }}`.replace(':id', id));
            $('#deleteForm').submit();
        }
    }
</script>
@endpush
