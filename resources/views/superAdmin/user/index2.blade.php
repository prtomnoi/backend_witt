@extends('superAdmin.layout_super')

@section('h4-page', 'จัดการสมาชิก')

@section('contents')
    <div class="row layout-top-spacing">
        <div class="col-lg-12 col-md-6 mb-4">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5>จัดการสมาชิก</h5>
                    <a href="{{ route('user.create') }}" class="btn btn-primary my-1">สมัครสมาชิกใหม่</a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">x</button>
                    </div>
                @endif

                <div class="card-body px-2 pb-2">
                    <form method="GET" action="{{ route('user.index') }}" class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อหรือเลขบัตรประชาชน" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">เลือกสถานะ</option>
                                <option value="A" {{ request('status') == 'A' ? 'selected' : '' }}>ใช้งานอยู่</option>
                                <option value="I" {{ request('status') == 'I' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                                <option value="P" {{ request('status') == 'P' ? 'selected' : '' }}>รอดำเนินการ</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="position" class="form-control">
                                <option value="">เลือกตำแหน่ง</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->position_id }}" 
                                        {{ request('position') == $position->position_id ? 'selected' : '' }}>
                                        {{ $position->position_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2 mt-2">
                            <button type="submit" class="btn btn-light">กรอง</button>
                            <a href="{{ route('user.index') }}" class="btn btn-light">รีเซ็ต</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th>รหัสประชาชน</th>
                                    <th>ชื่อ</th>
                                    <th>อีเมล</th>
                                    <th>ตำแหน่ง</th>
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
                                            @if($user->user_status == 'A')
                                                <span class="badge bg-success">ปกติ</span>
                                            @elseif($user->user_status == 'I')
                                                <span class="badge bg-danger">ไม่ใช้งาน</span>
                                            @else
                                                <span class="badge bg-warning">รอดำเนินการ</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->user_status == 'A')
                                                <a href="{{ route('meeting.index', $user->user_id) }}" class="btn btn-sm btn-primary">เอกสารประชุม</a>
                                            @else
                                                <a href="{{ route('user.openAccount', $user->user_id) }}" class="btn btn-sm btn-success">เปิดบัญชี</a>
                                            @endif
                                     
                                        </td>
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
