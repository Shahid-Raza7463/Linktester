<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
@extends('Admin.admintemplate')
@section('content')
    <div class="container mt-3">
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title mt-3">Proxies</h3>
                <div class="card-tools">
                    <a href="{{ url('proxy') }}" class="btn btn-sm btn-success">
                        All
                    </a>
                </div>
            </div>
            <!-- display error massage for user-->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Display success massage for user-->
            @if (session('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
            <!--data store by this form in database-->
            <div class="form-Container m-3">
                <form action="{{ url('/') }}/proxy" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-6 d-flex flex-column  mb-2">
                            <label for="">iso:</label>
                            <input class="form-control" type="text" name="iso" placeholder="Enter iso">
                        </div>
                        <div class="col-6 d-flex flex-column  mb-2">
                            <label for="">ip address:</label>
                            <input class="form-control" type="text" name="ipAddress" placeholder="Enter ip address">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <input type="submit" class="btn btn-success btn-md" name="submit" value="submit">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
