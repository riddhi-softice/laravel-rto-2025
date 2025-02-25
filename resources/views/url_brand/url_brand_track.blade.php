@extends('layouts.app')
<link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@section('content')
    <div class="pagetitle">
        <h1>Track : {{$brand_name}} </h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('url_track.index') }}">URL Track</a></li>
                <li class="breadcrumb-item active">Brand Wise Track</li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Track : {{$brand_name}} </h5>
                        </div>
                        <div class="row mb-4">
                        
                            <input type="hidden" id="url_brand_id" class="form-control" value="{{$id}}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="keyFilter" class="form-label"><strong>Select Key</strong></label>
                                    <select id="keyFilter" class="form-control">
                                       <option value="">All Keys</option>
                                        @foreach($all_keys as $key)
                                            <option value="{{ $key ?? 'no_key' }}">{{ $key ?? 'No Key' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="startDate" class="form-label"><strong>Start Date</strong></label>
                                    <input type="date" id="startDate" class="form-control" placeholder="Start Date">
                                </div>
                            </div>
                            <div class="col-md-4">
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
                url: "{{ route('url_brand_track.detail') }}",
                data: function (d) {
                    d.brand_id = $('#url_brand_id').val();      //  brand
                    d.params_key = $('#keyFilter').val();      // Get selected key
                    d.start_date = $('#startDate').val();      // Get start date
                    d.end_date = $('#endDate').val();          // Get end date
                }
            }),
            order: [[3, 'desc']],
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'params_key', name: 'params_key' },
                { data: 'param_value', name: 'param_value' }, 
                { data: 'created_at', name: 'created_at' }, 
            ]
        });
             
        $('#keyFilter, #startDate, #endDate').on('change keyup', function () {
            table.ajax.reload();
        });
        $('#clearFilters').click(function () {
            // $('#brandFilter').val('');
            $('#keyFilter').val('');
            $('#startDate').val('');
            $('#endDate').val('');
            table.ajax.reload();
        });
    });
</script>


