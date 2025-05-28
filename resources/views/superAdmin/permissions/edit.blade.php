@extends('superAdmin.layout_super')

@section('h4-page', 'การจัดการสิทธิ์การเข้าถึง')

@section('contents')
<style>
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
        box-shadow: none;
    }

    .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
<div class="row layout-top-spacing">
    <div class="col-lg-12 col-md-6 mb-md-0 mb-4">
        <div class="card">
            <div class="card-header pb-0">
                <h5 class="text-primary">แก้ไขสิทธิ์การเข้าถึง</h5>
                @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            <div class="card-body">
                <form action="{{ route('permission.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @forelse ($permission as $key => $item)
                    <div class="mb-4">
                        <h6 class="text-uppercase text-secondary text-xs font-weight-bold mb-3">
                            {{ $item->table }}
                        </h6>
                        <input type="hidden" name="id[]" value="{{ $item->id }}">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>สิทธิ์</th>
                                        <th class="text-center">อนุญาต</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>ดู</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input view" type="checkbox" value="1"
                                                    @if ($item->view == 1) checked @endif
                                                onchange="onchangeCheckboxView(this)">
                                                <input type="hidden" class="view-input" name="view[]"
                                                    value="{{ $item->view }}">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>สร้าง</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input create" type="checkbox" value="1"
                                                    @if ($item->create == 1) checked @endif
                                                onchange="onchangeCheckboxCreate(this)">
                                                <input type="hidden" class="create-input" name="create[]"
                                                    value="{{ $item->create }}">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>แก้ไข</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input update" type="checkbox" value="1"
                                                    @if ($item->update == 1) checked @endif
                                                onchange="onchangeCheckboxUpdate(this)">
                                                <input type="hidden" class="update-input" name="update[]"
                                                    value="{{ $item->update }}">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>ลบ</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input delete" type="checkbox" value="1"
                                                    @if ($item->delete == 1) checked @endif
                                                onchange="onchangeCheckboxDelete(this)">
                                                <input type="hidden" class="delete-input" name="delete[]"
                                                    value="{{ $item->delete }}">
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @empty
                    <div class="text-center">
                        <h6 class="text-secondary">ไม่มีข้อมูลสิทธิ์การเข้าถึง</h6>
                    </div>
                    @endforelse

                    <div class="text-end">
                        <a href="{{ route('permission.index') }}" class="btn btn-secondary">กลับ</a>
                        <button type="submit" class="btn btn-success">บันทึกสิทธิ์</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function onchangeCheckboxView(e) {
        e.closest('.form-check').querySelector('.view-input').value = e.checked ? 1 : 0;
    }

    function onchangeCheckboxCreate(e) {
        e.closest('.form-check').querySelector('.create-input').value = e.checked ? 1 : 0;
    }

    function onchangeCheckboxUpdate(e) {
        e.closest('.form-check').querySelector('.update-input').value = e.checked ? 1 : 0;
    }

    function onchangeCheckboxDelete(e) {
        e.closest('.form-check').querySelector('.delete-input').value = e.checked ? 1 : 0;
    }
</script>
@endsection
