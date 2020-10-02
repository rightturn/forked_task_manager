@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-success ">
                <div class="card-header text-white bg-success " > <b>Create New Task</b></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('tasks.store')}}" method="post">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" name="description" id="description" required>
                        </div>
                        <div class="form-group">
                            <label for="command">Command</label>
                            <input type="text" class="form-control" name="command" id="command" required>
                        </div>
                        <div class="form-group">
                            <label for="expression">Cron Expression</label>
                            <input required type="text" class="form-control" name="expression" id="expression" value="* * * * *">
                        </div>
                        <div class="form-group">
                            <label for="notification_email">Email Address</label>
                            <input required type="email" class="form-control" name="notification_email" id="notification_email">
                        </div>
                        <div class="form-check">
                            <input  type="checkbox" class="form-check-input" name="dont_overlap" id="dont_overlap" value="1">
                            <label  for="dont_overlap" class="form-check-label">Don't Overlap</label>
                        </div>
                        <div class="form-check">
                            <input  type="checkbox" class="form-check-input" name="run_in_maintenance" id="run_in_maintenance" value="1">
                            <label for="run_in_maintenance" class="form-check-label">Run in maintenance</label>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-success">Create Task</button>
                                <a href="{{route('tasks.index')}}" class="btn btn-outline-danger">Cancel</a>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
