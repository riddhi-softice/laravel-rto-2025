@extends('layouts.app')

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

                <a href="{{ route('url_track.index') }}" class="text-decoration-none">
                    <div class="col-xxl-2 col-md-6">
                        <div class="card info-card sales-card">
                            <div class="card-body">
                                <h5 class="card-title">Total URL Track</h5>
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-asterisk"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6>{{ $data }}</h6>
                                        <!-- <span class="text-muted small pt-2 ps-1">Brand wise</span> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>

                </div>
            </div>
        </div>
    </section>
@endsection
@yield('javascript')