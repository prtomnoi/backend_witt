@extends('superAdmin.layout_super')

@section('h4-page', 'รวมบัญชีสมาชิก')

@section('contents')
    <div class="row layout-top-spacing">
        <div class="col-lg-12 col-md-6 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    {{-- <div class="d-flex justify-content-between">
                        <h5>รายการบัญชี</h5>
                        <a href="{{ route('account.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> เพิ่มบัญชี
                        </a>
                    </div> --}}

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">x</button>
                        </div>
                    @endif
                </div>
    
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0 p-2">
                            <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">เลขที่บัญชี</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ชื่อบัญชี</th>
                                {{-- <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ชื่อเจ้าของบัญชี</th> --}}
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">เล่มที่</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">สถานะ</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">จำนวนหุ้น</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">งวดที่ชำระ / งวดทั้งหมด</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">จำนวนเงินสะสม</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($accounts as $account)
                            @php
                                $year = date('Y');
                                $paidDeposits = $account->deposits;
                                $totalPeriods = $account->user->deposits
                                ->where('deposit_year', $year)
                                ->count();
                            @endphp
                        
                                <tr>
                                    <td>{{ $account->account_no }}</td>
                                    <td>{{ $account->account_name }}</td>
                                    {{-- <td>
                                        {{ $account->user->user_fname ?? '' }}
                                        {{ $account->user->user_lname ?? '' }}
                                    </td> --}}
                                    <td>{{ $account->account_book_no }}</td>
                                    <td>
                                        @switch($account->account_status)
                                            @case(\App\Enums\Enum::ACCOUNT_STATUS_W)
                                                <span class="badge bg-warning">รอพิจารณา</span>
                                                @break
                                            @case(\App\Enums\Enum::ACCOUNT_STATUS_A)
                                                <span class="badge bg-success">ใช้งาน</span>
                                                @break
                                            @case(\App\Enums\Enum::ACCOUNT_STATUS_I)
                                                <span class="badge bg-danger">ปิดบัญชี</span>
                                                @break
                                            @case(\App\Enums\Enum::ACCOUNT_STATUS_C)
                                                <span class="badge bg-secondary">ไม่ผ่านการเห็นชอบ</span>
                                                @break
                                            @case(\App\Enums\Enum::ACCOUNT_STATUS_L)
                                                <span class="badge bg-info">กู้เงิน</span>
                                                @break
                                            @case(\App\Enums\Enum::ACCOUNT_STATUS_G)
                                                <span class="badge bg-primary">ค้ำประกัน</span>
                                                @break
                                            @case(\App\Enums\Enum::ACCOUNT_STATUS_WI)
                                                <span class="badge bg-warning text-dark">รอพิจารณาปิดบัญชี</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">ไม่ระบุ</span>
                                        @endswitch
                                    </td>
                                    <td>{{ number_format($account->account_final_unit) }}</td>
                                    <td>  {{ $paidDeposits->count() }} / {{ $totalPeriods }}</td>
                                    <td class="text-end">{{ number_format($account->account_balance, 2) }}</td>
                                    <td>
                                        @if($account->account_status == \App\Enums\Enum::ACCOUNT_STATUS_A)
                                            <a href="{{ route('payDepositsId', $account->account_id) }}"
                                               class="btn btn-success btn-sm">
                                                ฝากเงิน
                                            </a>
                                            <a href="#" class="btn btn-danger btn-sm confirm-close-account"
                                               data-url="{{ route('account.preCloseAccount', $account->account_id) }}">
                                                ปิดบัญชี
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">ไม่พบข้อมูล</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        {{ $accounts->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.confirm-close-account').forEach(function (button) {
            button.addEventListener('click', function () {
                const url = this.getAttribute('data-url');

                Swal.fire({
                    title: 'คุณแน่ใจหรือไม่?',
                    text: "คุณต้องการปิดบัญชีนี้หรือไม่!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ใช่, ปิดบัญชี!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    });
</script>

@endsection
