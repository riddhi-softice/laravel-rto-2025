@extends('layouts.app')
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" /> -->
<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<style>
    .btn-close {
        display: block !important;
    }
    .table {
        table-layout: fixed; /* Enforces fixed-width columns */
        width: 100%; /* Ensures the table spans the full container */
    }
    .table th, .table td {
        white-space: normal; /* Allows the content to wrap to a new line */
        word-wrap: break-word; /* Ensures long words break properly */
        overflow-wrap: anywhere; /* Forces the URL to break at any point */
    }
    .table th:nth-child(1), .table td:nth-child(1) {
        width: 40px; /* Id column */
    }
    .table th:nth-child(2), .table td:nth-child(2) {
        width: 250px; /* Title column */
    }
    .table th:nth-child(3), .table td:nth-child(3) {
        width: 100px; /* Button column */
    }
    .table th:nth-child(4), .table td:nth-child(4) {
        width: 450px; /* Button Link column */
    }
    .table th:nth-child(5), .table td:nth-child(5) {
        width: 60px; /* Date column */
    }
    .table th:nth-child(6), .table td:nth-child(6) {
        width: 100px; /* Action column */
    }
</style>
@section('content')
    <div class="pagetitle">
        <h1>Reel</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Reels</li>
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
                            <h5 class="card-title">Reel List</h5>
                            <div>
                                <a href="{{ route('reels.create') }}" class="btn btn-primary">New Reel</a>
                            </div>
                        </div>
                        <!-- <table id="reelTable" class="table"> -->
                        <table class="table data-table" id="reelTable">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Title</th>
                                    <th>Button</th>
                                    <th>Button Link</th>
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

    <!-- Delete Modal -->
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

    <!-- Video Modal -->
    <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="videoModalLabel">Video Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <video id="videoPreview" width="100%" style="height:600px;" controls>
                        <source src="" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
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

        var table = $('#reelTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('reels.index') }}",
            order : [],
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'title', name: 'title', orderable: true },        
                { data: 'video_button', name: 'video_button', orderable: true },
                { data: 'button_link', name: 'button_link', orderable: true },
                { data: 'created_at', name: 'created_at', orderable: true }, 
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
        });

        $(document).on('click', '.deleteData', function(){
            var id = $(this).data('id');
            $('#confirmDelete').data('id', id);
        });

        $(document).on('click', '#confirmDelete', function(){
            var id = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "{{ route('reel_destroy', ':id') }}".replace(':id', id),
                success: function (data) {
                    $('#confirmDeleteModal').modal('hide'); 
                    window.location.reload();
                },
                error: function (xhr, status, error) {
                    console.error('Error deleting item:', error);
                }
            });
        });

        $(document).on('click', '.viewData', function() {
            var url = $(this).data('url');
            $('#videoPreview source').attr('src', url);
            $('#videoPreview')[0].load(); 
            $('#videoModal').modal('show');
        });

        $(document).on('click', '.btn-close', function() {
            $('#videoModal').modal('hide'); 
        });
    });
</script>
