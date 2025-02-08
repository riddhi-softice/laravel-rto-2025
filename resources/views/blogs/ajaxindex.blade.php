@extends('layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@section('content')
    <div class="pagetitle">
        <h1>Blogs</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Blogs</li>
            </ol>
        </nav>
    </div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert" >
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div id="alertContainer"></div>
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
                        <table class="table data-table text-capitalize" id="blogTable">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Top Status</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Publish At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
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
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

<script type="text/javascript">
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#blogTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('blogs.index') }}",
            // order: [], // Default ordering
            // order: [1, 'asc'], [2, 'desc']
            order: [[0, 'desc']],
            columns: [
                {data: 'id', name: 'id'},
                {data: 'top_status', name: 'top_status'},
                {data: 'image_url', name: 'image_url',orderable: false, searchable: false},
                {data: 'title', name: 'title'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $(document).on('click', '.deleteData', function(){
            var id = $(this).data('id');
            $('#confirmDelete').data('id', id);
        });

        $(document).on('click', '#confirmDelete', function(){
            var id = $(this).data('id');
            $.ajax({
                type: "DELETE",
                url: "{{ route('blog_destroy', ':id') }}".replace(':id', id),
                success: function (data) {
                    $('#confirmDeleteModal').modal('hide'); // Hide the modal
                    window.location.reload();
                },
                error: function (xhr, status, error) {
                    console.error('Error deleting item:', error);
                }
            });
        });
        
        $(document).on('change', '.top-status', function () {
            const id = $(this).data('id');
            const topStatus = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: "{{ route('blogs.updateTopStatus') }}",
                type: 'POST',
                data: {
                    id: id,
                    top_status: topStatus,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                },
                success: function (response) {
                    let message = '';
                    let alertType = '';
                    if (response.success) {
                        message = 'Top Status updated successfully.';
                        alertType = 'alert-success'; 
                    } else {
                        message = 'Failed to update Top Status.';
                        alertType = 'alert-danger'; 
                    }
                    const alertHtml = `
                        <div class="alert ${alertType} alert-dismissible fade show" role="alert">
                            ${message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    $('#alertContainer').html(alertHtml);
                    setTimeout(function() {
                        $('#alertContainer').html('');
                    }, 3000);
                },
                error: function () {
                    const errorMessage = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            An error occurred while updating the Top Status. Please try again.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    $('#alertContainer').html(errorMessage);
                }
            });
        });
    });
</script>

