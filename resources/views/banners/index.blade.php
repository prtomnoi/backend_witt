@extends('superAdmin.layout_super')

@section('h4-page', 'Banners')

@section('contents')
    <div class="row layout-top-spacing">
        <div class="col-lg-12 col-md-6 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h5>Banners</h5>
                        <a href="{{ route('banners.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create new
                        </a>
                    </div>

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
                                    <th>Product Slug</th>
                                    <th>Image</th>
                                    <th>Link</th>
                                    {{-- <th>Position</th> --}}
                                    <th>Status</th>
                                    <th>Del</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($banners as $banner)
                                <tr>
                                    <td>{{ $banner->product_slug }}</td>
                                    <td><img src="{{ asset('storage/' . $banner->image) }}" width="100" /></td>
                                    <td>{{ $banner->link }}</td>
                                    {{-- <td>{{ $banner->position }}</td> --}}
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input toggle-status {{ $banner->status === 'A' ? 'bg-success' : 'bg-danger' }}"
                                                   type="checkbox"
                                                   data-id="{{ $banner->id }}"
                                                   {{ $banner->status === 'A' ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <form action="{{ route('banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        {{ $banners->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-status').forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const bannerId = this.getAttribute('data-id');
                    const newStatus = this.checked ? 'A' : 'I';
        
                    fetch(`/banners/${bannerId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: newStatus })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Toggle CSS class color
                            this.classList.remove('bg-success', 'bg-danger');
                            this.classList.add(this.checked ? 'bg-success' : 'bg-danger');
                        } else {
                            alert('Failed to update status');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Error occurred while updating');
                    });
                });
            });
        });
        </script>
        
        
@endsection
