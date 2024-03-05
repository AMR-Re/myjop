@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include('message')
            <div class="card shadow-lg">
                <div class="card-header">Login</div>
                <form action="{{route('login.post')}}" method="post">@csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Email</label>
                            <input type="text" name="email" class="form-control">
                            @if($errors->has('email'))
                            <span class="text-danger">{{ $errors->first('email')}}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="">Password</label>
                            <input type="password" name="password" class="form-control">
                            @if($errors->has('password'))
                            <span class="text-danger">{{ $errors->first('password')}}</span>
                            @endif
                        </div>
                        <br>
                        <div class="form-group text-center">
                            <button class="btn btn-dark" type="submit">Login</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-4">
             Employer your credentails:
            <p>Email: <p> 
            <p>Password:</p>
            <hr>
            <p>Note:Make sure you have linked  myjob.sql file </p>
            <p>You can also run migration and create your own records</p>
                        <hr>
            <p class="lead"> Please rate this project with  star 
                I have implement the stripe payment subscription with new stripe Api version and integerate it with the portal 
                go and try it 
            </p>
        </div>
    </div>
</div>
<style> 
body{
    background-color: #f5f5f5;
}
</style>

@endsection