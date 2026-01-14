@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Assign Award to Envoy') }}</h4>
        <a href="{{ route('admin.award.envoy-awards') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> {{ __('Back to List') }}
        </a>
    </div>

    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.award.assign') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Select Envoy') }} *</label>
                            <select class="form-control select2" name="envoy_id" required>
                                <option value="">{{ __('Select Envoy') }}</option>
                                @foreach($envoys as $envoy)
                                    <option value="{{ $envoy['id'] }}">
                                        {{ $envoy['name'] }} ({{ $envoy['phone'] }}) - ID: {{ $envoy['id'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Select Award') }} *</label>
                            <select class="form-control select2" name="award_id" required>
                                <option value="">{{ __('Select Award') }}</option>
                                @foreach($awards as $award)
                                    <option value="{{ $award['id'] }}">
                                        {{ $award['title'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">{{ __('Reason') }}</label>
                            <textarea class="form-control" name="reason" rows="3" placeholder="{{ __('Enter reason for this award...') }}"></textarea>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">{{ __('Assign Award') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "{{ __('Select an option') }}",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
