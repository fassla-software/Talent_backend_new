@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Report Dropdown Options') }}</h4>
        
        <button type="button" data-bs-toggle="modal" data-bs-target="#createOption" class="btn py-2 btn-primary">
            <i class="fa fa-plus-circle"></i>
            {{ __('Add New Option') }}
        </button>
    </div>

    <div class="container-fluid mt-3">
        @foreach($dropdowns as $type => $options)
            <div class="mb-4 card">
                <div class="card-body">
                    <div class="cardTitleBox">
                        <h5 class="card-title chartTitle">
                            {{ __(ucwords(str_replace('_', ' ', $type))) }}
                            @if($type === 'sales_classification')
                                <span class="badge bg-warning">Static</span>
                            @endif
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table border table-responsive-md">
                            <thead>
                                <tr>
                                    <th>{{ __('Key') }}</th>
                                    <th>{{ __('English Value') }}</th>
                                    <th>{{ __('Arabic Value') }}</th>
                                    @if($type !== 'sales_classification')
                                        <th class="text-center">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($options as $option)
                                    <tr>
                                        <td>{{ $option['key'] }}</td>
                                        <td>{{ $option['value_en'] }}</td>
                                        <td>{{ $option['value_ar'] ?? '-' }}</td>
                                        @if($type !== 'sales_classification')
                                            <td class="text-center">
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                                        onclick="openEditModal({{ json_encode($option) }})">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        onclick="deleteOption({{ $option['id'] }})">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="4">{{ __('No options found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Create Option Modal -->
    <form action="{{ route('admin.reportDropdown.store') }}" method="POST">
        @csrf
        <div class="modal fade" id="createOption" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Add New Option') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Dropdown Type') }} *</label>
                            <select name="dropdown_type" class="form-control" required>
                                <option value="">Select Type</option>
                                @foreach($types as $type)
                                    @if($type !== 'sales_classification')
                                        <option value="{{ $type }}">{{ ucwords(str_replace('_', ' ', $type)) }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Key') }} *</label>
                            <input type="text" class="form-control" name="key" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('English Value') }} *</label>
                            <input type="text" class="form-control" name="value_en" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Arabic Value') }}</label>
                            <input type="text" class="form-control" name="value_ar">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Add Option') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Edit Option Modal -->
    <form action="" id="editForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal fade" id="editOption" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Edit Option') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Key') }} *</label>
                            <input type="text" class="form-control" id="editKey" name="key" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('English Value') }} *</label>
                            <input type="text" class="form-control" id="editValueEn" name="value_en" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Arabic Value') }}</label>
                            <input type="text" class="form-control" id="editValueAr" name="value_ar">
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
    function openEditModal(option) {
        $('#editKey').val(option.key);
        $('#editValueEn').val(option.value_en);
        $('#editValueAr').val(option.value_ar || '');
        $('#editForm').attr('action', `{{ route('admin.reportDropdown.update', ':id') }}`.replace(':id', option.id));
        $('#editOption').modal('show');
    }

    function deleteOption(id) {
        if (confirm('Are you sure you want to delete this option?')) {
            $('#deleteForm').attr('action', `{{ route('admin.reportDropdown.destroy', ':id') }}`.replace(':id', id));
            $('#deleteForm').submit();
        }
    }
</script>
@endpush