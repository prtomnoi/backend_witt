@extends('superAdmin.layout_super')

@section('h4-page', 'ปิดบัญชี')

@section('contents')
    <div class="row layout-top-spacing">
        <div class="col-lg-12 col-md-6 mb-4">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5>ปิดบัญชี</h5>
                    
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">x</button>
                    </div>
                @endif

                <div class="card-body px-2 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                            <tr>
                                <th>รหัสประชาชน</th>
                                <th>ชื่อ</th>
                                <th>อีเมล</th>
                                <th>ตำแหย่ง</th>
                                <th>สถานะ</th>
                                <th>จัดการ</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->user_id_no }}</td>
                                    <td>{{ $user->user_fname }} {{ $user->user_lname }}</td>
                                    <td>{{ $user->user_email }}</td>
                                    <td>{{ $user->position->position_name ?? '-' }}</td>
                                    <td>
                                            <span class="badge bg-success">กำลังใช้งานอยู่</span>
                                    </td>
                              
                                    <td>
                                        <a href="" class="btn btn-sm btn-danger">ปิดบัญชี</a>
                                     
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">ไม่พบผู้ใช้งาน</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
