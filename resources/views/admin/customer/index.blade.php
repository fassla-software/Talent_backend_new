@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center justify-content-between">
        <h4>{{ __('All Customers') }}</h4>
        <a href="{{ route('admin.customer.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> {{ __('Add Customer') }}
        </a>
    </div>

    <div class="container-fluid mt-3">

        <div class="mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border table-responsive-lg">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('SL') }}.</th>
                                <th>{{ __('Profile') }}</th>
                                <th style="min-width: 150px">{{ __('Name') }}</th>
                                <th style="min-width: 100px">{{ __('Phone') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th class="text-center">{{ __('Gender') }}</th>
                                <th class="text-center">{{ __('Date of Birth') }}</th>

                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        @forelse($customers as $key => $customer)
                            <tr>
                                <td class="text-center">{{ ++$key }}</td>

                                <td>
                                    <img src="{{ $customer->thumbnail }}" width="50">
                                </td>

                                <td>{{ Str::limit($customer->fullName, 50, '...') }}</td>

                                <td>
                                    {{ $customer->phone ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ $customer->email ?? 'N/A' }}
                                </td>

                                <td class="text-center">
                                    {{ $customer->gender ?? 'N/A' }}
                                </td>

                                <td class="text-center">
                                {{ $customer->date_of_birth ?? 'N/A' }}
                                </td>

                                <td class="text-center">
                                    <div class="d-flex gap-3 justify-content-center">
                                        <a href="{{ route('admin.customer.show', $customer->id) }}"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.customer.edit', $customer->id) }}"
                                            class="btn btn-outline-info btn-sm">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <button onclick="confirmDeny('{{ $customer->id }}')"
                                            class="btn btn-outline-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="100%">{{ __('No Data Found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="my-3">
            {{ $customers->withQueryString()->links() }}
        </div>
        
        <form id="deleteForm" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

    </div>
@endsection

@push('scripts')
    <script>
        const confirmDeny = (id) => {
            const form = document.getElementById('deleteForm');
            form.action = `{{ route('admin.customer.destroy', ':id') }}`.replace(':id', id);
            Swal.fire({
                title: "Are you sure?",
                text: "You want to delete this customer! If you confirm, it will be deleted permanently.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ef4444",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Yes, Delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
@endpush

