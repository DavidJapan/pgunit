@extends('app_layout')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-6">
            <div class="centred">
                <img src="/assets/logo.png">
                <h1>Welcome to</h1>
                <h2>The General Union Aministration Database</h2>
                <p>Version {{ config('app.version')}}</p>
            </div>
        </div>
    </div>
</div>

@endsection