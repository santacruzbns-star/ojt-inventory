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
                                <option value="{{ $b->branch_id }}"
                                    {{ request('branch') == $b->branch_id ? 'selected' : '' }}>
                                    {{ $b->branch_name }}
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
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title">Add Personnel</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('personnels.store') }}" method="POST" class="needs-validation" novalidate>
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
                                    <option value="Gold Town">Gold Town</option>
                                    <option value="Edison Branch">Edison Branch</option>
                                    <option value="Osmeña Branch">Osmeña Branch</option>
                                    <option value="Grainsco Branch">Grainsco Branch</option>
                                </select>
                                <label for="branch_name">Branch Name</label>
                                <div class="invalid-feedback">Please select a branch.</div>
                            </div>

                            <div class="form-floating mb-3">
                                <select name="branch_department" class="form-select" id="branch_department" required>
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
                                            <option value="To be delivered">To be delivered</option>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Form and Error elements
            const form = document.getElementById('outboundForm');
            const submitBtn = document.getElementById('submitBtn');

            // Personnel Elements
            const pSearchInput = document.getElementById('personnelSearch');
            const personnelItems = document.querySelectorAll('.personnel-item');
            const pHiddenInput = document.getElementById('selected_personnel_id');
            const pDisplayCard = document.getElementById('selectedPersonnelCard');
            const pErrorDiv = document.getElementById('personnelError');

            // Item Elements
            const iSearchInput = document.getElementById('itemSearch');
            const itemBtns = document.querySelectorAll('.item-btn');
            const iHiddenInput = document.getElementById('selected_item_id');
            const iDisplayCard = document.getElementById('selectedItemCard');
            const iErrorDiv = document.getElementById('itemError');

            // Quantity Elements
            const qtyInput = document.getElementById('personnel_item_quantity');
            const qtyErrorText = document.getElementById('qtyErrorText');
            const qtyAvailableText = document.getElementById('qtyAvailableText');
            let maxAvailableQty = 0;

            /* =========================================
               1. Generic Search Function
               ========================================= */
            function attachSearch(searchInput, listItems) {
                if (!searchInput) return;
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    listItems.forEach(item => {
                        const textData = item.innerText.toLowerCase();
                        if (textData.includes(searchTerm)) {
                            item.classList.remove('d-none');
                        } else {
                            item.classList.add('d-none');
                        }
                    });
                });
            }

            attachSearch(pSearchInput, personnelItems);
            attachSearch(iSearchInput, itemBtns);

            /* =========================================
               2. Personnel Selection Logic
               ========================================= */
            personnelItems.forEach(item => {
                item.addEventListener('click', function() {
                    personnelItems.forEach(btn => btn.classList.remove('active', 'bg-primary',
                        'text-white'));
                    this.classList.add('active', 'bg-primary', 'text-white');

                    personnelItems.forEach(btn => {
                        const textElements = btn.querySelectorAll(
                            '.text-muted, .text-light');
                        if (btn.classList.contains('active')) {
                            textElements.forEach(el => {
                                el.classList.remove('text-muted');
                                el.classList.add('text-light');
                            });
                        } else {
                            textElements.forEach(el => {
                                el.classList.remove('text-light');
                                el.classList.add('text-muted');
                            });
                        }
                    });

                    pHiddenInput.value = this.getAttribute('data-id');
                    pErrorDiv.classList.remove('d-block');

                    document.getElementById('display_personnel_name').innerText = this.getAttribute(
                        'data-name');
                    document.getElementById('display_branch').innerText = this.getAttribute(
                        'data-branch');
                    document.getElementById('display_dept').innerText = this.getAttribute(
                        'data-dept');
                    pDisplayCard.classList.remove('d-none');
                });
            });

            /* =========================================
               3. Item Selection & Stock Logic
               ========================================= */
            itemBtns.forEach(item => {
                item.addEventListener('click', function() {
                    if (this.hasAttribute('disabled')) return; // Extra safety check

                    itemBtns.forEach(btn => btn.classList.remove('active', 'bg-success',
                        'text-white'));
                    this.classList.add('active', 'bg-success', 'text-white');

                    itemBtns.forEach(btn => {
                        const textElements = btn.querySelectorAll(
                            '.text-muted, .text-light');
                        if (btn.classList.contains('active')) {
                            textElements.forEach(el => {
                                el.classList.remove('text-muted');
                                el.classList.add('text-light');
                            });
                        } else {
                            textElements.forEach(el => {
                                el.classList.remove('text-light');
                                el.classList.add('text-muted');
                            });
                        }
                    });

                    iHiddenInput.value = this.getAttribute('data-id');
                    iErrorDiv.classList.remove('d-block');

                    // Update Selected Item Card
                    document.getElementById('display_item_name').innerText = this.getAttribute(
                        'data-name');
                    document.getElementById('display_item_brand').innerText = this.getAttribute(
                        'data-brand');
                    document.getElementById('display_item_sn').innerText = this.getAttribute(
                        'data-serial');
                    iDisplayCard.classList.remove('d-none');

                    // --- Update Quantity Validation Rules ---
                    maxAvailableQty = parseInt(this.getAttribute('data-qty'));

                    qtyInput.disabled = false;
                    qtyInput.max = maxAvailableQty;
                    qtyInput.value = 1; // Auto-fill 1 for convenience
                    qtyInput.classList.remove('is-invalid');

                    qtyAvailableText.classList.remove('d-none');
                    qtyAvailableText.innerText =
                        `Max available: ${maxAvailableQty} ${this.getAttribute('data-uom')}`;
                    submitBtn.disabled = false;
                });
            });

            /* =========================================
               4. Quantity Input Live Validation
               ========================================= */
            if (qtyInput) {
                qtyInput.addEventListener('input', function() {
                    const currentVal = parseInt(this.value);

                    if (!iHiddenInput.value) {
                        this.classList.add('is-invalid');
                        qtyErrorText.innerText = `Please select an item first.`;
                        submitBtn.disabled = true;
                    } else if (currentVal > maxAvailableQty) {
                        this.classList.add('is-invalid');
                        qtyErrorText.innerText = `Cannot exceed stock limit (${maxAvailableQty}).`;
                        submitBtn.disabled = true;
                    } else if (currentVal < 1 || isNaN(currentVal)) {
                        this.classList.add('is-invalid');
                        qtyErrorText.innerText = `Quantity must be at least 1.`;
                        submitBtn.disabled = true;
                    } else {
                        this.classList.remove('is-invalid');
                        submitBtn.disabled = false;
                    }
                });
            }

            /* =========================================
               5. Form Final Submit Validation
               ========================================= */
            if (form) {
                form.addEventListener('submit', function(event) {
                    let isValid = true;

                    // Check Personnel
                    if (!pHiddenInput.value) {
                        pErrorDiv.classList.add('d-block');
                        isValid = false;
                    }

                    // Check Item
                    if (!iHiddenInput.value) {
                        iErrorDiv.classList.add('d-block');
                        isValid = false;
                    }

                    // Check Quantity
                    const finalQty = parseInt(qtyInput.value);
                    if (!iHiddenInput.value || finalQty > maxAvailableQty || finalQty < 1 || isNaN(
                            finalQty)) {
                        qtyInput.classList.add('is-invalid');
                        isValid = false;
                    }

                    if (!isValid) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                });
            }

            /* =========================================
               6. Remarks Logic for "Received" Date
               ========================================= */
            const remarkSelect = document.getElementById('personnel_item_remarks');
            const receiveDateContainer = document.getElementById('receive_date_container');
            const receiveDateInput = document.getElementById('personnel_date_receive');

            if (remarkSelect && receiveDateContainer && receiveDateInput) {
                remarkSelect.addEventListener('change', function() {
                    if (this.value === 'Received') {
                        receiveDateContainer.style.display = 'block';
                        receiveDateInput.setAttribute('required', 'required');
                    } else {
                        receiveDateContainer.style.display = 'none';
                        receiveDateInput.removeAttribute('required');
                        receiveDateInput.value = '';
                    }
                });
            }
        });
    </script>
    {{-- search filtering --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById("OutboundSearch");
            const personnelSelect = document.querySelector("select[name='personnel']");
            const departmentSelect = document.querySelector("select[name='department']");
            const branchSelect = document.querySelector("select[name='branch']");
            const remarksSelect = document.querySelector("select[name='remarks']");
            const tableBody = document.getElementById("table-data");

            if (!tableBody) return;

            let timer;

            function fetchTable() {
                const search = searchInput ? searchInput.value.trim() : '';
                const personnel = personnelSelect ? personnelSelect.value : '';
                const department = departmentSelect ? departmentSelect.value : '';
                const branch = branchSelect ? branchSelect.value : '';
                const remarks = remarksSelect ? remarksSelect.value : '';
                const colCount = tableBody.closest("table").querySelectorAll("thead th").length;

                tableBody.innerHTML = `
            <tr>
                <td colspan="${colCount}" class="text-center text-muted fw-bold">Loading...</td>
            </tr>
        `;

                clearTimeout(timer);
                timer = setTimeout(() => {
                    const url =
                        `{{ route('outbound.index') }}?search=${encodeURIComponent(search)}&personnel=${encodeURIComponent(personnel)}&department=${encodeURIComponent(department)}&branch=${encodeURIComponent(branch)}&remarks=${encodeURIComponent(remarks)}&ajax=1`;

                    fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        })
                        .then(res => res.text())
                        .then(html => {
                            tableBody.innerHTML = html;
                        })
                        .catch(() => {
                            tableBody.innerHTML = `
                    <tr>
                        <td colspan="${colCount}" class="text-center text-danger fw-bold">Failed to load data.</td>
                    </tr>
                `;
                        });

                }, 300);
            }

            // Listen to all filters
            if (searchInput) searchInput.addEventListener("keyup", fetchTable);
            if (personnelSelect) personnelSelect.addEventListener("change", fetchTable);
            if (departmentSelect) departmentSelect.addEventListener("change", fetchTable);
            if (branchSelect) branchSelect.addEventListener("change", fetchTable);
            if (remarksSelect) remarksSelect.addEventListener("change", fetchTable);
        });
    </script>
    {{-- JS to toggle Receive Date --}}
    <script>
        const selectAll = document.getElementById('select_all');
        const bulkDeleteBtn = document.getElementById('bulk_delete_btn');

        // Handle select all toggle
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.select_item');
            checkboxes.forEach(cb => cb.checked = this.checked);
            bulkDeleteBtn.disabled = !this.checked;
        });

        // Enable/disable bulk delete on individual checkbox change
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('select_item')) {
                const checkboxes = document.querySelectorAll('.select_item');
                bulkDeleteBtn.disabled = ![...checkboxes].some(cb => cb.checked);
                // Also update "select all" checkbox
                const allChecked = [...checkboxes].every(cb => cb.checked);
                selectAll.checked = allChecked;
            }
        });

        // Bulk delete click
        bulkDeleteBtn.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.select_item');
            const selectedIds = [...checkboxes]
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            if (selectedIds.length === 0) return;

            Swal.fire({
                title: `Delete ${selectedIds.length} item(s)?`,
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('outbound.bulkDelete') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                ids: selectedIds
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: `${selectedIds.length} item(s) deleted.`,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', 'Something went wrong.', 'error');
                            }
                        });
                }
            });
        });
    </script>
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

        // Handle Excel Export (Selected or All filtered)
        const exportExcelBtn = document.getElementById('export_excel_btn');
        if (exportExcelBtn) {
            exportExcelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const checkboxes = document.querySelectorAll('.select_item:checked');
                const selectedIds = Array.from(checkboxes).map(cb => cb.value);

                let urlParams = new URLSearchParams(window.location.search);
                urlParams.set('export', 'excel');

                if (selectedIds.length > 0) {
                    urlParams.set('ids', selectedIds.join(','));
                }

                window.location.href = "{{ route('outbound.index') }}?" + urlParams.toString();
            });
        }
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
