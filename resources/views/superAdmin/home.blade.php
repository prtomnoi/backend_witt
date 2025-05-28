@extends('superAdmin.layout_super')

@section('contents')
<div class="row layout-top-spacing">

    {{-- Cards Summary --}}
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-header">
                    <div class="w-info">
                        <h6 class="value">Today Visitor</h6>
                    </div>
                </div>
                <div class="w-content">
                    <h5 class="text-success">{{ $todayVisitors  }}</h5>
                    <p class="task-left">Total users</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-header">
                    <div class="w-info">
                        <h6 class="value">Total Visitor</h6>
                    </div>
                </div>
                <div class="w-content">
                    <h5 class="text-primary">{{ $totalVisitors }}</h5>
                    <p class="task-left">Total users</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12 mb-4">
        <div class="widget widget-card-four">
            <div class="widget-content">
                <div class="w-header">
                    <div class="w-info">
                        <h6 class="value">Banners</h6>
                    </div>
                </div>
                <div class="w-content">
                    <h5 class="text-warning">{{ $bannerCount }}</h5>
                    <p class="task-left">Total active banners</p>
                </div>
            </div>
        </div>
    </div>

 
  
</div>
@endsection
