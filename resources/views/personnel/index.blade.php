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
                        Delete Selected (0)
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
                        <a href="#" id="export_pdf_btn" class="btn btn-danger d-flex align-items-center gap-1"
                            title="Checked rows: PDF only those. No checks: PDF uses current filters (all matching rows).">
                            <i class="bi bi-file-earmark-pdf"></i> Print PDF (0)
                        </a>

                        <a href="#" id="export_excel_btn" class="btn btn-success d-flex align-items-center gap-1"
                            title="Checked rows: Excel only those. No checks: Excel uses current filters (all matching rows).">
                            <i class="bi bi-file-earmark-excel"></i> Excel (0)
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
                                        <input type="text" class="form-control text-uppercase" name="personnel_name"
                                            placeholder="ENTER NAME" required>
                                        <label class="text-uppercase">INPUT PERSONNEL NAME</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <select name="branch_name" class="form-select text-uppercase" id="branch_name" required>
                                            <option value="" disabled selected>SELECT BRANCH</option>
                                            <option value="Gold Town">GOLD TOWN</option>
                                            <option value="Edison Branch">EDISON BRANCH</option>
                                            <option value="Osmeña Branch">OSMEÑA BRANCH</option>
                                            <option value="Grainsco Branch">GRAINSCO BRANCH</option>
                                        </select>
                                        <label class="text-uppercase">BRANCH NAME</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <select name="branch_department" class="form-select text-uppercase" id="branch_department"
                                            required>
                                            <option value="" disabled selected>SELECT DEPARTMENT</option>
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
                                        <label class="text-uppercase">DEPARTMENT</label>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100 text-uppercase">
                                        ADD PERSONNEL
                                    </button>
                                </form>

                            </div>

                            <div class="col-md-4 border-end">
                                <h6 class="text-muted mb-3 d-flex justify-content-between align-items-center text-uppercase">
                                    <span><i class="bi bi-people me-1"></i> PERSONNEL LIST</span>
                                    <span class="badge bg-secondary rounded-pill"
                                        id="personnelCount">{{ $personnels->count() }}</span>
                                </h6>

                                <div class="input-group mb-2 shadow-sm">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input type="text" id="personnelSearchView"
                                        class="form-control border-start-0 ps-0 text-uppercase" placeholder="QUICK SEARCH...">
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
                                                    class="btn btn-link text-danger p-1 border-0 shadow-none hover-scale text-uppercase"
                                                    title="REMOVE">
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
                                                    <strong class="text-dark mb-0 text-truncate text-uppercase"
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
                                        style="font-size: 0.65rem; letter-spacing: 0.05rem;">ACTIVE SELECTION</div>
                                    <h6 class="mb-0 text-primary fw-bold text-uppercase" id="view_person_name"></h6>
                                    <div class="text-secondary small text-uppercase">
                                        <i class="bi bi-geo-alt-fill me-1"></i><span id="view_branch"></span>
                                        <span class="mx-1">•</span>
                                        <i class="bi bi-building me-1"></i><span id="view_dept"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">

                                <h6 class="text-muted mb-3 text-uppercase">
                                    <i class="bi bi-box-seam me-1"></i> ASSIGNED ITEMS
                                </h6>

                                <div id="assignedItemsContainer" class="border rounded-3" style="max-height: 400px; overflow-y: auto;">
                                    <div class="text-muted text-uppercase p-3 small text-center">SELECT PERSONNEL TO VIEW ITEMS.</div>
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
                                    <h6 class="text-muted mb-3 text-uppercase"><i class="bi bi-person-badge me-1"></i>
                                        1. SELECT
                                        PERSONNEL</h6>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                        <input type="text" id="personnelSearch"
                                            class="form-control text-uppercase" placeholder="SEARCH NAME OR ID...">
                                    </div>
                                    <div class="list-group list-group-flush border rounded-3"
                                        style="max-height: 250px; overflow-y: auto;" id="personnelList">
                                        @foreach ($personnels as $personnel)
                                            <button type="button"
                                                class="list-group-item list-group-item-action personnel-item text-uppercase"
                                                data-id="{{ $personnel->personnel_id }}"
                                                data-name="{{ $personnel->personnel_name }}"
                                                data-branch="{{ $personnel->branch->branch_name ?? 'N/A' }}"
                                                data-dept="{{ $personnel->branch->branch_department ?? 'N/A' }}">
                                                <div class="d-flex w-100 justify-content-between align-items-center">
                                                    <h6 class="mb-1 text-truncate text-uppercase"
                                                        style="max-width: 150px;">
                                                        {{ $personnel->personnel_name }}</h6>

                                                </div>
                                                <p class="mb-0 small text-muted text-uppercase">
                                                    <i
                                                        class="bi bi-building me-1"></i>{{ $personnel->branch->branch_name ?? 'N/A' }}
                                                    |
                                                    <i
                                                        class="bi bi-diagram-3 me-1"></i>{{ $personnel->branch->branch_department ?? 'N/A' }}
                                                </p>
                                            </button>
                                        @endforeach
                                    </div>

                                    <div class="invalid-feedback mt-2 text-uppercase" id="personnelError">
                                        <i class="bi bi-exclamation-circle"></i> PLEASE SELECT A PERSONNEL.
                                    </div>

                                    <div class="mt-3 p-3 bg-light rounded-3 border d-none" id="selectedPersonnelCard">
                                        <p class="small text-muted mb-1 text-uppercase fw-bold"
                                            style="font-size: 0.7rem;">ASSIGNED TO:</p>
                                        <h6 class="mb-0 text-primary text-uppercase" id="display_personnel_name"></h6>
                                        <div class="small text-muted mt-1 text-uppercase" style="font-size: 0.8rem;">
                                            <span id="display_branch"></span> &bull; <span id="display_dept"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 border-end pe-md-3">
                                    <h6 class="text-muted mb-3 text-uppercase"><i class="bi bi-box-seam me-1"></i> 2.
                                        SELECT ITEM
                                    </h6>

                                    <div class="input-group mb-2">
                                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                        <input type="text" id="itemSearch" class="form-control text-uppercase"
                                            placeholder="SEARCH ITEM, BRAND, OR SN...">
                                    </div>

                                    <div class="list-group list-group-flush border rounded-3"
                                        style="max-height: 250px; overflow-y: auto;" id="itemList">

                                        @php
                                            // 🔥 Push items with 0 quantity to the bottom of the list
                                            $sortedItems = $items->sortBy(
                                                fn($item) => ($item->item_quantity_remaining ?? 0) <= 0 ? 1 : 0,
                                            );
                                        @endphp

                                        @foreach ($sortedItems as $item)
                                            @continue($item->item_remark === 'Damaged' || $item->item_remark === 'Missing')
                                            @php
                                                $qty = $item->item_quantity_remaining ?? 0;
                                                $isOutOfStock = $qty <= 0;
                                            @endphp
                                            <button type="button"
                                                class="list-group-item list-group-item-action item-btn text-uppercase {{ $isOutOfStock ? 'disabled bg-light opacity-75' : '' }}"
                                                data-id="{{ $item->item_id }}" data-name="{{ $item->item_name }}"
                                                data-brand="{{ $item->brand->item_brand_name ?? 'N/A' }}"
                                                data-category="{{ $item->category->item_category_name ?? 'N/A' }}"
                                                data-serial="{{ $item->item_serialno ?? '-' }}"
                                                data-uom="{{ $item->uom->item_uom_name ?? 'Pcs' }}"
                                                data-qty="{{ $qty }}" {{ $isOutOfStock ? 'disabled' : '' }}>

                                                <div
                                                    class="d-flex w-100 justify-content-between align-items-center mb-1">
                                                    <h6 class="mb-0 text-truncate text-uppercase"
                                                        style="max-width: 140px; font-size: 0.95rem;">
                                                        {{ $item->item_name }}</h6>
                                                    <span
                                                        class="badge text-uppercase {{ $isOutOfStock ? 'bg-danger' : 'bg-success' }}">
                                                        {{ $qty }} LEFT
                                                    </span>
                                                </div>

                                                <div class="d-flex flex-wrap gap-2 mb-1 text-uppercase"
                                                    style="font-size: 0.75rem;">
                                                    <span class="text-muted"><i
                                                            class="bi bi-tag-fill me-1"></i>{{ $item->brand->item_brand_name ?? '-' }}</span>
                                                    <span class="text-muted"><i
                                                            class="bi bi-upc-scan me-1"></i>{{ $item->item_serialno ?? 'N/A' }}</span>
                                                </div>
                                                <div class="text-muted text-uppercase" style="font-size: 0.7rem;">
                                                    ITEM TYPE: {{ $item->category->item_category_name ?? 'N/A' }}
                                                    &bull; UOM:
                                                    {{ $item->uom->item_uom_name ?? '-' }}
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>

                                    <div class="invalid-feedback mt-2 text-uppercase" id="itemError">
                                        <i class="bi bi-exclamation-circle"></i> PLEASE SELECT AN ITEM.
                                    </div>

                                    <div class="mt-3 p-3 bg-light rounded-3 border d-none" id="selectedItemCard">
                                        <p class="small text-muted mb-1 text-uppercase fw-bold"
                                            style="font-size: 0.7rem;">ITEM TO ISSUE:</p>
                                        <h6 class="mb-0 text-success text-uppercase" id="display_item_name"></h6>
                                        <div class="small text-muted mt-1 text-uppercase" style="font-size: 0.8rem;">
                                            <span id="display_item_brand"></span> &bull; SN: <span
                                                id="display_item_sn"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <h6 class="text-muted mb-3 text-uppercase"><i class="bi bi-card-text me-1"></i> 3.
                                        ISSUANCE
                                        DETAILS</h6>

                                    <div class="form-floating mb-3">
                                        <input type="number" name="personnel_item_quantity"
                                            class="form-control text-uppercase" id="personnel_item_quantity"
                                            placeholder="QUANTITY" min="1" disabled required>
                                        <label for="personnel_item_quantity" class="text-uppercase">QUANTITY TO
                                            ISSUE</label>
                                        <div class="invalid-feedback text-uppercase" id="qtyErrorText">
                                            PLEASE SELECT AN ITEM FIRST.
                                        </div>
                                        <div class="form-text text-success d-none mt-1 text-uppercase"
                                            id="qtyAvailableText"></div>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <input type="date" name="personnel_date_issued"
                                            class="form-control text-uppercase" id="personnel_date_issued" required>
                                        <label for="personnel_date_issued" class="text-uppercase">DATE ISSUED</label>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <select name="personnel_item_remarks" class="form-select text-uppercase"
                                            id="personnel_item_remarks" required>
                                            <option value="" disabled selected>SELECT REMARK</option>
                                            <option value="Received">RECEIVED</option>
                                            <option value="Not Receive">NOT RECEIVE</option>
                                            {{-- <option value="To be delivered">TO BE DELIVERED</option> --}}
                                        </select>
                                        <label for="personnel_item_remarks" class="text-uppercase">REMARKS</label>
                                        <div class="invalid-feedback text-uppercase">REMARK IS REQUIRED.</div>
                                    </div>

                                    <div class="form-floating mb-3" id="receive_date_container"
                                        style="display:none;">
                                        <input type="date" name="personnel_date_receive"
                                            class="form-control text-uppercase" id="personnel_date_receive">
                                        <label for="personnel_date_receive" class="text-uppercase">RECEIVE
                                            DATE</label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary text-uppercase"
                                data-bs-dismiss="modal">CANCEL</button>
                            <button type="submit" class="btn btn-success px-4 text-uppercase" id="submitBtn">RECORD
                                OUTBOUND</button>
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
    <script src="/storage/js/personnel/personnel.js"></script>


</x-app-layout>
