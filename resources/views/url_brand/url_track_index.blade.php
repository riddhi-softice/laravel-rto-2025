@extends('layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@section('content')
    <div class="pagetitle">
        <h1>URL Track</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">URL Track</li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                      
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">URL Track</h5>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="brandFilter" class="form-label"><strong>Select Brand</strong></label>
                                    <select id="brandFilter" class="form-control">
                                        <option value="">All Brands</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="keyFilter" class="form-label"><strong>Select Key</strong></label>
                                    <select id="keyFilter" class="form-control">
                                        <option value="">All Keys</option>
                                        @foreach($all_keys as $key)
                                            <option value="{{ $key }}">{{ $key }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="startDate" class="form-label"><strong>Start Date</strong></label>
                                    <input type="date" id="startDate" class="form-control" placeholder="Start Date">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="endDate" class="form-label"><strong>End Date</strong></label>
                                    <input type="date" id="endDate" class="form-control" placeholder="End Date">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <button id="clearFilters" class="btn btn-secondary">Clear All Filters</button>
                            </div>
                        </div>

                        <table class="table data-table" id="ListTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Brand</th>
                                    <th>Key</th>
                                    <th>Value</th>
                                    <th>Date</th>
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
        var table = $('#ListTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: ({
                url: "{{ route('url_track.filter') }}",
                data: function (d) {
                    d.url_brand_id = $('#brandFilter').val();  // Get selected brand
                    d.params_key = $('#keyFilter').val();      // Get selected key
                    d.start_date = $('#startDate').val();      // Get start date
                    d.end_date = $('#endDate').val();          // Get end date
                }
            }),
            order: [[0, 'desc']],
            columns: [
                { data: 'id', name: 'id' }, 
                { data: 'brand_name', name: 'brand_name' }, 
                { data: 'params_key', name: 'params_key' }, 
                { data: 'param_value', name: 'param_value' }, 
                { data: 'created_at', name: 'created_at' }, 
            ]
        });
        $('#brandFilter, #keyFilter, #startDate, #endDate').on('change keyup', function () {
            table.ajax.reload();
        });

        $('#clearFilters').click(function () {
            $('#brandFilter').val('');
            $('#keyFilter').val('');
            $('#startDate').val('');
            $('#endDate').val('');
            table.ajax.reload();
        });
    });
</script>


