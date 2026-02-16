@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Create New Ticket') }}</h4>
        <a href="{{ route('admin.ticket.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> {{ __('Back') }}
        </a>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.ticket.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <!-- Inspector Selection -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Select Inspector (Envoy)') }} <span class="text-danger">*</span></label>
                            <select name="inspector_id" class="form-control select2" required style="width: 100%">
                                <option value="">{{ __('Search for envoy...') }}</option>
                                @foreach($envoys as $envoy)
                                    <option value="{{ $envoy->id }}" {{ old('inspector_id') == $envoy->id ? 'selected' : '' }}>
                                        {{ $envoy->name }} ({{ $envoy->phone }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Client Type Toggle -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Client Type') }} <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 pt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="client_type" id="type_trader" value="trader" {{ old('client_type', 'trader') == 'trader' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_trader">{{ __('Trader') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="client_type" id="type_plumber" value="plumber" {{ old('client_type') == 'plumber' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_plumber">{{ __('Plumber') }}</label>
                                </div>
                            </div>
                        </div>

                        <!-- Client Selection -->
                        <div class="col-md-6" id="trader_selection">
                            <label class="form-label">{{ __('Select Trader') }} <span class="text-danger">*</span></label>
                            <select name="trader_phone_select" id="trader_id" class="form-control select2" style="width: 100%">
                                <option value="">{{ __('Search for trader...') }}</option>
                                @foreach($traders as $trader)
                                    <option value="{{ $trader->user?->phone }}" {{ old('client_phone') == $trader->user?->phone ? 'selected' : '' }}>
                                        {{ $trader->user?->name ?? 'N/A' }} ({{ $trader->user?->phone }}) - {{ $trader->city }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 d-none" id="plumber_selection">
                            <label class="form-label">{{ __('Select Plumber') }} <span class="text-danger">*</span></label>
                            <select name="plumber_phone_select" id="plumber_id" class="form-control select2" style="width: 100%">
                                <option value="">{{ __('Search for plumber...') }}</option>
                                @foreach($plumbers as $plumber)
                                    <option value="{{ $plumber->user?->phone }}" {{ old('client_phone') == $plumber->user?->phone ? 'selected' : '' }}>
                                        {{ $plumber->user?->name ?? 'N/A' }} ({{ $plumber->user?->phone }}) - {{ $plumber->city }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Hidden client_phone field -->
                        <input type="hidden" name="client_phone" id="client_phone" value="{{ old('client_phone') }}">

                        <!-- Title -->
                        <div class="col-md-12">
                            <label class="form-label">{{ __('Ticket Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="{{ __('Enter ticket title') }}" required>
                        </div>

                        <!-- Issue -->
                        <div class="col-12">
                            <label class="form-label">{{ __('Issue Description') }} <span class="text-danger">*</span></label>
                            <textarea name="issue" class="form-control" rows="4" placeholder="{{ __('Describe the issue in detail...') }}" required>{{ old('issue') }}</textarea>
                        </div>

                        <!-- Priority -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Priority') }}</label>
                            <select name="priority" class="form-select">
                                <option value="LOW" {{ old('priority') == 'LOW' ? 'selected' : '' }}>{{ __('Low') }}</option>
                                <option value="AVERAGE" {{ old('priority', 'AVERAGE') == 'AVERAGE' ? 'selected' : '' }}>{{ __('Average') }}</option>
                                <option value="HIGH" {{ old('priority') == 'HIGH' ? 'selected' : '' }}>{{ __('High') }}</option>
                            </select>
                        </div>

                        <!-- Due Date -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Due Date') }}</label>
                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                        </div>

                        <!-- Submit -->
                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-primary px-5">
                                {{ __('Create Ticket') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Force select2 initialization with 100% width to prevent 0px width in hidden containers
        $('.select2').each(function() {
            $(this).select2({
                width: '100%'
            });
        });

        // Toggle client selection based on radio button
        $('input[name="client_type"]').change(function() {
            const type = $(this).val();
            if (type === 'trader') {
                $('#trader_selection').removeClass('d-none');
                $('#plumber_selection').addClass('d-none');
                $('#trader_id').prop('required', true);
                $('#plumber_id').prop('required', false);
            } else {
                $('#plumber_selection').removeClass('d-none');
                $('#trader_selection').addClass('d-none');
                $('#plumber_id').prop('required', true);
                $('#trader_id').prop('required', false);
            }
            updateClientPhone();
        });

        function updateClientPhone() {
            const type = $('input[name="client_type"]:checked').val();
            const phone = type === 'trader' ? $('#trader_id').val() : $('#plumber_id').val();
            $('#client_phone').val(phone);
        }

        // Update hidden client_phone when selection changes
        $('.select2').on('change', function() {
            updateClientPhone();
        });

        // Trigger change on load to set initial state correctly
        $('input[name="client_type"]:checked').trigger('change');
    });
</script>
@endpush
