@extends('layouts.app')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@section('content')
    <div class="pagetitle">
        <h1>Blog</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                 <li class="breadcrumb-item active"><a href="{{ route('blogs.index') }}">Blogs</a></li> 
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Blog Edit Form</h5>

                        <form action="{{ route('blogs.update',$blog->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- <div class="row mb-3">
                                <label for="tag_id" class="col-sm-2 col-form-label">Select Category</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="tag_id" name="tag_id" required>
                                        <option value="0">-- Select Category --</option>
                                        @foreach($tags as $tag)
                                            <option value="{{ $tag->id }}" {{ old('tag_id', $blog->tag_id) == $tag->id ? 'selected' : '' }}> {{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> -->

                            <div class="row mb-3">
                                <label for="select2Multiple" class="col-sm-2 col-form-label">Select Categories</label>
                                <div class="col-sm-10">
                                    <select class="select2-multiple form-control" name="tag_id[]" multiple="multiple" id="select2Multiple">
                                        @foreach($tags as $tag)
                                            <option value="{{ $tag->id }}" @if(in_array($tag->id, $selectedTags)) selected @endif>
                                                {{ $tag->name }}
                                            </option> 
                                        @endforeach            
                                    </select>
                                </div>
                            </div>

                            <!-- <div class="row mb-3">
                                <label for="select2Multiple" class="col-sm-2 col-form-label">Select Categories</label>
                                <div class="col-sm-10">
                                    <select class="select2-multiple form-control" name="tag_id[]" multiple="multiple" id="select2Multiple">
                                        @foreach($tags as $tag)
                                            <option value="{{ $tag->id }}">{{ $tag->name }}</option> 
                                        @endforeach            
                                    </select>
                                </div>
                            </div> -->

                            <div class="row mb-3">
                                <label for="title" class="col-sm-2 col-form-label"> Title</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="title"  value="{{ old('name',$blog['title']) }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="created_at" class="col-sm-2 col-form-label"> Publish At</label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" id="created_at"  value="{{ old('created_at', \Carbon\Carbon::parse($blog['created_at'])->format('Y-m-d')) }}"  name="created_at" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="content" class="col-sm-2 col-form-label">Description</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" type="text" name="content"  required  rows="15"> {{ $blog['content'] }}</textarea>
                                </div>
                            </div> 

                            <!-- <div class="row mb-3">
                                <label for="content" class="col-sm-2 col-form-label">Description</label>
                                <div class="col-sm-10">
                                    <textarea class="tinymce-editor form-control" name="content" required>
                                    {!! $blog->content !!}
                                    </textarea>
                                </div>
                            </div> -->

                            <div class="row mb-3">
                                <label for="inputNumber" class="col-sm-2 col-form-label"> Image</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="file" id="image_url" name="image_url">
                                    @if ($blog['image_url'])
                                        <img src="{{ asset('public/' .$blog['image_url']) }}" alt="Preview Image" class="mt-2" style="max-width: 150px;max-height: 150px;">
                                    @endif
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
@section('javascript')
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        const today = new Date();
        const formattedDate = [
            today.getFullYear(),                    
            ('0' + (today.getMonth() + 1)).slice(-2), 
            ('0' + today.getDate()).slice(-2)       
        ].join('-'); 

        const publishAtInput = document.querySelector('input[name="created_at"]');
        publishAtInput.setAttribute('max', formattedDate);  // Disable future dates
    });
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2-multiple').select2({
        placeholder: "Select",
        allowClear: true
    });
});
</script>
@endsection

