@extends('layouts.app')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
@section('content')
    <div class="pagetitle">
        <h1>Categories</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Categories</li>
            </ol>
        </nav>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">Category List</h5>
                            <div>
                                <a href="{{ route('tags.create') }}" class="btn btn-primary">Add Category</a>
                            </div>
                        </div>
                        <table class="table datatable text-capitalize ">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tags as $value)
                                    <tr>
                                        <td>{{ $value->name }}</td>
                                        <td>
                                            <a href="{{ route('tags.edit', $value->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <button data-id="{{ $value->id }}" data-toggle="modal" data-target="#confirmDeleteModal" class="btn btn-danger btn-sm deleteData">Delete</button>
                                            {{-- <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button> --}}
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
                type: "DELETE",
                url: "{{ route('tags.destroy', ':id') }}".replace(':id', id),
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
