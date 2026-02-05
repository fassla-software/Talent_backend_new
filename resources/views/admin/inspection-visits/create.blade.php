@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Schedule Inspection Visit') }}</h4>
        <a href="{{ route('admin.inspectionVisit.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> {{ __('Back') }}
        </a>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.inspectionVisit.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <!-- Envoy Selection -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Select Envoy') }} <span class="text-danger">*</span></label>
                            <select name="inspector_id" class="form-select select2" required>
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
                            <select name="trader_id" class="form-select select2">
                                <option value="">{{ __('Search for trader...') }}</option>
                                @foreach($traders as $trader)
                                    <option value="{{ $trader->id }}" {{ old('trader_id') == $trader->id ? 'selected' : '' }}>
                                        {{ $trader->user?->name ?? 'N/A' }} ({{ $trader->city }} - {{ $trader->area }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 d-none" id="plumber_selection">
                            <label class="form-label">{{ __('Select Plumber') }} <span class="text-danger">*</span></label>
                            <select name="plumber_id" class="form-select select2">
                                <option value="">{{ __('Search for plumber...') }}</option>
                                @foreach($plumbers as $plumber)
                                    <option value="{{ $plumber->id }}" {{ old('plumber_id') == $plumber->id ? 'selected' : '' }}>
                                        {{ $plumber->user?->name ?? 'N/A' }} ({{ $plumber->city }} - {{ $plumber->area }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Scheduled At -->
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Scheduled At') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}" required>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label class="form-label">{{ __('Notes') }}</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('Optional notes...') }}">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Submit -->
                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-primary px-5">
                                {{ __('Schedule Visit') }}
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
        // Toggle client selection based on radio button
        $('input[name="client_type"]').change(function() {
            if ($(this).val() === 'trader') {
                $('#trader_selection').removeClass('d-none');
                $('#plumber_selection').addClass('d-none');
                $('select[name="trader_id"]').prop('required', true);
                $('select[name="plumber_id"]').prop('required', false).val(null).trigger('change.select2');
            } else {
                $('#plumber_selection').removeClass('d-none');
                $('#trader_selection').addClass('d-none');
                $('select[name="plumber_id"]').prop('required', true);
                $('select[name="trader_id"]').prop('required', false).val(null).trigger('change.select2');
            }
        });

        // Trigger change on load if plumber is selected (for old input)
        if ($('input[name="client_type"]:checked').val() === 'plumber') {
            $('input[name="client_type"]:checked').trigger('change');
        } else {
            $('select[name="trader_id"]').prop('required', true);
        }
    });
</script>
@endpush
