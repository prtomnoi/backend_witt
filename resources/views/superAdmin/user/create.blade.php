@extends('superAdmin.layout_super')

@section('h4-page', 'สร้างบัญชีผู้ใช้งาน')

@section('contents')
    <div class="row layout-top-spacing">
        <div class="col-lg-12 col-md-6 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>รายละเอียดผู้ใช้งาน</h5>
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
                    <form action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">รหัสประชาชน <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="user_id_no"
                                           value="{{ old('user_id_no') }}" required maxlength="13">
                                    <button class="btn btn-outline-secondary" type="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                             class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">คำนำหน้า <span class="text-danger">*</span></label>
                                <select class="form-control" name="user_prefix">
                                    <option value="นาย">นาย</option>
                                    <option value="นางสาว">นางสาว</option>
                                    <option value="นาง">นาง</option>
                                </select>
                            </div>

                            <div class="col-md-5 mb-3">
                                <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="user_fname"
                                       value="{{ old('user_fname') }}" required>
                            </div>

                            <div class="col-md-5 mb-3">
                                <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="user_lname"
                                       value="{{ old('user_lname') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">วันที่เป็นสมาชิก <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="user_start_date"
                                       value="{{ old('user_start_date') }}" required 
                                       placeholder="กรุณากรอกเลขสมาชิก 4 หลัก">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">สมาชิกเลขที่ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="user_number"
                                       value="{{ old('user_number') }}" required maxlength="4"
                                       placeholder="กรุณากรอกเลขสมาชิก 4 หลัก">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">วันเดือนปีเกิด</label>
                                <input type="date" class="form-control" name="user_birthday"
                                       value="{{ old('user_birthday') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">สถานะ</label>
                                <select class="form-control" name="user_spouse_status" required>
                                    <option value="">เลือกสถานะ</option>
                                    <option value="S">โสด</option>
                                    <option value="W">หม้าย</option>
                                    <option value="M">สมรส</option>
                                    <option value="">อื่นๆ</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ชื่อคู่สมรส (ถ้ามี)</label>
                                <input type="text" class="form-control" placeholder="ถ้าไม่มี ไม่จำเป็นต้องกรอกข้อมูล" name="user_spouse_name" value="{{ old('user_spouse_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">เลขที่สมาชิกคู่สมรส (ถ้ามี)</label>
                                <input type="text" class="form-control" name="user_spouse_number" placeholder="ถ้าไม่มี ไม่จำเป็นต้องกรอกข้อมูล" value="{{ old('user_spouse_number') }}">
                            </div>
                         

                            <div class="col-md-6 mb-3">
                                <label class="form-label">อีเมลของสมาชิก</label>
                                <input type="email" class="form-control" name="user_email"
                                       value="{{ old('user_email') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">รหัสผ่านของสมาชิก</label>
                                <input type="password" class="form-control" name="user_password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">เบอร์โทรของสมาชิก</label>
                                <input type="text" class="form-control" name="user_tel"
                                       value="{{ old('user_tel') }}">
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">ที่อยู่ของสมาชิก</label>
                                <textarea class="form-control" name="user_address" required>{{ old('user_address') }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">จังหวัด</label>
                                <select class="form-control" name="province_id" id="province_id" required>
                                    <option value="">เลือกจังหวัด</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province->province_id }}">
                                            {{ $province->province_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">อำเภอ</label>
                                <select class="form-control" name="amphur_id" id="amphur_id" required>
                                    <option value="">เลือกอำเภอ / เขต</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">ตำบล</label>
                                <select class="form-control" name="district_code" id="district_code" required>
                                    <option value="">เลือกตำบล / แขวง</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">รหัสไปรษณีย์</label>
                                <input type="text" class="form-control" name="zip_id" id="zip_id" readonly required>
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
                                    <div class="row mb-2 occupation-row">
                                        <div class="col-4">
                                            <input type="text" class="form-control" name="occupation_name[]"
                                                   placeholder="ชื่ออาชีพ">
                                        </div>
                                        <div class="col-3">
                                            <input type="number" class="form-control" name="occupation_income[]"
                                                   placeholder="รายได้" step="0.01">
                                        </div>
                                        <div class="col-4">
                                            <select class="form-control" name="occupation_type[]">
                                                <option value="M">อาชีพหลัก</option>
                                                {{-- <option value="S">อาชีพรอง</option> --}}
                                            </select>
                                        </div>
                                       
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success" id="add-occupation">เพิ่มอาชีพ</button>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">การเป็นสมาชิกกลุ่ม หรือสหกรณ์ (ระบุกลุ่มอาชีพและสหกรณ์ที่เป็นสมาชิก)</label>
                                <div id="other-members-container">
                                    <div class="row mb-2 other-member-row">
                                        <div class="col-11">
                                            <input type="text" class="form-control" name="ogm_names[]"
                                                   placeholder="ชื่อสมาชิกออมเงินกลุ่มอื่น">
                                        </div>
                                       
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success" id="add-member">
                                    เพิ่มสมาชิกออมเงินกลุ่ม
                                </button>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">เงื่อนไข</label>
                                <select class="form-control" name="rule_id" required>
                                    <option value="">เลือกเงื่อนไข</option>
                                    @foreach($rules as $rule)
                                        <option value="{{ $rule->rule_id }}">
                                            {{ $rule->rule_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ตำแหน่ง</label>
                                    <select class="form-control" name="position_id" required>
                                        <option value="">เลือกตำแหน่ง</option>
                                        @foreach($positions as $position)
                                            <option value="{{ $position->position_id }}">
                                                {{ $position->position_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

{{--                                <div class="col-md-6 mb-3">--}}
{{--                                    <label class="form-label">Profile Picture</label>--}}
{{--                                    <input type="file" class="form-control" name="user_avatar" accept="image/*">--}}
{{--                                </div>--}}

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">รูปสำเนาบัตรประชาชน</label>
                                    <input type="file" class="form-control" name="user_id_no_pic" accept="image/*">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">รูปสำเนาทะเบียนบ้าน</label>
                                    <input type="file" class="form-control" name="user_home_pic" accept="image/*">
                                </div>

                          

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
                                    <div class="row mb-2 beneficiary-row">
                                        <div class="col-12">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control d-inline " name="beneficiaries_name[]" placeholder="ชื่อผู้รับผลประโยชน์" >
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control d-inline" name="beneficiaries_id[]" placeholder="เลขบัตรประชาชน" >
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control d-inline" name="beneficiaries_age[]" placeholder="อายุ" min="0" max="99" >
                                                    </div>
                                                  
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-6">
                                                        <input type="text" class="form-control d-inline" name="beneficiaries_relation[]" placeholder="ความสัมพันธ์" >
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="number" class="form-control d-inline " name="beneficiaries_ratio[]" placeholder="ของผู้สมัคร โดยรับผลประโยชน์ในสัดส่วน (ร้อยละ) " min="0" max="100" > 
                                                    </div>
                                                </div>

                                        </div>
                                       
                                    </div>
                                </div>
                            
                                <button type="button" class="btn btn-success" id="add-beneficiary">เพิ่มผู้รับผลประโยชน์</button>
                            </div>
                            
                            <div class="col-md-12 mb-12">
                                <label class="form-label">พยานคนที่ 1 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="witness1"
                                       value="{{ old('witness1') }}" required>
                            </div>

                            <div class="col-md-12 mb-12">
                                <label class="form-label">พยานคนที่ 2</label>
                                <input type="text" class="form-control" name="witness2"
                                       value="{{ old('witness2') }}">
                            </div>

                 

                            <div class="text-end mt-3">
                                <a href="{{ route('user.index') }}" class="btn btn-secondary">ยกเลิก</a>
                                <button type="submit" class="btn btn-primary">เพิ่มผู้ใช้งาน</button>
                            </div>
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
                            <input type="text" class="form-control d-inline" name="beneficiaries_id[]" placeholder="เลขบัตรประชาชน">
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
