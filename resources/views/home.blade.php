@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}

                        </div>
                    @endif

                    {{ __('You are logged in!') }}

                        <form method="post" action="{{ route('sendmail') }}">
                            @csrf
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" class="form-control" value="{{$subject}}" name="subject"/>
                            </div>

                            <div class="form-group">
                                <label for="url">URL</label>
                                <input type="text" class="form-control" value="{{$url}}" name="url"/>
                            </div>

                            <div class="form-group">
                                <label for="group">Group:</label>
                                <input type="number" class="form-control" value="{{$group}}" name="group"/>
                            </div>
                            <button type="submit" class="btn btn-primary-outline">Send</button>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
