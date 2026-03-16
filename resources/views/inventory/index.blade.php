<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">

        </h2>
    </x-slot>
    <style>
    </style>

    <body>
        <div class="inventory_form container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Buttons on the left -->
                <div class="d-flex flex-column gap-2">
                    <!-- Top row buttons -->
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal"
                            data-bs-target="#category_modal">
                            Create Category
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#item_modal">
                            Add New Item
                        </button>
                    </div>

                    <!-- Delete button below -->
                    <button id="bulk_delete_btn" class="btn btn-danger" disabled>
                        <i class="bi bi-trash"></i>
                        Delete Selected
                    </button>
                </div>


                <!-- Right Column: Search, Filters, Export -->
                <div class="w-5">
                    <!-- Top Row: Search Bar -->
                    <div class="mb-2">
                        <div class="input-group w-100">
                            <input type="text" id="inventorySearch" class="form-control"
                                placeholder="Search item...">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Middle Row: Dropdown Filters (Horizontal) -->
                    <div class="d-flex gap-2 mb-3">
                        <select name="remark" class="form-select form-select-sm" style="min-width: 120px;">
                            <option value="">All Remarks</option>
                            @foreach ($item_remarks as $remark)
                                <option value="{{ $remark }}"
                                    {{ request('remark') == $remark ? 'selected' : '' }}>
                                    {{ $remark }}
                                </option>
                            @endforeach
                        </select>

                        <select name="category" class="form-select form-select-sm" id="categoryFilter"
                            style="min-width: 120px;">
                            <option value="">All Categories</option>
                            @foreach ($item_categories as $category)
                                <option value="{{ $category->item_category_id }}"
                                    {{ request('category') == $category->item_category_id ? 'selected' : '' }}>
                                    {{ $category->item_category_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="brand" class="form-select form-select-sm" id="brandFilter"
                            style="min-width: 120px;">
                            <option value="">All Brands</option>
                            @foreach ($item_brands as $brand)
                                <option value="{{ $brand->item_brand_id }}"
                                    {{ request('brand') == $brand->item_brand_id ? 'selected' : '' }}>
                                    {{ $brand->item_brand_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bottom Row: Export Buttons -->
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('inventory.index', ['export' => 'pdf', 'search' => request('search')]) }}"
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

            <!-- Modal -->
            <div class="modal fade" id="category_modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">Create Category</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- Modal Form -->
                        <form action="{{ route('item-category.store') }}" method="POST" class="needs-validation"
                            novalidate>
                            @csrf

                            <!-- Modal Body -->
                            <div class="modal-body">

                                <!-- Category Input -->
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="category_name"
                                        name="item_category_name" placeholder="Enter Category" required>
                                    <label for="category_name">Category Name</label>
                                    <div class="invalid-feedback">
                                        Please enter a category name.
                                    </div>
                                </div>

                                <!-- Category Dropdown -->
                                <div class="mb-3">
                                    <label class="form-label">Show all Category:</label>
                                    <select name="item_category_id" class="form-control">
                                        @foreach ($item_categories as $category)
                                            <option value="{{ $category->item_category_id }}">
                                                {{ $category->item_category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-light text-dark"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>


            <div class="modal fade" id="item_modal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">Add New Item</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- Modal body -->
                        <form action="{{ route('inventory.store') }}" method="POST" class="needs-validation"
                            novalidate>
                            @csrf

                            <div class="modal-body">

                                <!-- Item Name (required) -->
                                <div class="form-floating mb-3">
                                    <input type="text" name="item_name" class="form-control" id="item_name"
                                        placeholder="Item Name" required>
                                    <label for="item_name">Item Name</label>
                                    <div class="invalid-feedback">
                                        Item Name is required.
                                    </div>
                                </div>

                                <!-- Serial Number (optional) -->
                                <div class="form-floating mb-3">
                                    <input type="text" name="item_serialno" class="form-control"
                                        id="item_serialno" placeholder="Serial Number">
                                    <label for="item_serialno">Serial Number (Optional)</label>
                                </div>

                                <!-- Unit of Measure (optional) -->
                                <div class="form-floating mb-3">
                                    <select name="item_uom_name" class="form-select" id="item_uom_name">
                                        <option value="" disabled>Select Unit of Measure</option>
                                        <option value="Pcs">Pcs</option>
                                        <option value="Set">Set</option>
                                        <option value="Box">Box</option>
                                        <option value="Roll">Roll</option>
                                        <option value="Pack">Pack</option>
                                        <option value="Pair">Pair</option>
                                    </select>
                                    <label for="item_uom_name">Unit of Measure</label>
                                </div>

                                <!-- Quantity (optional) -->
                                <div class="form-floating mb-3">
                                    <input type="number" name="item_quantity" class="form-control"
                                        id="item_quantity" placeholder="Quantity" min="1" max="99"
                                        required oninput="this.value=this.value.slice(0,4)">
                                    <label for="item_quantity">Quantity</label>
                                    <div class="invalid-feedback">
                                        Quantity is required.
                                    </div>
                                </div>

                                <!-- Remark (optional) -->
                                <div class="form-floating mb-3">
                                    <select name="item_remark" class="form-select" id="item_remark" required>
                                        <option value="" disabled>Select Remark</option>
                                        <option value="Good">Good</option>
                                        <option value="Damaged">Damaged</option>
                                        <option value="Missing">Missing</option>
                                    </select>
                                    <label for="item_remark">Remark</label>
                                </div>

                                <!-- Category (required) -->
                                <div class="form-floating mb-3">
                                    <select name="item_category_id" class="form-select" id="item_category_id"
                                        required>
                                        <option value="" disabled>Select Category</option>
                                        @foreach ($item_categories as $category)
                                            <option value="{{ $category->item_category_id }}">
                                                {{ $category->item_category_name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="item_category_id">Category</label>
                                    <div class="invalid-feedback">
                                        Category is required.
                                    </div>
                                </div>

                                <!-- Brand (optional) -->
                                <div class="form-floating mb-3">
                                    <select name="item_brand_name" class="form-select" id="item_brand_name" required>
                                        <option value="" disabled selected>Select Brand</option>
                                        <option value="Logitech">Logitech</option>
                                        <option value="Microsoft">Microsoft</option>
                                        <option value="HP">HP</option>
                                        <option value="Dell">Dell</option>
                                        <option value="Corsair">Corsair</option>

                                        <option value="belkin" disabled>Networking Equipment</option>
                                        <option value="TP-Link">TP-Link</option>
                                        <option value="Cisco">Cisco</option>
                                        <option value="Netgear">Netgear</option>
                                        <option value="Ubiquiti">Ubiquiti</option>

                                        <option value="belkin" disabled>Storage Devices</option>
                                        <option value="Seagate">Seagate</option>
                                        <option value="Western Digital (WD)">Western Digital (WD)</option>
                                        <option value="Samsung">Samsung</option>
                                        <option value="Kingston">Kingston</option>

                                        <option value="belkin" disabled>Computer Components </option>
                                        <option value="Intel">Intel</option>
                                        <option value="AMD">AMD</option>
                                        <option value="Nvidia">Nvidia</option>
                                        <option value="ASUS">ASUS</option>
                                        <option value="MSI">MSI</option>
                                        <option value="Gigabyte">Gigabyte</option>

                                        <option value="belkin" disabled>Printers & Scanners</option>
                                        <option value="Canon">Canon</option>
                                        <option value="Epson">Epson</option>
                                        <option value="Brother">Brother</option>

                                        <option value="belkin" disabled>Mobile Devices</option>
                                        <option value="Apple">Apple</option>
                                        <option value="Samsung">Samsung</option>
                                        <option value="Xiaomi">Xiaomi</option>
                                        <option value="Lenovo">Lenovo</option>
                                        <option value="Huawei">Huawei</option>

                                        <option value="belkin" disabled>Cables & Accessories</option>
                                        <option value="Belkin">Belkin</option>
                                        <option value="UGREEN">UGREEN</option>
                                        <option value="Anker">Anker</option>
                                        <option value="AmazonBasics">AmazonBasics</option>
                                    </select>
                                    <label for="item_brand_name">Brand</label>
                                    <div class="invalid-feedback">
                                        Brand is required.
                                    </div>
                                </div>



                            </div> <!-- /.modal-body -->

                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-light text-dark"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <table class="table table-striped">
                <thead class="table-light">
                    <tr>
                        <th>
                            <input type="checkbox" id="select_all">
                        </th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Brand Name</th>
                        <th>Serial Number</th>
                        <th>Unit of Measure</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="table-data">
                    @include('inventory.inventory-table')
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-3">
                {{ $items->links('pagination::bootstrap-4') }}
            </div>
    </body>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    fetch("{{ route('inventory.bulkDelete') }}", {
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
        document.addEventListener("DOMContentLoaded", function() {
            const searchInput = document.getElementById("inventorySearch");
            const remarkSelect = document.querySelector("select[name='remark']");
            const categorySelect = document.querySelector("select[name='category']");
            const brandSelect = document.querySelector("select[name='brand']");
            const tableBody = document.getElementById("table-data");

            if (!searchInput || !tableBody || !remarkSelect || !categorySelect || !brandSelect) return;

            let timer;

            function fetchTable() {
                const query = searchInput.value.trim();
                const remark = remarkSelect.value;
                const category = categorySelect.value;
                const brand = brandSelect.value; // include brand

                const colCount = tableBody.closest("table").querySelectorAll("thead th").length;

                tableBody.innerHTML = `<tr>
            <td colspan="${colCount}" class="text-center text-muted" style="font-size:15px;font-weight:bold; color:gray;">
                Loading...
            </td>
        </tr>`;

                clearTimeout(timer);
                timer = setTimeout(() => {
                    // Include brand in the URL
                    const url =
                        `{{ route('inventory.index') }}?search=${encodeURIComponent(query)}&remark=${encodeURIComponent(remark)}&category=${encodeURIComponent(category)}&brand=${encodeURIComponent(brand)}&ajax=1`;

                    fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error("Server error: " + res.status);
                            return res.text();
                        })
                        .then(html => {
                            tableBody.innerHTML = html;
                        })
                        .catch(err => {
                            console.error(err);
                            tableBody.innerHTML = `<tr>
                    <td colspan="${colCount}" class="text-center text-danger" style="font-size:15px;font-weight:bold;">
                        Failed to load data.
                    </td>
                </tr>`;
                        });
                }, 300);
            }

            searchInput.addEventListener("keyup", fetchTable);
            remarkSelect.addEventListener("change", fetchTable);
            categorySelect.addEventListener("change", fetchTable);
            brandSelect.addEventListener("change", fetchTable);
        });
    </script>
    <script>
        $(document).ready(function() {
            // -------------------------
            // AJAX Duplicate Check on Submit
            // -------------------------
            $('form.needs-validation').on('submit', function(e) {
                e.preventDefault(); // prevent default submission

                let form = this;

                // Only proceed if Bootstrap validation passed
                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    return;
                }

                let item_name = $('#item_name').val();
                let item_uom_name = $('#item_uom_name').val();
                let item_brand_name = $('#item_brand_name').val();
                let item_remark = $('#item_remark').val();

                $.ajax({
                    url: '{{ route('inventory.checkDuplicate') }}', // route to check duplicates
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        item_name: item_name,
                        item_uom_name: item_uom_name,
                        item_brand_name: item_brand_name,
                        item_remark: item_remark
                    },
                    success: function(response) {
                        if (response.exists) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Duplicate Item!',
                                text: 'This item already exists. Please check name, brand, UOM, and remark.',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            // No duplicate, submit the form
                            form.submit();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Something went wrong. Please try again.',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });

            // -------------------------
            // BOOTSTRAP FIELD VALIDATION
            // -------------------------
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

            // -------------------------
            // SWEETALERT DELETE CONFIRM
            // -------------------------
            const deleteForms = document.querySelectorAll('form input[name="_method"][value="DELETE"]');

            deleteForms.forEach(input => {
                const form = input.closest('form');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This item will be deleted.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

        });
    </script>

    <script>
        // -------------------------
        // EXPORT SELECTED TO EXCEL
        // -------------------------
        document.getElementById('export_excel_btn').addEventListener('click', function(e) {
            e.preventDefault();

            const checkboxes = document.querySelectorAll('.select_item:checked');
            const selectedIds = Array.from(checkboxes).map(cb => cb.value);

            let url = "{{ route('inventory.index', ['export' => 'excel']) }}";

            const searchInput = document.getElementById("inventorySearch");
            if (searchInput && searchInput.value) {
                url += "&search=" + encodeURIComponent(searchInput.value);
            }

            if (selectedIds.length > 0) {
                url += "&ids=" + selectedIds.join(',');
            }

            window.open(url, '_blank');
        });
    </script>

    {{-- sweet alert yes or no on the update modal --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const updateForms = document.querySelectorAll(".needs-validation-update");

            updateForms.forEach(function(form) {

                form.addEventListener("submit", function(e) {
                    e.preventDefault(); // stop normal submit

                    Swal.fire({
                        title: "Update Item?",
                        text: "Are you sure you want to update this item?",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, update it!",
                        cancelButtonText: "No"
                    }).then((result) => {

                        if (result.isConfirmed) {
                            form.submit(); // submit the form if yes
                        }

                    });

                });

            });

        });
    </script>
</x-app-layout>
