@extends('layouts.app')
<style>
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
        width: 50px; /* Id column */
    }

    .table th:nth-child(2), .table td:nth-child(2) {
        width: 350px; /* Base URLcolumn */
    }

    .table th:nth-child(3), .table td:nth-child(3) {
        width: 100px; /* URLType column */
    }

    .table th:nth-child(4), .table td:nth-child(4) {
        width: 350px; /* Generated URLcolumn */
    }

    .table th:nth-child(5), .table td:nth-child(5) {
        width: 100px; /* Action column */
    }
</style>
@section('content')

    <div class="pagetitle">
        <h1>URL Configs</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">URL Configs</li>
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
                            <h5 class="card-title">URL Config List</h5>
                            <div>
                                <a href="{{ route('url_configs.create') }}" class="btn btn-primary">Add URL Config</a>
                            </div>
                        </div>
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Base URL</th>
                                    <th>URL Brand</th>
                                    <th>Generated URL</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($url_configs as $key=>$value)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $value->original_url }}</td>
                                        <td>{{ $value->brand_name }}</td>
                                        <td>{{ $value->generate_url }}</td>
                                        <td style="text-align: center;">
                                            <button class="btn btn-secondary btn-sm copy-btn" style="margin-bottom: 3px;" title="Copy" data-url="{{ $value->generate_url }}"><i class="bi bi-copy"></i></button>
                                            <a href="{{ route('url_configs.edit', $value->url_id) }}" class="btn btn-sm btn-warning" style="margin-bottom: 3px;" title="Edit"><i class="bi bi-pencil"></i></a> 
                                            <button data-id="{{ $value->url_id }}" data-toggle="modal" data-target="#confirmDeleteModal" class="btn btn-danger btn-sm deleteData" title="Delete"><i class="bi bi-trash"></i></button>
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
                url: "{{ route('url_configs.destroy', ':id') }}".replace(':id', id),
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const copyButtons = document.querySelectorAll('.copy-btn');
        copyButtons.forEach(button => {
            button.addEventListener('click', function () {
                const urlToCopy = this.getAttribute('data-url');

                if (!urlToCopy) {
                    alert('URL to copy is empty or unavailable.');
                    return;
                }
                const tempTextarea = document.createElement('textarea');
                tempTextarea.value = urlToCopy;
                document.body.appendChild(tempTextarea);

                tempTextarea.select();
                tempTextarea.setSelectionRange(0, 99999); 
                try {
                    document.execCommand('copy');
                    // alert('URL copied to clipboard: ' + urlToCopy);
                } catch (err) {
                    console.error('Failed to copy URL:', err);
                    alert('Failed to copy URL. Please try again.');
                }
                document.body.removeChild(tempTextarea);
            });
        });
    });
</script>