@extends('superAdmin.layout_super')

@section('h4-page', $flagOpen ? 'จัดการบัญชีสมาชิก' :'เพิ่มบัญชี')

@section('contents')
    <div class="row layout-top-spacing">
        <div class="col-lg-12 col-md-6 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>{{$flagOpen ? 'เพิ่มจำนวนหุ้นสำหรับเปิดบัญชีใหม่' :'เพิ่มบัญชี'}}</h5>
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
                    <form action="{{ route('account.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ผู้ใช้งาน  {{ auth()->id() }}</label>

                                @if($flagOpen)
                                    <input type="hidden" name="user_id" value="{{ $users->user_id }}">

                                    <input type="text" class="form-control" name="name"
                                           value="{{$users->getUserFullName()}}" readonly>
                                @else

                                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                                    <select class="form-control" name="name" disabled>
                                        <option value="">เลือกผู้ใช้งาน</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->user_id }}"
                                                {{ $user->user_id == auth()->id() ? 'selected' : '' }}>
                                                {{ $user->user_fname }} {{ $user->user_lname }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif

                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">เลขที่บัญชี</label>
                                <input type="text" class="form-control" name="account_no"
                                       value="{{ $accountNo }}" readonly>

                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">จำนวนหุ้น (สูงสุด {{ \App\Enums\Enum::UNIT_MAX }}
                                    หุ้น)</label>
                                <input type="number" class="form-control" name="unit_num"
                                       min="1" max="{{ \App\Enums\Enum::UNIT_MAX }}"
                                       value="{{ old('unit_num', 1) }}"
                                       required
                                       onchange="calculateTotal()">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">ราคาต่อหุ้น</label>
                                <input type="text" class="form-control"
                                       value="{{ number_format(\App\Enums\Enum::UNIT_PRICE) }} บาท"
                                       readonly>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">รวมมูลค่าทั้งหมด</label>
                                <input type="text" class="form-control" id="total_amount" readonly>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('account.index') }}" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit"
                                    class="btn btn-primary">{{$flagOpen ? 'ยืนยันการเปิดบัญชี' :'บันทึก'}}</button>
                        </div>
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
