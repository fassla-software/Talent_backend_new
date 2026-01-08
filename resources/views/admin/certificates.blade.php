@extends('layouts.app')

@section('title', 'Certificates')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4">Certificates</h1>

        <!-- Search and Filter Form -->
        <form method="GET" action="{{ route('certificates.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search (Plumber Name, Phone, Certificate ID)" value="{{ request()->get('search') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_from" class="form-control" value="{{ request()->get('date_from') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_to" class="form-control" value="{{ request()->get('date_to') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        @if ($certificates->isEmpty())
            <div class="alert alert-info">
                No certificates found.
            </div>
        @else
            <form id="downloadForm">
                @csrf
                <table class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                            <th>ID</th>
                            <th>Certificate ID</th>
                            <th>Plumber Name</th>
                            <th>Phone</th>
                            <th>File</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($certificates as $certificate)
                            <tr>
                                <td><input type="checkbox" class="certificate-checkbox" value="{{ $certificate->file_url }}"></td>
                                <td>{{ $certificate->id ?? 'N/A' }}</td>
                                <td>{{ $certificate->certificate_id ?? 'N/A' }}</td>
                                <td>{{ $certificate->plumber->name ?? 'N/A' }}</td>
                                <td>{{ $certificate->user_phone ?? 'N/A' }}</td>
                                <td>
                                    @if ($certificate->file_name)
                                        <a href="{{ $certificate->file_url }}" class="btn btn-primary btn-sm" download>
                                            <i class="fa-solid fa-download"></i> Download
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($certificate->created_at ?? now())->format('d-m-Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" id="bulkDownloadButton" class="btn btn-success">Download Selected Certificates</button>
                </div>
            </form>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-center mt-4">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.certificate-checkbox');
            const bulkDownloadButton = document.getElementById('bulkDownloadButton');

            // Toggle all checkboxes
            selectAll.addEventListener('change', () => {
                checkboxes.forEach(checkbox => checkbox.checked = selectAll.checked);
            });

            // Bulk download handler
            bulkDownloadButton.addEventListener('click', function () {
                const selectedLinks = Array.from(checkboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);

                if (selectedLinks.length === 0) {
                    alert('Please select at least one certificate to download.');
                    return;
                }

                // Automatically download each selected file
                selectedLinks.forEach(link => {
                    if (link) {
                        const a = document.createElement('a');
                        a.href = link; // Download link
                        a.download = link.split('/').pop(); // Use the last part as the filename
                        a.style.display = 'none'; // Hide the <a> element
                        document.body.appendChild(a);
                        a.click(); // Trigger the download
                        document.body.removeChild(a); // Remove the <a> element
                    }
                });
            });
        });
    </script>
@endsection
