@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Account Deletion Requests</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User Name</th>
                <th>Requested At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
            <tr>
                <td>{{ $request->id }}</td>
                <td>{{ $request->user->name }}</td>
                <td>{{ $request->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
