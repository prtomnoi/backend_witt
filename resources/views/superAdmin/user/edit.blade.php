@extends('superAdmin.layout_super')

@section('h4-page', 'แก้ไขผู้ใช้งาน')

@section('contents')
    <div class="row layout-top-spacing">
        <div class="col-lg-12 col-md-6 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>แก้ไขผู้ใช้งาน</h5>
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
                    <form action="{{ route('user.update', $user->user_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">รหัสประชาชน <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="user_id_no"
                                           value="{{ old('user_id_no', $user->user_id_no) }}" required maxlength="13">
                                </div>
    
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">คำนำหน้า</label>
                                    <select class="form-control" name="user_prefix">
                                        <option value="นาย" {{ old('user_prefix', $user->user_prefix) == 'นาย' ? 'selected' : '' }}>นาย</option>
                                        <option value="นางสาว" {{ old('user_prefix', $user->user_prefix) == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                                        <option value="นาง" {{ old('user_prefix', $user->user_prefix) == 'นาง' ? 'selected' : '' }}>นาง</option>
                                    </select>
                                </div>
    
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">ชื่อ</label>
                                    <input type="text" class="form-control" name="user_fname"
                                           value="{{ old('user_fname', $user->user_fname) }}" required>
                                </div>
    
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">นามสกุล</label>
                                    <input type="text" class="form-control" name="user_lname"
                                           value="{{ old('user_lname', $user->user_lname) }}" required>
                                </div>
    
                             
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">วันที่เป็นสมาชิก <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="user_start_date"
                                           value="{{ old('user_start_date', $user->user_start_date) }}" required 
                                           placeholder="กรุณากรอกเลขสมาชิก 4 หลัก">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">สมาชิกเลขที่</label>
                                    <input type="text" class="form-control" name="user_number"
                                           value="{{ old('user_number', $user->user_number) }}" required  maxlength="4">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">วันเดือนปีเกิด</label>
                                    <input type="date" class="form-control" name="user_birthday"
                                           value="{{ old('user_birthday', $user->user_birthday) }}">
                                </div>
    
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">สถานะ</label>
                                    <select class="form-control" name="user_spouse_status">
                                        <option value="">เลือกสถานะ</option>
                                        <option value="S" {{ old('user_spouse_status', $user->user_spouse_status) == 'S' ? 'selected' : '' }}>โสด</option>
                                        <option value="W" {{ old('user_spouse_status', $user->user_spouse_status) == 'W' ? 'selected' : '' }}>หม้าย</option>
                                        <option value="M" {{ old('user_spouse_status', $user->user_spouse_status) == 'M' ? 'selected' : '' }}>สมรส</option>
                                    </select>
                                </div>
    
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ชื่อคู่สมรส (ถ้ามี)</label>
                                    <input type="text" class="form-control" name="user_spouse_name"
                                           value="{{ old('user_spouse_name', $user->user_spouse_name) }}">
                                </div>
    
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">เลขที่สมาชิกคู่สมรส (ถ้ามี)</label>
                                    <input type="text" class="form-control" name="user_spouse_number"
                                           value="{{ old('user_spouse_number', $user->user_spouse_number) }}">
                                </div>
    
                               
    
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">อีเมล</label>
                                    <input type="email" class="form-control" name="user_email"
                                           value="{{ old('user_email', $user->user_email) }}" required>
                                </div>
    
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">รหัสผ่าน (ปล่อยว่างหากไม่ต้องการเปลี่ยน)</label>
                                    <input type="password" class="form-control" name="user_password">
                                </div>
    
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">เบอร์โทร</label>
                                    <input type="text" class="form-control" name="user_tel"
                                           value="{{ old('user_tel', $user->user_tel) }}">
                                </div>
    
                                <div class="col-12 mb-3">
                                    <label class="form-label">ที่อยู่</label>
                                    <textarea class="form-control" name="user_address" required>{{ old('user_address', $user->user_address) }}</textarea>
                                </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">จังหวัด</label>
                                <select class="form-control" name="province_id" id="province_id" required>
                                    <option value="">เลือกจังหวัด</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->province_id }}"
                                            {{ $user->province_id == $province->province_id ? 'selected' : '' }}>
                                            {{ $province->province_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">อำเภอ</label>
                                <select class="form-control" name="amphur_id" id="amphur_id" required>
                                    <option value="">เลือกอำเภอ / เขต</option>
                                    @foreach($amphurs as $amphur)
                                        <option value="{{ $amphur->amphur_id }}"
                                            {{ $user->amphur_id == $amphur->amphur_id ? 'selected' : '' }}>
                                            {{ $amphur->amphur_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">ตำบล</label>
                                <select class="form-control" name="district_code" id="district_code" required>
                                    <option value="">เลือกตำบล / แขวง</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->district_code }}"
                                            {{ $user->district_code == $district->district_code ? 'selected' : '' }}>
                                            {{ $district->district_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">รหัสไปรษณีย์</label>
                                <input type="text" class="form-control" name="zip_id" id="zip_id"
                                       value="{{ $user->zip_id }}" readonly required>
                            </div>


{{--                            <div class="col-md-6 mb-3">--}}
{{--                                <label class="form-label">Profile Picture</label>--}}
{{--                                <input type="file" class="form-control" name="user_avatar" accept="image/*">--}}
{{--                                @if($user->user_avatar)--}}
{{--                                    <img src="{{ asset('storage/'.$user->user_avatar) }}"--}}
{{--                                         class="mt-2" style="max-width: 200px">--}}
{{--                                @endif--}}
{{--                            </div>--}}

                            <div class="col-md-6 mb-3">
                                <label class="form-label">รูปสำเนาบัตรประชาชน</label>
                                <input type="file" class="form-control" name="user_id_no_pic" accept="image/*">
                                @if($user->user_id_no_pic)
                                    <div class="mt-2 text-success">มีไฟล์แนบแล้ว</div>
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">รูปสำเนาทะเบียนบ้าน</label>
                                <input type="file" class="form-control" name="user_home_pic" accept="image/*">
                                @if($user->user_home_pic)
                                    <div class="mt-2 text-success">มีไฟล์แนบแล้ว</div>
                                @endif
                            </div>

                     

                         
                        </div>

                       
                        <div class="col-12 mb-3">
                            <label class="form-label">ข้อมูลอาชีพ</label>
                            <div class="alert alert-info mb-2">
                                <i class="fas fa-info-circle"></i> คำแนะนำ:
                                <ul class="mb-0">
                                    <li>กรุณากรอกข้อมูลอาชีพ</li>
                                    <li>สามารถเพิ่มได้มากกว่า 1 อาชีพ</li>
                                </ul>
                            </div>
                            <div id="occupations-container">
                                @foreach($user->occupations as $occupation)
                                <div class="row mb-2 occupation-row">
                                    <div class="col-4">
                                        <input type="text" class="form-control" name="occupation_name[]"
                                               value="{{ $occupation->occupation_name }}" placeholder="ชื่ออาชีพ">
                                    </div>
                                    <div class="col-3">
                                        <input type="number" class="form-control" name="occupation_income[]"
                                               value="{{ $occupation->occupation_income }}" placeholder="รายได้"
                                               step="0.01">
                                    </div>
                                    <div class="col-4">
                                        <select class="form-control" name="occupation_type[]">
                                            <option
                                                value="M" {{ $occupation->occupation_type == 'M' ? 'selected' : '' }}>
                                                อาชีพหลัก
                                            </option>
                                            <option
                                                value="S" {{ $occupation->occupation_type == 'S' ? 'selected' : '' }}>
                                                อาชีพรอง
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-1">
                                        {{-- <button type="button" class="btn btn-danger remove-occupation">ลบ</button> --}}
                                    </div>
                                </div>
                            @endforeach
                            </div>
                            <button type="button" class="btn btn-success" id="add-occupation">เพิ่มอาชีพ</button>
                        </div>

                        
                        <div class="col-12 mb-3">
                            <label class="form-label">การเป็นสมาชิกออมเงินกลุ่มอื่น</label>
                            <div id="other-members-container">
                                @foreach($user->otherGroupMembers as $member)
                                    <div class="row mb-2 other-member-row">
                                        <div class="col-11">
                                            <input type="text" class="form-control" name="ogm_names[]"
                                                   value="{{ $member->ogm_name }}" placeholder="ชื่อสมาชิก">
                                        </div>
                                        <div class="col-1">
                                            {{-- <button type="button" class="btn btn-danger remove-member">ลบ</button> --}}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-success" id="add-member">เพิ่มสมาชิกออมเงินกลุ่มอื่น
                            </button>
                        </div>


                        <div class="col-md-6 mb-3">
                            <label class="form-label">เงื่อนไข</label>
                            <select class="form-control" name="rule_id" required>
                                <option value="">Select Rule</option>
                                @foreach($rules as $rule)
                                    <option value="{{ $rule->rule_id }}"
                                        {{ $user->rule_id == $rule->rule_id ? 'selected' : '' }}>
                                        {{ $rule->rule_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">ตำแหน่ง</label>
                            <select class="form-control" name="position_id" required>
                                <option value="">Select Position</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->position_id }}"
                                        {{ $user->position_id == $position->position_id ? 'selected' : '' }}>
                                        {{ $position->position_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">ข้อมูลผู้รับผลประโยชน์ (ไม่เกิน 3 คน)</label>
                            <div class="alert alert-info mb-2">
                                <i class="fas fa-info-circle"></i> คำแนะนำ:
                                <ul class="mb-0">
                                    <li>กรุณากรอกข้อมูลให้ครบถ้วน</li>
                                    <li>สามารถเพิ่มได้สูงสุด 3 คน</li>
                                </ul>
                            </div>
                            <div id="beneficiaries-container">
                                @foreach($user->beneficiaries as $beneficiary)
                                <div class="row mb-2 beneficiary-row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="beneficiaries_name[]" value="{{ $beneficiary->beneficiaries_name }}" placeholder="ชื่อผู้รับผลประโยชน์">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" name="beneficiaries_id_no[]" value="{{ $beneficiary->beneficiaries_id_no }}" placeholder="เลขบัตรประชาชน">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="number" class="form-control" name="beneficiaries_age[]" value="{{ $beneficiary->beneficiaries_age }}" placeholder="อายุ" min="0" max="99">
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="beneficiaries_relation[]" value="{{ $beneficiary->beneficiaries_relation }}" placeholder="ความสัมพันธ์">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="number" class="form-control" name="beneficiaries_ratio[]" value="{{ $beneficiary->beneficiaries_ratio }}" placeholder="ของผู้สมัคร โดยรับผลประโยชน์ในสัดส่วน (ร้อยละ)" min="0" max="100">
                                            </div>
                                        </div>
                                    </div>
                                  
                                </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-success" id="add-beneficiary">เพิ่มผู้รับผลประโยชน์</button>
                        </div>


                        <div class="col-md-12 mb-12">
                            <label class="form-label">พยาน1</label>
                            <input type="text" class="form-control" name="witness1"
                                   value="{{ old('witness1', $user->witness1) }}" required>


                        </div>

                        <div class="col-md-12 mb-12">
                            <label class="form-label">พยาน2</label>
                            <input type="text" class="form-control" name="witness2"
                                   value="{{ old('witness2', $user->witness2) }}">
                        </div>

                        <div class="text-end mt-3">
                            <a href="{{ route('user.index') }}" class="btn btn-secondary">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary">แก้ไข</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
         document.addEventListener('DOMContentLoaded', function () {
            // Province change event
            document.getElementById('province_id').addEventListener('change', function () {

                console.log("5555555")

                var provinceId = this.value;
                if (provinceId) {
                    fetch('/get-amphurs/' + provinceId)
                        .then(response => response.json())
                        .then(data => {
                            var amphurSelect = document.getElementById('amphur_id');
                            var districtSelect = document.getElementById('district_code');
                            var zipInput = document.getElementById('zip_id');

                            amphurSelect.innerHTML = '<option value="">เลือกอำเภอ / เขต</option>';
                            districtSelect.innerHTML = '<option value="">เลือกตำบล / แขวง</option>';
                            zipInput.value = '';

                            data.forEach(function (item) {
                                amphurSelect.innerHTML += `<option value="${item.amphur_id}">${item.amphur_name}</option>`;
                            });
                        });
                }
            });

            // Amphur change event
            document.getElementById('amphur_id').addEventListener('change', function () {
                var amphurId = this.value;
                if (amphurId) {
                    fetch('/get-districts/' + amphurId)
                        .then(response => response.json())
                        .then(data => {
                            var districtSelect = document.getElementById('district_code');
                            var zipInput = document.getElementById('zip_id');

                            districtSelect.innerHTML = '<option value="">เลือกตำบล / แขวง</option>';
                            zipInput.value = '';

                            data.forEach(function (item) {
                                districtSelect.innerHTML += `<option value="${item.district_code}">${item.district_name}</option>`;
                            });
                        });
                }
            });

            // District change event
            document.getElementById('district_code').addEventListener('change', function () {
                var districtId = this.value;
                if (districtId) {
                    fetch('/get-zipcode/' + districtId)
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            document.getElementById('zip_id').value = data.zipcode_name;
                        });
                }
            });

            // เพิ่มสมาชิกใหม่
            document.getElementById('add-member').addEventListener('click', function () {
                const container = document.getElementById('other-members-container');
                const newRow = document.createElement('div');
                newRow.className = 'row mb-2 other-member-row';
                newRow.innerHTML = `
                            <div class="col-11">
                            <input type="text" class="form-control" name="ogm_names[]" placeholder="ชื่อสมาชิกออมเงินกลุ่มอื่น">
                            </div>
                            <div class="col-1">
                            <button type="button" class="btn btn-danger remove-member">ลบ</button>
                            </div>
                            `;
                container.appendChild(newRow);
            });

            // ลบสมาชิก
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-member')) {
                    e.target.closest('.other-member-row').remove();
                }
            });

            // เพิ่ม Beneficiary
            document.getElementById('add-beneficiary').addEventListener('click', function () {
            const container = document.getElementById('beneficiaries-container');
            const existingRows = container.querySelectorAll('.beneficiary-row');

            if (existingRows.length >= 3) {
                alert('คุณสามารถเพิ่มผู้รับผลประโยชน์ได้สูงสุด 3 คนเท่านั้น');
                return;
            }

            const newRow = document.createElement('div');
            newRow.className = 'row mb-2 beneficiary-row';
            newRow.innerHTML = `
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" class="form-control d-inline" name="beneficiaries_name[]" placeholder="ชื่อผู้รับผลประโยชน์">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control d-inline" name="beneficiaries_id_no[]" placeholder="เลขบัตรประชาชน">
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="form-control d-inline" name="beneficiaries_age[]" placeholder="อายุ" min="0" max="99">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <input type="text" class="form-control d-inline" name="beneficiaries_relation[]" placeholder="ความสัมพันธ์">
                        </div>
                        <div class="col-md-6">
                            <input type="number" class="form-control d-inline" name="beneficiaries_ratio[]" placeholder="ของผู้สมัคร โดยรับผลประโยชน์ในสัดส่วน (ร้อยละ)" min="0" max="100">
                        </div>
                    </div>
                </div>
                <div class="col-12 text-end mt-2">
                    <button type="button" class="btn btn-danger remove-beneficiary">ลบ</button>
                </div>
            `;
            container.appendChild(newRow);
        });

        // ลบแถวผู้รับผลประโยชน์
        document.getElementById('beneficiaries-container').addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-beneficiary')) {
                const row = e.target.closest('.beneficiary-row');
                row.remove();
            }
        });

// เพิ่ม Occupation
            document.getElementById('add-occupation').addEventListener('click', function () {
                const container = document.getElementById('occupations-container');
                const newRow = document.createElement('div');
                newRow.className = 'row mb-2 occupation-row';
                newRow.innerHTML = `
      <div class="col-4">
          <input type="text" class="form-control" name="occupation_name[]" placeholder="ชื่ออาชีพ">
      </div>
      <div class="col-3">
          <input type="number" class="form-control" name="occupation_income[]" placeholder="รายได้" step="0.01">
      </div>
      <div class="col-4">
          <select class="form-control" name="occupation_type[]">

              <option value="S">อาชีพรอง</option>
          </select>
      </div>
      <div class="col-1">
          <button type="button" class="btn btn-danger remove-occupation">ลบ</button>
      </div>
  `;
                container.appendChild(newRow);
            });

// ลบ Occupation
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-occupation')) {
                    e.target.closest('.occupation-row').remove();
                }
            });

            document.addEventListener('DOMContentLoaded', function () {
                // ตรวจสอบข้อมูลก่อน submit
                document.querySelector('form').addEventListener('submit', function (e) {
                    let isValid = true;

                    // ตรวจสอบ Beneficiaries
                    document.querySelectorAll('.beneficiary-row').forEach(row => {
                        const name = row.querySelector('[name="beneficiaries_name[]"]').value;
                        const age = row.querySelector('[name="beneficiaries_age[]"]').value;
                        const relation = row.querySelector('[name="beneficiaries_relation[]"]').value;

                        if (name || age || relation) {
                            if (!name || !age || !relation) {
                                alert('กรุณากรอกข้อมูลผู้รับผลประโยชน์ให้ครบถ้วน');
                                isValid = false;
                            }
                        }
                    });

                    // ตรวจสอบ Occupations
                    document.querySelectorAll('.occupation-row').forEach(row => {
                        const name = row.querySelector('[name="occupation_name[]"]').value;
                        const income = row.querySelector('[name="occupation_income[]"]').value;
                        const type = row.querySelector('[name="occupation_type[]"]').value;

                        if (name || income || type) {
                            if (!name || !income || !type) {
                                alert('กรุณากรอกข้อมูลอาชีพให้ครบถ้วน');
                                isValid = false;
                            }
                        }
                    });

                    if (!isValid) {
                        e.preventDefault();
                    }
                });

                // จัดการรูปแบบการแสดงผลตัวเลข
                document.querySelectorAll('input[name="occupation_income[]"]').forEach(input => {
                    input.addEventListener('input', function (e) {
                        let value = this.value.replace(/[^0-9.]/g, '');
                        if (value) {
                            value = parseFloat(value).toFixed(2);
                            this.value = value;
                        }
                    });
                });

                // จัดการรูปแบบการแสดงผลอายุ
                document.querySelectorAll('input[name="beneficiaries_age[]"]').forEach(input => {
                    input.addEventListener('input', function (e) {
                        let value = this.value.replace(/[^0-9]/g, '');
                        if (value > 99) value = 99;
                        this.value = value;
                    });
                });
            });

            document.querySelector('input[name="user_number"]').addEventListener('input', function(e) {
                // อนุญาตให้กรอกได้เฉพาะตัวเลขและตัวอักษร
                this.value = this.value.replace(/[^A-Za-z0-9]/g, '').substr(0, 4);
            });

        });
    </script>
@endsection
