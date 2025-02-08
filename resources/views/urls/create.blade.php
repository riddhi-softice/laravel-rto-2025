@extends('layouts.app')
@section('content')
    <div class="pagetitle">
        <h1>URL Config</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('url_configs.index') }}"> URL Configs</a></li>
            </ol>
        </nav>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add URL Config Form</h5>
                        <form action="{{ route('url_configs.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Base URL</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="base_url" required>
                                </div>
                            </div>

                            <!-- <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">URLType</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="url_type" required>
                                    @error('url_type')
                                        <span class="text-danger">*URLtype must be unique! </span>
                                    @enderror
                                </div>
                            </div> -->

                            <div class="row mb-3">
                                <label for="url_brand_id" class="col-sm-2 col-form-label">Select Brand</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="url_brand_id" name="url_brand_id" required>
                                        <option value="">-- Select Brand --</option>
                                        @foreach($UrlBrand as $value)
                                            <option value="{{ $value->id }}"> {{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('url_brand_id')
                                        <span class="text-danger">*URLBrand must be unique! </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">URLParams</label>
                                <div class="col-sm-2">
                                    <button type="button" class="btn btn-success" onclick="addTextField()">Add New</button>
                                </div>
                            </div>

                            <div id="additionalFields">
                                <div class="row mb-3">
                                    <label for="inputText" class="col-sm-2 col-form-label"></label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="Param Key" name="url_dynamic_params[0][param_key]" required>
                                    </div>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="Param Value" name="url_dynamic_params[0][param_value]">
                                    </div>
                                    <div class="col-sm-2">
                                        <select class="form-control" id="value_status" name="url_dynamic_params[0][value_status]" required>
                                            <option value="">-- Select Value Status --</option>
                                            <option value="fixed">Fixed</option>
                                            <option value="dynamic">Dynamic</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-danger" onclick="removeTextField(this)">Remove</button>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Create</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@yield('javascript')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
    var additionalFieldsCounter = 0;
    var addFielsValue = 1;

    function addTextField() {

        var additionalFields = document.getElementById('additionalFields');
        var newField = document.createElement('div');
        newField.className = 'row mb-3';
        newField.innerHTML = `
            <label for="inputText" class="col-sm-2 col-form-label"></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" placeholder="Param Key"  name="url_dynamic_params[${addFielsValue}][param_key]"  required>
            </div>
            <div class="col-sm-3">
                <input type="text" class="form-control" placeholder="Param Value"  name="url_dynamic_params[${addFielsValue}][param_value]">
            </div>
            <div class="col-sm-2">
                <select class="form-control" id="value_status" name="url_dynamic_params[${addFielsValue}][value_status]" required>
                    <option value="">-- Select Value Status --</option>
                    <option value="fixed">Fixed</option>
                    <option value="dynamic">Dynamic</option>
                </select>
            </div>
            <div class="col-sm-2">
                <button type="button" class="btn btn-danger" onclick="removeTextField(this)">Remove</button>
            </div>
        `;

        // Remove the "Remove" button for the first added field
        // if (additionalFieldsCounter === 0) {
        //     newField.querySelector('.btn-danger').style.display = 'none';
        // }

        additionalFields.appendChild(newField);
        additionalFieldsCounter++;
        addFielsValue++;
    }

    function removeTextField(button) {
        var parentDiv = button.parentNode.parentNode;
        parentDiv.parentNode.removeChild(parentDiv);
        additionalFieldsCounter--;

        // Show the "Remove" button for the remaining fields
        if (additionalFieldsCounter > 0) {
            var fields = document.getElementById('additionalFields').querySelectorAll('.btn-danger');
            fields[fields.lparam_valueth - 1].style.display = 'block';
        }
    }
</script>

