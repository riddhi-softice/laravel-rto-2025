@extends('layouts.app')

@section('content')
    <div class="pagetitle">
        <h1>URL Config</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('url_configs.index') }}">URL Configs</a></li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Edit URL Form</h5>
                        <form action="{{ route('url_configs.update', $UrlConfig->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Base URL</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="base_url" value="{{ old('base_url', $UrlConfig->base_url) }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="url_brand_id" class="col-sm-2 col-form-label">Select Brand</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="url_brand_id" name="url_brand_id" required>
                                        <option value="">-- Select Brand --</option>
                                        @foreach($UrlBrand as $value)
                                            <option value="{{ $value->id }}" {{ old('url_brand_id', $UrlConfig->url_brand_id) == $value->id ? 'selected' : '' }}> {{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('url_brand_id')
                                        <span class="text-danger">*URL Brand must be unique! </span>
                                    @enderror
                                </div>
                            </div> 

                            <!-- <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label"> URL Type</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="url_type" value="{{ old('url_type', $UrlConfig->url_type) }}" required>
                                </div>
                            </div> -->

                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">URL Params</label>
                                <div class="col-sm-2">
                                    <button type="button" class="btn btn-success" onclick="addTextField()">Add New</button>
                                </div>
                            </div>

                            <div id="additionalFields">
                                @foreach($DynamicParam as $key=>$fieldValue)
                                    <div class="row mb-3">
                                        <label for="inputText" class="col-sm-2 col-form-label"></label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" placeholder="Param Key" value="{{ $fieldValue->param_key }}" name="url_dynamic_params[{{$key}}][param_key]">
                                        </div>
                                        <div class="col-sm-3">
                                            <input class="form-control" placeholder="Param Value" name="url_dynamic_params[{{$key}}][param_value]" value="{{ $fieldValue->param_value }}">
                                        </div>
                                        <div class="col-sm-2">
                                            <select class="form-control" name="url_dynamic_params[{{$key}}][value_status]" required>
                                                <option value="">-- Select Value Status --</option>
                                                <option value="fixed" {{ $fieldValue->value_status == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                <option value="dynamic" {{ $fieldValue->value_status == 'dynamic' ? 'selected' : '' }}>Dynamic</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-danger" onclick="removeTextField(this)">Remove</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>

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
    // Counter for generating unique field keys
    let fieldCounter = {{ count($DynamicParam) }};

    // Function to add a new text field
    function addTextField() {
        const additionalFields = document.getElementById('additionalFields');
        const newField = document.createElement('div');
        newField.className = 'row mb-3';

        newField.innerHTML = `
            <label for="inputText" class="col-sm-2 col-form-label"></label>
            <div class="col-sm-3">
                <input type="text" class="form-control" placeholder="Param Key" 
                    name="url_dynamic_params[${fieldCounter}][param_key]" required>
            </div>
            <div class="col-sm-3">
                <input type="text" class="form-control" placeholder="Param Value" 
                    name="url_dynamic_params[${fieldCounter}][param_value]">
            </div>
            <div class="col-sm-2">
                <select class="form-control" name="url_dynamic_params[${fieldCounter}][value_status]" required>
                    <option value="">-- Select Value Status --</option>
                    <option value="fixed">Fixed</option>
                    <option value="dynamic">Dynamic</option>
                </select>
            </div>
            <div class="col-sm-2">
                <button type="button" class="btn btn-danger" onclick="removeTextField(this)">Remove</button>
            </div>
        `;

        additionalFields.appendChild(newField);
        fieldCounter++;
    }

    // Function to remove a text field
    function removeTextField(button) {
        const fieldToRemove = button.closest('.row');
        fieldToRemove.remove();
    }
</script>

