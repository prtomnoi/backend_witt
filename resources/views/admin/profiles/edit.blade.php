@extends('layout')

@section('h4-page', 'Edit Profiles')

@section('contents')
    <div class="row layout-top-spacing">
        <div class="col-lg-12 col-md-6 mb-md-0 mb-4">
            <div class="card">
                @if ($errors->any())
                    <div class="alert alert-danger mt-3 text-white">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card-body">
                    <form action="{{ route('profiles.update', @$main->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <ul class="list-group">
                            <li class="list-group-item border-0 px-0">
                                <div class="form-group ps-0">
                                    <label for="fname" class="form-label">First Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="fname" name="fname"
                                        placeholder="First Nmae" maxlength="255" required value="{{ @$main->fname }}">
                                </div>
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <div class="form-group ps-0">
                                    <label for="lname" class="form-label">Last Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lname" name="lname"
                                        placeholder="Last Name" maxlength="255" required value="{{ @$main->lname }}">
                                </div>
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <div class="form-group ps-0">
                                    <label for="gender" class="form-label">Gender <span
                                            class="text-danger">*</span></label>
                                    <select name="gender" id="gender" class="form-control" required>
                                        <option value="M" @if (@$main->gender == 'M') selected @endif>Male
                                        </option>
                                        <option value="F" @if (@$main->gender == 'F') selected @endif>Female
                                        </option>
                                        <option value="OTHER" @if (@$main->gender == 'OTHER') selected @endif>Other
                                        </option>
                                    </select>
                                </div>
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <div class="form-group ps-0">
                                    <label for="birthday" class="form-label">Birth Day</label>
                                    <input type="date" class="form-control" name="birthday" id="birthday"
                                        value="{{ @$main->birthday }}">
                                </div>
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <div class="form-group ps-0">
                                    <label for="birthday" class="form-label">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" id="email"  placeholder="Email" maxlength="255" required
                                        value="{{ @$main->email }}">
                                </div>
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <div class="form-group ps-0">
                                    <label for="tel" class="form-label">Tel <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tel" name="tel" maxlength="10"
                                        required value="{{ @$main->tel }}">
                                </div>
                            </li>
                            <li class="list-group-item border-0 px-0">
                                <div class="form-group ps-0">
                                    <label for="image" class="form-label">Avatar</label>
                                    <input type="file" accept="image/*" class="form-control" name="image"
                                        id="image" onchange="previewImage(this)">
                                    @if (@$main->avatar)
                                        <div class="div-preview-image">
                                            <img src="{{ asset('app/accounts/' . @$main->avatar) }}" alt="image"
                                                width="100" height="100" class="img-fluid" id="preview-image">
                                        </div>
                                    @else
                                        <div class="d-none div-preview-image">
                                            <img src="#" alt="image" width="100" height="100"
                                                class="img-fluid" id="preview-image">
                                        </div>
                                    @endif
                                </div>
                            </li>
                        </ul>
                        <a href="{{ route('profiles.index') }}" class="btn bg-gradient-success">Back</a>
                        <button class="btn btn-success" type="submit">Save</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        function setInputFilter(textbox, inputFilter, errMsg) {
            ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout"].forEach(
                function(event) {
                    textbox.addEventListener(event, function(e) {
                        if (inputFilter(this.value)) {
                            // Accepted value.
                            if (["keydown", "mousedown", "focusout"].indexOf(e.type) >= 0) {
                                this.classList.remove("input-error");
                                this.setCustomValidity("");
                            }

                            this.oldValue = this.value;
                            this.oldSelectionStart = this.selectionStart;
                            this.oldSelectionEnd = this.selectionEnd;
                        } else if (this.hasOwnProperty("oldValue")) {
                            // Rejected value: restore the previous one.
                            this.classList.add("input-error");
                            this.setCustomValidity(errMsg);
                            this.reportValidity();
                            this.value = this.oldValue;
                            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                        } else {
                            // Rejected value: nothing to restore.
                            this.value = "";
                        }
                    });
                });
        }
        setInputFilter(document.getElementById("tel"), function(value) {
            return /^\d*$/.test(value);
        }, "Must be an unsigned integer");

        function previewImage(e) {
            const [file] = e.files
            if (file) {
                document.querySelector('.div-preview-image').classList.remove('d-none');
                document.getElementById('preview-image').src = URL.createObjectURL(file)
            }
        }
    </script>
@endsection
