@extends('layout')

@section('h4-page', 'Profiles')

@section('contents')
    <div class="row layout-top-spacing">
        <div class="col-lg-12 col-md-6 mb-md-0 mb-4">
            <div class="mb-3">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">x</button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger mt-3 text-white">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="d-flex justify-content-end">
                    <a href="{{ route('profiles.create') }}" class="btn btn-sm btn-info font-weight-bold text-xs"
                        data-toggle="tooltip" data-original-title="Create user">
                        Create +
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (@$main as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->fullname() }}</td>
                                <td>
                                    <a href="{{ route('profiles.edit', $item->id) }}" class="btn btn-secondary">edit</a>
                                    <button class="btn btn-danger" onclick="deleteItem({{$item->id}})">delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No data found.</td>
                            </tr>
                        @endforelse
                        <tr>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function deleteItem(id) {
            Swal.fire({
                title: 'Delete?',
                text: "You want delete value ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#5e72e4',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    var is_url = "{{ route('profiles.destroy', ':id') }}";
                    $.ajax({
                        url: is_url.replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: 0,
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Success',
                                text: "Delete value success.",
                                icon: 'success',
                            }).then((result) => {
                                if(result.isConfirmed){
                                    location.reload();
                                }
                            });
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            Swal.fire({
                                title: 'Error!!!',
                                text: "Cannot delete value.",
                                icon: 'error',
                            });
                        }
                    });
                }
            })
        }
</script>
@endsection
