@extends('superAdmin.layout_super')

@section('h4-page', 'แก้ไขบัญชี')

@section('contents')
    <div class="row layout-top-spacing">
        <div class="col-lg-12 col-md-6 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>แก้ไขบัญชี</h5>
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
                    <form action="{{ route('account.update', $account->account_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ผู้ใช้งาน</label>
                                <select class="form-control" name="user_id" required>
                                    <option value="">เลือกผู้ใช้งาน</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->user_id }}"
                                            {{ old('user_id', $account->user_id) == $user->user_id ? 'selected' : '' }}>
                                            {{ $user->user_fname }} {{ $user->user_lname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">เลขที่บัญชี</label>
                                <input type="text" class="form-control" name="account_no"
                                       value="{{ old('account_no', $account->account_no) }}" required maxlength="10">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">ชื่อบัญชี</label>
                                <input type="text" class="form-control" name="account_name"
                                       value="{{ old('account_name', $account->account_name) }}" required maxlength="500">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">เล่มที่</label>
                                <input type="text" class="form-control" name="account_book_no"
                                       value="{{ old('account_book_no', $account->account_book_no) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">สถานะ</label>
                                <select class="form-control" name="account_status" required>
                                    @foreach($statuses as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('account_status', $account->account_status) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

{{--                            <div class="col-md-6 mb-3">--}}
{{--                                <label class="form-label">เอกสารการประชุม</label>--}}
{{--                                <select class="form-control" name="meeting_doc_id">--}}
{{--                                    <option value="">เลือกเอกสารการประชุม</option>--}}
{{--                                    @foreach($meetingDocs as $doc)--}}
{{--                                        <option value="{{ $doc->meeting_doc_id }}"--}}
{{--                                            {{ old('meeting_doc_id', $account->meeting_doc_id) == $doc->meeting_doc_id ? 'selected' : '' }}>--}}
{{--                                            {{ $doc->meeting_doc_no }} - {{ $doc->meeting_doc_title }}--}}
{{--                                        </option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}

                            <div class="col-md-6 mb-3">
                                <label class="form-label">วันที่เริ่มใช้งาน</label>
                                <input type="date" class="form-control" name="account_start_date"
                                       value="{{ old('account_start_date', $account->account_start_date) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">จำนวนหน่วยลงทุนสุดท้าย</label>
                                <input type="number" class="form-control" name="account_final_unit"
                                       value="{{ old('account_final_unit', $account->account_final_unit) }}" min="0">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">จำนวนเงินสะสม</label>
                                <input type="number" class="form-control" name="account_balance"
                                       value="{{ old('account_balance', $account->account_balance) }}"
                                       step="0.01" min="0">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">หมายเหตุการปิดบัญชี</label>
                                <textarea class="form-control" name="account_close_remark"
                                          maxlength="800">{{ old('account_close_remark', $account->account_close_remark) }}</textarea>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('account.index') }}" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // เมื่อเลือกสถานะเป็นปิดบัญชี
        document.querySelector('select[name="account_status"]').addEventListener('change', function() {
            if (this.value === 'I') {
                if (!confirm('คุณต้องการปิดบัญชีนี้ใช่หรือไม่?')) {
                    this.value = '{{ $account->account_status }}';
                }
            }
        });
    </script>
@endsection
