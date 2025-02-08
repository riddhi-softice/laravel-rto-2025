@extends('layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>Reel</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                 <li class="breadcrumb-item active"><a href="{{ route('reels.index') }}">Reels</a></li> 
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Create Form</h5>
                        <form action="{{ route('reels.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row mb-3">
                                <label for="title" class="col-sm-2 col-form-label">Title</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="video_button" class="col-sm-2 col-form-label">Button Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="video_button" required>
                                </div>
                            </div>
                          
                            <div class="row mb-3">
                                <label for="button_link" class="col-sm-2 col-form-label">Button Link</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="button_link" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="inputNumber" class="col-sm-2 col-form-label">Reel</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="file" id="video_url" name="video_url" accept="video/*" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
