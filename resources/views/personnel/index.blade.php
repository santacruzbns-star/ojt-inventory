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
                            <i class="bi bi-person-plus"></i> Personnel Management

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

                <div class="w-5">
                    <div class="mb-2">
                        <div class="input-group w-100">
                            <input type="text" id="OutboundSearch" class="form-control" placeholder="Search item...">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mb-3">
                        <select name="remarks" class="form-select form-select-sm" style="min-width: 120px;">
                            <option value="" disabled>Select Remark</option>
                            <option value="">All Remarks</option>
                            @foreach ($item_remarks as $remark)
                                <option value="{{ $remark }}"
                                    {{ request('remarks') == $remark ? 'selected' : '' }}>
                                    {{ $remark }}
                                </option>
                            @endforeach
                        </select>
                        {{-- <select name="personnel" class="form-select form-select-sm" style="min-width: 150px;">
                            <option value="">All Personnel</option>
                            @foreach ($personnels as $p)
                                <option value="{{ $p->personnel_id }}"
                                    {{ request('personnel') == $p->personnel_id ? 'selected' : '' }}>
                                    {{ $p->personnel_name }}
                                </option>
                            @endforeach
                        </select> --}}

                        <select name="department" class="form-select form-select-sm" style="min-width: 150px;">
                            <option value="" disabled>Select Department</option>
                            <option value="">All Departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept }}"
                                    {{ request('department') == $dept ? 'selected' : '' }}>
                                    {{ $dept }}
                                </option>
                            @endforeach
                        </select>

                        <select name="branch" class="form-select form-select-sm" style="min-width: 150px;">
                            <option value="" disabled>Select Branch</option>
                            <option value="">All Branches</option>

                            @foreach ($branches as $b)
                                <option value="{{ $b }}" {{ request('branch') == $b ? 'selected' : '' }}>
                                    {{ $b }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('outbound.index', array_merge(request()->query(), ['pdf' => 1, 'action' => 'view'])) }}"
                            class="btn btn-danger d-flex align-items-center gap-1" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> PRINT PDF
                        </a>

                        <a href="#" id="export_excel_btn" class="btn btn-success d-flex align-items-center gap-1"
                            data-bs-toggle="tooltip" title="Click if you want to export selected items">
                            <i class="bi bi-file-earmark-excel"></i> SELECTED EXCEL
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Personnel Form Modal --}}
        <div class="modal fade" id="personnel_modal" tabindex="-1">
            <div class="modal-dialog modal-xl"> <!-- 👈 make it wider -->
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title">Personnel Management</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row g-4">

                            <!-- LEFT SIDE: FORM -->
                            <div class="col-md-4 border-end">

                                <h6 class="text-muted mb-3">
                                    <i class="bi bi-person-plus me-1"></i> Add Personnel
                                </h6>

                                <form id="addPersonnelForm" action="{{ route('personnels.store') }}" method="POST"
                                    class="needs-validation" novalidate>
                                    @csrf

                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" name="personnel_name"
                                            placeholder="Enter Name" required>
                                        <label>Input Personnel Name</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <select name="branch_name" class="form-select" id="branch_name" required>
                                            <option value="" disabled selected>Select Branch</option>
                                            <option value="Gold Town">Gold Town</option>
                                            <option value="Edison Branch">Edison Branch</option>
                                            <option value="Osmeña Branch">Osmeña Branch</option>
                                            <option value="Grainsco Branch">Grainsco Branch</option>
                                        </select>
                                        <label>Branch Name</label>
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
                                        <label>Department</label>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100">
                                        Add Personnel
                                    </button>
                                </form>

                            </div>

                            <!-- MIDDLE: PERSONNEL LIST -->
                            <div class="col-md-4 border-end">
                                <h6 class="text-muted mb-3 d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-people me-1"></i> Personnel List</span>
                                    <span class="badge bg-secondary rounded-pill"
                                        id="personnelCount">{{ $personnels->count() }}</span>
                                </h6>

                                <div class="input-group mb-2 shadow-sm">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input type="text" id="personnelSearchView"
                                        class="form-control border-start-0 ps-0" placeholder="Quick search...">
                                </div>

                                <div class="list-group list-group-flush border rounded-3 overflow-hidden"
                                    style="max-height: 400px; overflow-y: auto;" id="personnelListView">

                                    @foreach ($personnels as $personnel)
                                        <div class="personnel-row position-relative border-bottom">
                                            <form action="{{ route('personnels.delete', $personnel->personnel_id) }}"
                                                method="POST"
                                                class="delete-form position-absolute top-50 end-0 translate-middle-y me-2"
                                                style="z-index: 5;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-link text-danger p-1 border-0 shadow-none hover-scale"
                                                    title="Remove">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </form>

                                            <button type="button"
                                                class="list-group-item list-group-item-action personnel-view-item border-0 py-2 pe-5"
                                                data-id="{{ $personnel->personnel_id }}"
                                                data-name="{{ $personnel->personnel_name }}"
                                                data-branch="{{ $personnel->branch->branch_name ?? 'N/A' }}"
                                                data-dept="{{ $personnel->branch->branch_department ?? 'N/A' }}">

                                                <div class="d-flex flex-column">
                                                    <strong class="text-dark mb-0 text-truncate"
                                                        style="max-width: 90%;">
                                                        {{ $personnel->personnel_name }}
                                                    </strong>
                                                    <small class="text-muted text-uppercase"
                                                        style="font-size: 0.7rem;">
                                                        {{ $personnel->branch->branch_name ?? 'N/A' }} |
                                                        {{ $personnel->branch->branch_department ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3 p-3 bg-white rounded border shadow-sm d-none"
                                    id="selectedPersonnelView">
                                    <div class="text-uppercase text-muted fw-bold"
                                        style="font-size: 0.65rem; letter-spacing: 0.05rem;">Active Selection</div>
                                    <h6 class="mb-0 text-primary fw-bold" id="view_person_name"></h6>
                                    <div class="text-secondary small">
                                        <i class="bi bi-geo-alt-fill me-1"></i><span id="view_branch"></span>
                                        <span class="mx-1">•</span>
                                        <i class="bi bi-building me-1"></i><span id="view_dept"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT: ASSIGNED ITEMS -->
                            <div class="col-md-4">

                                <h6 class="text-muted mb-3">
                                    <i class="bi bi-box-seam me-1"></i> Assigned Items
                                </h6>

                                <div id="assignedItemsContainer">
                                    <div class="text-muted">Select personnel to view items.</div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>

        {{-- Outbound Form Modal --}}
        <div class="modal fade" id="outbound_modal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title">Record Outbound Item</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('outbound.store') }}" method="POST"
                        class="needs-validation text-confirm-submit" id="outboundForm" novalidate>
                        @csrf
                        <input type="hidden" name="personnel_id" id="selected_personnel_id" required>
                        <input type="hidden" name="item_id" id="selected_item_id" required>
                        <div class="modal-body p-4">
                            <div class="row g-4">

                                <div class="col-md-4 border-end pe-md-3">
                                    <h6 class="text-muted mb-3"><i class="bi bi-person-badge me-1"></i> 1. Select
                                        Personnel</h6>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                        <input type="text" id="personnelSearch" class="form-control"
                                            placeholder="Search name or ID...">
                                    </div>
                                    <div class="list-group list-group-flush border rounded-3"
                                        style="max-height: 250px; overflow-y: auto;" id="personnelList">
                                        @foreach ($personnels as $personnel)
                                            <button type="button"
                                                class="list-group-item list-group-item-action personnel-item"
                                                data-id="{{ $personnel->personnel_id }}"
                                                data-name="{{ $personnel->personnel_name }}"
                                                data-branch="{{ $personnel->branch->branch_name ?? 'N/A' }}"
                                                data-dept="{{ $personnel->branch->branch_department ?? 'N/A' }}">
                                                <div class="d-flex w-100 justify-content-between align-items-center">
                                                    <h6 class="mb-1 text-truncate" style="max-width: 150px;">
                                                        {{ $personnel->personnel_name }}</h6>

                                                </div>
                                                <p class="mb-0 small text-muted">
                                                    <i
                                                        class="bi bi-building me-1"></i>{{ $personnel->branch->branch_name ?? 'N/A' }}
                                                    |
                                                    <i
                                                        class="bi bi-diagram-3 me-1"></i>{{ $personnel->branch->branch_department ?? 'N/A' }}
                                                </p>
                                            </button>
                                        @endforeach
                                    </div>

                                    <div class="invalid-feedback mt-2" id="personnelError">
                                        <i class="bi bi-exclamation-circle"></i> Please select a personnel.
                                    </div>

                                    <div class="mt-3 p-3 bg-light rounded-3 border d-none" id="selectedPersonnelCard">
                                        <p class="small text-muted mb-1 text-uppercase fw-bold"
                                            style="font-size: 0.7rem;">Assigned To:</p>
                                        <h6 class="mb-0 text-primary" id="display_personnel_name"></h6>
                                        <div class="small text-muted mt-1" style="font-size: 0.8rem;">
                                            <span id="display_branch"></span> &bull; <span id="display_dept"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 border-end pe-md-3">
                                    <h6 class="text-muted mb-3"><i class="bi bi-box-seam me-1"></i> 2. Select Item
                                    </h6>

                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                        <input type="text" id="itemSearch" class="form-control"
                                            placeholder="Search item, brand, or SN...">
                                    </div>

                                    <div class="list-group list-group-flush border rounded-3"
                                        style="max-height: 250px; overflow-y: auto;" id="itemList">
                                        @foreach ($items as $item)
                                            @continue($item->item_remark === 'Damaged' || $item->item_remark === 'Missing')
                                            @php
                                                $qty = $item->item_quantity_remaining ?? 0;
                                                $isOutOfStock = $qty <= 0;
                                            @endphp
                                            <button type="button"
                                                class="list-group-item list-group-item-action item-btn {{ $isOutOfStock ? 'disabled bg-light opacity-75' : '' }}"
                                                data-id="{{ $item->item_id }}" data-name="{{ $item->item_name }}"
                                                data-brand="{{ $item->brand->item_brand_name ?? 'N/A' }}"
                                                data-category="{{ $item->category->item_category_name ?? 'N/A' }}"
                                                data-serial="{{ $item->item_serialno ?? '-' }}"
                                                data-uom="{{ $item->uom->item_uom_name ?? 'Pcs' }}"
                                                data-qty="{{ $qty }}" {{ $isOutOfStock ? 'disabled' : '' }}>

                                                <div
                                                    class="d-flex w-100 justify-content-between align-items-center mb-1">
                                                    <h6 class="mb-0 text-truncate"
                                                        style="max-width: 140px; font-size: 0.95rem;">
                                                        {{ $item->item_name }}</h6>
                                                    <span
                                                        class="badge {{ $isOutOfStock ? 'bg-danger' : 'bg-success' }}">
                                                        {{ $qty }} Left
                                                    </span>
                                                </div>

                                                <div class="d-flex flex-wrap gap-2 mb-1" style="font-size: 0.75rem;">
                                                    <span class="text-muted"><i
                                                            class="bi bi-tag-fill me-1"></i>{{ $item->brand->item_brand_name ?? '-' }}</span>
                                                    <span class="text-muted"><i
                                                            class="bi bi-upc-scan me-1"></i>{{ $item->item_serialno ?? 'N/A' }}</span>
                                                </div>
                                                <div class="text-muted" style="font-size: 0.7rem;">
                                                    Cat: {{ $item->category->item_category_name ?? 'N/A' }} &bull; UOM:
                                                    {{ $item->uom->item_uom_name ?? '-' }}
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>

                                    <div class="invalid-feedback mt-2" id="itemError">
                                        <i class="bi bi-exclamation-circle"></i> Please select an item.
                                    </div>

                                    <div class="mt-3 p-3 bg-light rounded-3 border d-none" id="selectedItemCard">
                                        <p class="small text-muted mb-1 text-uppercase fw-bold"
                                            style="font-size: 0.7rem;">Item to Issue:</p>
                                        <h6 class="mb-0 text-success" id="display_item_name"></h6>
                                        <div class="small text-muted mt-1" style="font-size: 0.8rem;">
                                            Brand: <span id="display_item_brand"></span> &bull; SN: <span
                                                id="display_item_sn"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <h6 class="text-muted mb-3"><i class="bi bi-card-text me-1"></i> 3. Issuance
                                        Details</h6>

                                    <div class="form-floating mb-3">
                                        <input type="number" name="personnel_item_quantity" class="form-control"
                                            id="personnel_item_quantity" placeholder="Quantity" min="1"
                                            disabled required>
                                        <label for="personnel_item_quantity">Quantity to Issue</label>
                                        <div class="invalid-feedback" id="qtyErrorText">
                                            Please select an item first.
                                        </div>
                                        <div class="form-text text-success d-none mt-1" id="qtyAvailableText"></div>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="date" name="personnel_date_issued" class="form-control"
                                            id="personnel_date_issued" required>
                                        <label for="personnel_date_issued">Date Issued</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <select name="personnel_item_remarks" class="form-select"
                                            id="personnel_item_remarks" required>
                                            <option value="" disabled selected>Select Remark</option>
                                            <option value="Received">Received</option>
                                            <option value="Not Receive">Not Receive</option>
                                            {{-- <option value="To be delivered">To be delivered</option> --}}
                                        </select>
                                        <label for="personnel_item_remarks">Remarks</label>
                                        <div class="invalid-feedback">Remark is required.</div>
                                    </div>

                                    <div class="form-floating mb-3" id="receive_date_container"
                                        style="display:none;">
                                        <input type="date" name="personnel_date_receive" class="form-control"
                                            id="personnel_date_receive">
                                        <label for="personnel_date_receive">Receive Date</label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success px-4" id="submitBtn">Record
                                Outbound</button>
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
                        <th>Serial Number</th>
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
        </div>


    </body>
    <script>
        window.outboundData = {
            csrfToken: "{{ csrf_token() }}",
            routes: {
                index: "{{ route('outbound.index') }}",
                bulkDelete: "{{ route('outbound.bulkDelete') }}",

            },
            items: @json(
                $items->map(function ($item) {
                    return [
                        'name' => $item->item_name,
                        'category_id' => $item->item_category_id,
                    ];
                }))
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('storage/js/personnel/personnel.js') }}"></script>


</x-app-layout>
