@extends('superAdmin.layout_super')

@section('h4-page', 'Contact')

@section('contents')
    <div class="row layout-top-spacing">
        <div class="col-lg-12 col-md-6 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h5>Contact</h5>
                       
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
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Submit date</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($messages as $msg)
                                    <tr>
                                        <td>{{ $msg->name }}</td>
                                        <td>{{ $msg->email }}</td>
                                        <td>{{ $msg->subject }}</td>
                                        <td>{{ $msg->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <button type="button"
                                            class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#contactModal"
                                            data-name="{{ $msg->name }}"
                                            data-email="{{ $msg->email }}"
                                            data-subject="{{ $msg->subject }}"
                                            data-message="{{ $msg->message }}"
                                            data-date="{{ $msg->created_at->format('d/m/Y H:i') }}"
                                        >
                                            View
                                        </button>
                                    </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        {{ $messages->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg ">
      <div class="modal-content bg-white">
        <div class="modal-header">
          <h5 class="modal-title" id="contactModalLabel">Contact Message</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body ">
          <p><strong>Name:</strong> <span id="modalName"></span></p>
          <p><strong>Email:</strong> <span id="modalEmail"></span></p>
          <p><strong>Subject:</strong> <span id="modalSubject"></span></p>
          <p><strong>Message:</strong><span id="modalMessage"></span></p>
       
          <p class="mt-3 text-muted"><strong>Submitted at:</strong> <span id="modalDate"></span></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const contactModal = document.getElementById('contactModal');
            contactModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
        
                // ดึงข้อมูลจาก data-* attributes
                document.getElementById('modalName').textContent = button.getAttribute('data-name');
                document.getElementById('modalEmail').textContent = button.getAttribute('data-email');
                document.getElementById('modalSubject').textContent = button.getAttribute('data-subject');
                document.getElementById('modalMessage').textContent = button.getAttribute('data-message');
                document.getElementById('modalDate').textContent = button.getAttribute('data-date');
            });
        });
        </script>
        

@endsection
