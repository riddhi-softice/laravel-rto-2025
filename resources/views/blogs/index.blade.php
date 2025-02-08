@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>Blog</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Blog</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">Blog List</h5>
                            <div>
                                <a href="{{ route('blogs.create') }}" class="btn btn-primary">New Blog</a>
                            </div>
                        </div>
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($blogs as $blog)
                                    <tr>
                                        <td>
                                            @if ($blog['image_url'])
                                                <a href="{{ asset('public/' .$blog['image_url']) }}" target="_blank">
                                                    <img src="{{ asset('public/' .$blog['image_url']) }}" alt="{{ $blog->title }}" width="50" height="50">
                                                </a>
                                            @else
                                                <a href="{{ asset('public/img/No-Image-Placeholder.png') }}" target="_blank">
                                                    <img src="{{ asset('public/img/No-Image-Placeholder.png') }}" alt="img" width="50" height="50">
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{ $blog->title }}</td>
                                        <td>
                                            @php
                                                $formattedTime = '';
                                                if (isset($blog['created_at']) && !empty($blog['created_at'])) {
                                                    try {
                                                        $formattedTime = \Carbon\Carbon::parse($blog['created_at'])->format('d M Y');
                                                    } catch (\Exception $e) {
                                                        $formattedTime = 'Invalid Time';
                                                    }
                                                }
                                            @endphp
                                            {{ $formattedTime }}
                                        </td>
                                        <td>
                                            <a href="{{ route('blogs.edit', $blog->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <button data-id="{{ $blog->id }}" data-toggle="modal" data-target="#confirmDeleteModal" class="btn btn-danger btn-sm deleteData">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this record?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@yield('javascript')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script type="text/javascript">
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).on('click', '.deleteData', function(){
            var id = $(this).data('id');
            $('#confirmDelete').data('id', id);
        });
        $(document).on('click', '#confirmDelete', function(){
            var id = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "{{ route('blog_destroy', ':id') }}".replace(':id', id),
                success: function (data) {
                    $('#confirmDeleteModal').modal('hide');
                    window.location.reload();
                },
                error: function (xhr, status, error) {
                    console.error('Error deleting item:', error);
                }
            });
        });
    });
</script>

