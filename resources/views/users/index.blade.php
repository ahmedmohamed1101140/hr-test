@extends('layouts.default')

@section('css')

    <link rel="stylesheet" href="{{ asset('css/bootstrapdatatable.css')}}" >

@endsection

@section('content')

<table id="example" class="table table-striped table-bordered" style="width:100%">

        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>User Name</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

            @foreach($users as $user)
                <tr>
                    <th> <a href="{{ route('user.show', $user->user_id ) }}">{{ $user->first_name }}</a></th>
                    <th> {{ $user->last_name }} </th>
                    <th> {{ $user->user_name }} </th>

                    <th>
                        <ul style="list-style:none;">

                            <form class="float-left" action="{{ route('user.update', $user->user_id ) }}" method="post">
                                @csrf
                                <li style="margin-right:15px;"><button class="btn-primary" type="submit">
                                    Edit</button></li>

                            </form>

                            <form method="POST" action="{{ route('user.destroy', $user->user_name) }}">
                                @csrf
                                {{ method_field('DELETE') }}
                            <li class="float-left"><button class="btn-danger" type="submit">
                                Disable </button></li>
                            </form>

                        </ul>
                    </th>
                </tr>
            @endforeach
        </tbody>

    </table>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#example').DataTable();
    } );
</script>
<script src="{{ asset('js/jquerydatatable.js') }}" ></script>
<script src="{{ asset('js/bootstrapdatatable.js') }}" ></script>
@endsection