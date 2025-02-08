@extends('layouts.app')
/*<style>*/
/*.card {*/
/*    cursor: pointer;*/
/*    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;*/
/*}*/

/*.card:hover {*/
/*    transform: scale(1.05);*/
/*    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);*/
/*}*/

/*</style>*/

@section('content')
    <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">

                    @foreach($data['type_array'] as $type)
                        <div class="col-xxl-2 col-md-6">
                                <div class="card info-card sales-card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $type->name }}</h5>
                                        <div class="d-flex align-items-center">
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="bi bi-asterisk"></i>
                                            </div>
                                            <div class="ps-3">
                                                <h6>{{ $type->user_count }}</h6>
                                                <span class="text-muted small pt-2 ps-1">Brand wise</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </section>
@endsection
@yield('javascript')