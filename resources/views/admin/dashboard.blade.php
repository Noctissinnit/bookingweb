@extends('layouts.app')

@section('content')
    <table class="container table table-bordered">
        <thead>
            <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Email</th>
                <th>NIS</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->nis }}</td>
                <td>{{ ucfirst($user->role) }}</td>
                <td>
                    <button class="btn btn-warning">Edit</button>
                    <button class="btn btn-danger">Hapus</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection