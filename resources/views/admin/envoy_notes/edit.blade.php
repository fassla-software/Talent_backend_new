@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4>{{ __('Edit Envoy Note') }}</h4>
        <a href="{{ route('admin.envoy-notes.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> {{ __('Back') }}
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.envoy-notes.update', $envoy_note->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">

                    <!-- Client Selection -->
                    <div class="col-md-6">
                        <label for="client_id" class="form-label">{{ __('Client') }} <span class="text-danger">*</span></label>
                        <select name="client_id" id="client_id" class="form-select select2 @error('client_id') is-invalid @enderror" required>
                            <option value="">{{ __('Search for client (Name/Phone)...') }}</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $envoy_note->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->fullName }} ({{ $client->phone }})
                                </option>
                            @endforeach
                        </select>
                        @error('client_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="content" class="form-label">{{ __('Note Content') }} <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" rows="6" class="form-control @error('content') is-invalid @enderror" required placeholder="{{ __('Enter note content here...') }}">{{ old('content', $envoy_note->content) }}</textarea>
                        @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary px-5">{{ __('Update Note') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });
    });
</script>
@endpush
