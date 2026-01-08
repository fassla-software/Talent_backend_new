@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center mb-4">Contact Messages</h2>

    <div class="card shadow p-4">
        <table class="table table-bordered table-hover text-center">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    
                    <th>Message</th>
                    <th>Received At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($messages as $index => $message)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $message->name }}</td>
                        <td>{{ $message->phone }}</td>
                       
                       
                        <td>
                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#messageModal{{ $message->id }}">
                                View Message
                            </button>
                        </td>
                        <td>{{ $message->created_at->format('d M Y, h:i A') }}</td>
                    </tr>

                    <!-- Modal for Full Message -->
                    <div class="modal fade" id="messageModal{{ $message->id }}" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="messageModalLabel">Message from {{ $message->name }}</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>{{ $message->message }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                @empty
                    <tr>
                        <td colspan="8" class="text-muted">No messages found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-3 d-flex justify-content-center">
            {{ $messages->links() }} <!-- Pagination -->
        </div>
    </div>
</div>
@endsection
