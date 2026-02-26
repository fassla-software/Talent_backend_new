@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between mb-4">
        <h4>{{ __('Envoy Notes Management') }}</h4>
        <a href="{{ route('admin.envoy-notes.create') }}" class="btn btn-primary py-2.5">
            <i class="fa fa-plus-circle"></i>
            {{ __('Add Note') }}
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.envoy-notes.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <label for="client_id" class="form-label">{{ __('Client') }}</label>
                        <select name="client_id" id="client_id" class="form-select select2">
                            <option value="">{{ __('All Clients') }}</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->fullName }} ({{ $client->phone }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">{{ __('Filter') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Notes Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table border table-responsive-lg">
                    <thead>
                        <tr>
                            <th class="text-center">{{ __('SL') }}.</th>
                            <th>{{ __('Client') }}</th>
                            <th>{{ __('Content') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notes as $key => $note)
                            <tr>
                                <td class="text-center">{{ $notes->firstItem() + $key }}</td>
                                <td>{{ $note->client->fullName ?? 'N/A' }}</td>
                                <td>{{ Str::limit($note->content, 100, '...') }}</td>
                                <td>{{ $note->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('admin.envoy-notes.edit', $note->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.envoy-notes.destroy', $note->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="5">{{ __('No Notes Found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="my-3">
        {{ $notes->withQueryString()->links() }}
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
