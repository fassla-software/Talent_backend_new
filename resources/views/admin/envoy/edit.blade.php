@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4>{{ __('Edit Envoy') }}</h4>
        <a href="{{ route('admin.envoy.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> {{ __('Back') }}
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.envoy.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <!-- Basic Info -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">{{ __('Phone') }} <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" required>
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Envoy Specific Settings -->
                    <div class="col-md-12 mb-3">
                        <h5 class="mt-3">{{ __('Envoy Settings') }}</h5>
                        <hr>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="region" class="form-label">{{ __('Region') }}</label>
                        <select name="region" id="region" class="form-control @error('region') is-invalid @enderror">
                            <option value="">{{ __('Select Region') }}</option>
                            @foreach(['القاهرة', 'الجيزة', 'القليوبية', 'البحيرة', 'دمياط', 'الدقهلية', 'كفر الشيخ', 'الغربية', 'المنوفية', 'الشرقية', 'بورسعيد', 'الإسماعيلية', 'بني سويف', 'الفيوم', 'المنيا', 'أسيوط', 'سوهاج', 'قنا'] as $city)
                                <option value="{{ $city }}" {{ old('region', $user->envoySetting->region ?? '') == $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                        @error('region') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

{{-- 
                    <div class="col-md-6 mb-3">
                        <label for="weight" class="form-label">{{ __('Weight') }}</label>
                        <input type="number" step="0.01" name="weight" id="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight', $user->envoySetting->weight ?? 0) }}">
                        @error('weight') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
--}}

                    <div class="col-md-6 mb-3">
                        <label for="target_sales" class="form-label">{{ __('Target Sales') }}</label>
                        <input type="number" step="0.01" name="target_sales" id="target_sales" class="form-control @error('target_sales') is-invalid @enderror" value="{{ old('target_sales', $user->envoySetting->target_sales ?? 0) }}">
                        @error('target_sales') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="weight_sales" class="form-label">{{ __('Weight Sales (%)') }}</label>
                        <input type="number" name="weight_sales" id="weight_sales" class="form-control weight-input @error('weight_sales') is-invalid @enderror" value="{{ old('weight_sales', $user->envoySetting->weight_sales ?? 0) }}">
                        @error('weight_sales') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="target_visits" class="form-label">{{ __('Target Visits') }}</label>
                        <input type="number" name="target_visits" id="target_visits" class="form-control @error('target_visits') is-invalid @enderror" value="{{ old('target_visits', $user->envoySetting->target_visits ?? 0) }}">
                        @error('target_visits') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="weight_visits" class="form-label">{{ __('Weight Visits (%)') }}</label>
                        <input type="number" name="weight_visits" id="weight_visits" class="form-control weight-input @error('weight_visits') is-invalid @enderror" value="{{ old('weight_visits', $user->envoySetting->weight_visits ?? 0) }}">
                        @error('weight_visits') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="target_retention_rate" class="form-label">{{ __('Target Retention Rate (%)') }}</label>
                        <input type="number" step="0.01" name="target_retention_rate" id="target_retention_rate" class="form-control @error('target_retention_rate') is-invalid @enderror" value="{{ old('target_retention_rate', $user->envoySetting->target_retention_rate ?? 0) }}">
                        @error('target_retention_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="weight_retention_rate" class="form-label">{{ __('Weight Retention (%)') }}</label>
                        <input type="number" name="weight_retention_rate" id="weight_retention_rate" class="form-control weight-input @error('weight_retention_rate') is-invalid @enderror" value="{{ old('weight_retention_rate', $user->envoySetting->weight_retention_rate ?? 0) }}">
                        @error('weight_retention_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="target_conversion_rate" class="form-label">{{ __('Target Conversion Rate (%)') }}</label>
                        <input type="number" step="0.01" name="target_conversion_rate" id="target_conversion_rate" class="form-control @error('target_conversion_rate') is-invalid @enderror" value="{{ old('target_conversion_rate', $user->envoySetting->target_conversion_rate ?? 0) }}">
                        @error('target_conversion_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="weight_conversion_rate" class="form-label">{{ __('Weight Conversion (%)') }}</label>
                        <input type="number" name="weight_conversion_rate" id="weight_conversion_rate" class="form-control weight-input @error('weight_conversion_rate') is-invalid @enderror" value="{{ old('weight_conversion_rate', $user->envoySetting->weight_conversion_rate ?? 0) }}">
                        @error('weight_conversion_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="target_inspection_requests" class="form-label">{{ __('Target Inspection Requests') }}</label>
                        <input type="number" name="target_inspection_requests" id="target_inspection_requests" class="form-control @error('target_inspection_requests') is-invalid @enderror" value="{{ old('target_inspection_requests', $user->envoySetting->target_inspection_requests ?? 0) }}">
                        @error('target_inspection_requests') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="weight_inspection_requests" class="form-label">{{ __('Weight Inspection Requests (%)') }}</label>
                        <input type="number" name="weight_inspection_requests" id="weight_inspection_requests" class="form-control weight-input @error('weight_inspection_requests') is-invalid @enderror" value="{{ old('weight_inspection_requests', $user->envoySetting->weight_inspection_requests ?? 0) }}">
                        @error('weight_inspection_requests') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <div id="weight-sum-alert" class="alert alert-info py-2">
                            {{ __('Total Weight: ') }} <span id="weight-total">0</span>%
                        </div>
                        @if($errors->has('weights_sum'))
                            <div class="text-danger small mt-1">{{ $errors->first('weights_sum') }}</div>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="salary" class="form-label">{{ __('Salary') }}</label>
                        <input type="number" step="0.01" name="salary" id="salary" class="form-control @error('salary') is-invalid @enderror" value="{{ old('salary', $user->envoySetting->salary ?? 0) }}">
                        @error('salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="incentives" class="form-label">{{ __('Incentives') }}</label>
                        <input type="number" step="0.01" name="incentives" id="incentives" class="form-control @error('incentives') is-invalid @enderror" value="{{ old('incentives', $user->envoySetting->incentives ?? 0) }}">
                        @error('incentives') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary">{{ __('Update Envoy') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const weightInputs = document.querySelectorAll('.weight-input');
        const weightTotal = document.getElementById('weight-total');
        const alertBox = document.getElementById('weight-sum-alert');

        function calculateSum() {
            let sum = 0;
            weightInputs.forEach(input => {
                sum += parseInt(input.value) || 0;
            });
            weightTotal.textContent = sum;
            
            if (sum === 100) {
                alertBox.className = 'alert alert-success py-2';
            } else {
                alertBox.className = 'alert alert-warning py-2';
            }
        }

        weightInputs.forEach(input => {
            input.addEventListener('input', calculateSum);
        });

        calculateSum();
    });
</script>
@endpush
@endsection
