<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Personnel & Outbound Management
        </h2>
    </x-slot>

    <style>
        /* Add any custom styles here */
    </style>

    <body>
        <div class="container-fluid px-4 mt-4">

            {{-- Top Action Bar --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal"
                            data-bs-target="#personnel_modal">
                            <i class="bi bi-person-plus"></i> Add Personnel
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#outbound_modal">
                            <i class="bi bi-box-arrow-right"></i> Record Outbound
                        </button>
                    </div>

                    <button id="bulk_delete_btn" class="btn btn-danger" disabled>
                        <i class="bi bi-trash"></i>
                        Delete Selected
                    </button>
                </div>

                <div class="w-25">
                    <div class="mb-2">
                        <div class="input-group w-100">
                            <input type="text" id="tableSearch" class="form-control" placeholder="Search records...">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Personnel Form Modal --}}
            <div class="modal fade" id="personnel_modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h4 class="modal-title">Add Personnel</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form action="{{ route('personnels.store') }}" method="POST" class="needs-validation"
                            novalidate>
                            @csrf
                            <div class="modal-body">

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="personnel_name" name="personnel_name"
                                        placeholder="Enter Name" required>
                                    <label for="personnel_name">Personnel Name</label>
                                    <div class="invalid-feedback">
                                        Please enter the personnel's name.
                                    </div>
                                </div>

                                <div class="form-floating mb-3">
                                    <select name="branch_name" class="form-select" id="branch_name" required>
                                        <option value="" disabled selected>Select Branch</option>
                                        <option value="Edison Branch">Edison Branch</option>
                                        <option value="Osmeña Branch">Osmeña Branch</option>
                                        <option value="Grainsco Branch">Grainsco Branch</option>
                                    </select>
                                    <label for="branch_name">Branch Name</label>
                                    <div class="invalid-feedback">Please select a branch.</div>
                                </div>

                                <div class="form-floating mb-3">
                                    <select name="branch_department" class="form-select" id="branch_department"
                                        required>
                                        <option value="" disabled selected>Select Department</option>
                                        <option value="IT DEPARTMENT">IT DEPARTMENT</option>
                                        <option value="HR DEPARTMENT">HR DEPARTMENT</option>
                                        <option value="ADMIN DEPARTMENT">ADMIN DEPARTMENT</option>
                                        <option value="PURCHASING DEPARTMENT">PURCHASING DEPARTMENT</option>
                                        <option value="WAREHOUSE DEPARTMENT">WAREHOUSE DEPARTMENT</option>
                                        <option value="SERVICE CENTER ">SERVICE CENTER </option>
                                        <option value="SERVICE SHOP ">SERVICE SHOP</option>
                                        <option value="SPARE PARTS ">SPARE PARTS</option>
                                        <option value="CORPORATE OFFICE">CORPORATE OFFICE</option>
                                        <option value="ACCOUNTING DEPARTMENT">ACCOUNTING DEPARTMENT</option>
                                        <option value="GUARD DEPARTMENT">GUARD DEPARTMENT</option>
                                        <option value="AMBOT SA EMO DEPARTMENT">AMBOT SA EMO DEPARTMENT</option>
                                    </select>
                                    <label for="branch_department">Department</label>
                                    <div class="invalid-feedback">Please select a department.</div>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-light text-dark"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            {{-- Outbound Form Modal --}}
            <div class="modal fade" id="outbound_modal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h4 class="modal-title">Record Outbound Item</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form action="{{ route('outbound.store') }}" method="POST"
                            class="needs-validation text-confirm-submit" novalidate>
                            @csrf
                            <div class="modal-body">

                                <!-- Personnel -->
                                <div class="form-floating mb-3">
                                    <select name="personnel_id" class="form-select" id="personnel_id" required>
                                        <option value="" disabled selected>Select Personnel</option>
                                        @foreach ($personnels as $personnel)
                                            <option value="{{ $personnel->personnel_id }}">
                                                {{ $personnel->personnel_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="personnel_id">Personnel</label>
                                    <div class="invalid-feedback">Please select a personnel.</div>
                                </div>

                                <!-- Item -->
                                <div class="form-floating mb-3">
                                    <select name="item_id" class="form-select" id="item_id" required>
                                        <option value="" disabled selected>Select Item</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->item_id }}">{{ $item->item_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="item_id">Item</label>
                                    <div class="invalid-feedback">Please select an item.</div>
                                </div>

                                <!-- Quantity -->
                                <div class="form-floating mb-3">
                                    <input type="number" name="personnel_item_quantity" class="form-control"
                                        id="personnel_item_quantity" placeholder="Quantity" min="1" required>
                                    <label for="personnel_item_quantity">Quantity</label>
                                    <div class="invalid-feedback">Quantity is required and must be at least 1.</div>
                                </div>

                                <!-- Date Issued -->
                                <div class="form-floating mb-3">
                                    <input type="date" name="personnel_date_issued" class="form-control"
                                        id="personnel_date_issued">
                                    <label for="personnel_date_issued">Date Issued (Optional)</label>
                                </div>

                                <!-- Remarks -->
                                <div class="form-floating mb-3">
                                    <select name="personnel_item_remarks" class="form-select"
                                        id="personnel_item_remarks" required>
                                        <option value="" disabled selected>Select Remark</option>
                                        <option value="Received">Received</option>
                                        <option value="Not Receive">Not Receive</option>
                                        <option value="To be delivered">To be delivered</option>
                                    </select>
                                    <label for="personnel_item_remarks">Remarks</label>
                                    <div class="invalid-feedback">Remark is required.</div>
                                </div>

                                <!-- Receive Date (hidden by default) -->
                                <div class="form-floating mb-3" id="receive_date_container" style="display:none;">
                                    <input type="date" name="personnel_date_receive" class="form-control"
                                        id="personnel_date_receive">
                                    <label for="personnel_date_receive">Receive Date (optional)</label>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-light text-dark"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Record Outbound</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            {{-- Table View Placeholder --}}
            <div class="table-responsive w-100 mt-4">
                <table class="table table-striped w-100">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox" id="select_all"></th>
                            <th>Custodian</th>
                            <th>Product Name</th>
                            <th>Date Issued</th>
                            <th>Quantity</th>
                            <th>Unit of Measure</th>
                            <th>Date Received</th>
                            <th>Branch</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="table-data">

                        @include('personnel.outbound-table')
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $outbounds->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </body>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- JS to toggle Receive Date --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const remarksSelect = document.getElementById('personnel_item_remarks');
            const receiveDateContainer = document.getElementById('receive_date_container');

            remarksSelect.addEventListener('change', function() {
                if (this.value === 'Received') {
                    // Show receive date only if 'Received' is selected
                    receiveDateContainer.style.display = 'block';
                } else {
                    receiveDateContainer.style.display = 'none';
                    document.getElementById('personnel_date_receive').value = '';
                }
            });
        });
    </script>
    <script>
        // Session Success Alert
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        @endif

        // Bootstrap Validation
        document.addEventListener("DOMContentLoaded", function() {
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(f => {
                f.addEventListener('submit', event => {
                    if (!f.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    f.classList.add('was-validated');
                }, false);
            });
        });

        // SweetAlert Confirmation for specific forms
        const confirmForms = document.querySelectorAll(".text-confirm-submit");
        confirmForms.forEach(function(form) {
            form.addEventListener("submit", function(e) {
                if (form.checkValidity()) {
                    e.preventDefault();
                    Swal.fire({
                        title: "Confirm Submission?",
                        text: "Are you sure you want to save this record?",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonColor: "#198754", // Bootstrap Success
                        cancelButtonColor: "#6c757d",
                        confirmButtonText: "Yes, submit it!",
                        cancelButtonText: "Cancel"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
    
</x-app-layout>
