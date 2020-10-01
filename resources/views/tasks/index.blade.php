@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>
                        Tasks
                        <a href="{{ route('tasks.create')}}" class="btn btn-primary float-right">Create Task</a>
                    </h3>
                </div>

                <div class="card-body">
                    @if (session('status'))
                    <div id="status">
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    </div>
                    @endif

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Last Run</th>
                                <th>Average Runtime</th>
                                <th>Next Run</th>
                                <th>Status</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                            <tr class="table-{{ $task->is_active ? 'success' : 'danger'}}">
                                <td><a href="{{ route('tasks.edit',$task->id)}}">{{ $task->description }}</a></td>
                                <td>{{ $task->last_run }}</td>
                                <td>{{ $task->average_runtime }} seconds</td>
                                <td>{{ $task->next_run }}</td>
                                <td class="text-center">
                                    <form id="toggle-form-{{$task->id}}" method="post" action="{{ route('tasks.toggle',$task->id)}}">
                                        {{ csrf_field()}}
                                        {{method_field('PUT')}}
                            <input  type="checkbox" class="form-check-input" onchange="getElementById('toggle-form-{{$task->id}}').submit();" {{ $task->is_active ? 'checked' : '' }}>
                                    </form>
                                </td>
                                <td>
                                    <form id="delete-form-{{$task->id}}" method="post" action="{{ route('tasks.destroy',$task->id)}}">
                                        {{ csrf_field()}}
                                        {{method_field('DELETE')}}
                                        <button type="button" class="btn btn-sm btn-danger" onclick="if(confirm('Are you sure?')) getElementById('delete-form-{{$task->id}}').submit();">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>  
<script>
    setTimeout(() => {
        const elem = document.getElementById("status");
        elem.parentNode.removeChild(elem);
    }, 4000);
</script>
@endsection
