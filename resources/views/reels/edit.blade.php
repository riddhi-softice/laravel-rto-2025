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
                        <h5 class="card-title">Reel Edit Form</h5>

                        <form action="{{ route('reels.update',$reel->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <label for="title" class="col-sm-2 col-form-label">Title</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="title"  value="{{ old('title',$reel['title']) }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="video_button" class="col-sm-2 col-form-label">Button</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="video_button"  value="{{ old('video_button',$reel['video_button']) }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="button_link" class="col-sm-2 col-form-label">Button Link</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="button_link"  value="{{ old('button_link',$reel['button_link']) }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="is_compress" class="col-sm-2 col-form-label">Is Compress</label>
                                <div class="col-sm-10 d-flex align-items-center">
                                    <!-- Checkbox with value 1 -->
                                    <input type="checkbox" id="is_compress" name="is_compress" value="1" 
                                        {{ old('is_compress', $reel['is_compress']) ? 'checked' : '' }}>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="video_url" class="col-sm-2 col-form-label">Upload Video</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="file" id="video_url" name="video_url" accept="video/*">
                                    @if (!empty($reel['video_url']))
                                        <video class="mt-2" width="150" height="150" controls>
                                            <source src="{{ asset('storage/app/public/compressed_videos/' . basename($reel['video_url'])) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @endif
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection