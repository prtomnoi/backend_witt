@extends('superAdmin.layout_super')

@section('h4-page', 'Add Banners')

@section('contents')
    <div class="row layout-top-spacing">
        <div class="col-lg-12 col-md-6 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Add Banners</h5>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger m-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
            
                <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label>Product Slug</label>
                        <select name="product_slug" class="form-control" required>
                            <option value="">Select Product</option>
                            {{-- <option value="BLACKMAGIC-DESIGN">BLACKMAGIC DESIGN</option> --}}
                            <option value="HOME">HOME</option>
                            <option value="BLACKWING7">BLACKWING7</option>
                            <option value="DZOFILM">DZOFILM</option>
                            <option value="NiSi">NiSi</option>
                            <option value="MID49">MID49</option>
                            <option value="GODOX">GODOX</option>
                            {{-- <option value="BLAZAR">BLAZAR</option> --}}
                            <option value="CKMOVA">CKMOVA</option>
                        </select>
                    </div>
            
                    <div class="mb-3">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>
            
                    <div class="mb-3">
                        <label>Link (optional)</label>
                        <input type="text" name="link" class="form-control">
                    </div>
            
                    <div class="mb-3 d-none">
                        <label>Position</label>
                        <input type="number" name="position" class="form-control" value="1">
                    </div>
            
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="A">Active</option>
                            <option value="I">Inactive</option>
                        </select>
                    </div>
            
                    <button type="submit" class="btn btn-success">Create</button>
                </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        function calculateTotal() {
            const unitNum = document.getElementsByName('unit_num')[0].value;
            const unitPrice = {{ \App\Enums\Enum::UNIT_PRICE }};
            const total = unitNum * unitPrice;
            document.getElementById('total_amount').value = new Intl.NumberFormat('th-TH').format(total) + ' บาท';
        }

        // คำนวณครั้งแรกตอนโหลดหน้า
        document.addEventListener('DOMContentLoaded', calculateTotal);
    </script>
@endsection
