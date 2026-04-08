<x-app-layout>
    <x-slot name="header">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">

        </h2>
    </x-slot>
    <style>
    </style>

    <body>
        <div class="inventory_form container-fluid px-4 mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal"
                            data-bs-target="#category_modal">
                            <i class="bi bi-plus-circle"></i>
                            Create Category
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#item_modal">
                            <i class="bi bi-plus-square"></i>
                            Add New Item
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
                            <input type="text" id="inventorySearch" class="form-control"
                                placeholder="Search item...">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mb-3">
                        <select name="remark" class="form-select form-select-sm" style="min-width: 120px;">
                            <option value="" disabled>Select Remark </option>
                            <option value="">Remarks </option>
                            @foreach ($item_remarks as $remark)
                                <option value="{{ $remark }}"
                                    {{ request('remark') == $remark ? 'selected' : '' }}>
                                    {{ $remark }}
                                </option>
                            @endforeach
                        </select>

                        <select name="category" class="form-select form-select-sm" id="categoryFilter"
                            style="min-width: 120px;">
                            <option value="" disabled>Select Category </option>
                            <option value="">Categories </option>
                            @foreach ($item_categories as $category)
                                <option value="{{ $category->item_category_id }}"
                                    {{ request('category') == $category->item_category_id ? 'selected' : '' }}>
                                    {{ $category->item_category_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="brand" class="form-select form-select-sm" id="brandFilter"
                            style="min-width: 120px;">
                            <option value="" disabled>Select Brand</option>
                            <option value="">Brands </option>
                            @foreach ($item_brands as $brand)
                                <option value="{{ $brand->item_brand_id }}"
                                    {{ request('brand') == $brand->item_brand_id ? 'selected' : '' }}>
                                    {{ $brand->item_brand_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

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

            <div class="modal fade" id="category_modal" tabindex="-1">
                <div class="modal-dialog modal-lg ">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h4 class="modal-title">Manage Categories</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row">

                                <!-- ================= LEFT: FORM ================= -->
                                <div class="col-md-6 border-end">

                                    <form action="{{ route('item-category.store') }}" method="POST"
                                        class="needs-validation category-form" novalidate>
                                        @csrf

                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="category_name"
                                                name="item_category_name" placeholder="Enter Category" required>
                                            <label for="category_name">Category Name</label>
                                            <div class="invalid-feedback">
                                                Please enter a category name.
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Category Icon</label>

                                            <div class="dropdown w-100">
                                                <button class="btn btn-outline-secondary w-100 text-start"
                                                    type="button" id="iconDropdown" data-bs-toggle="dropdown">
                                                    <i id="selectedIcon" class="bi"></i>
                                                    <span id="selectedIconText">None</span>
                                                </button>

                                                <ul class="dropdown-menu w-100"
                                                    style="max-height: 250px; overflow-y: auto;">
                                                    <!-- NONE -->
                                                    <li>
                                                        <a class="dropdown-item icon-option" href="#"
                                                            data-value="">
                                                            None
                                                        </a>
                                                    </li>


                                                    <!-- COMPUTERS -->
                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-pc-display">
                                                            <i class="bi bi-pc-display me-2"></i> Desktop PC
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-laptop">
                                                            <i class="bi bi-laptop me-2"></i> Laptop
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-display">
                                                            <i class="bi bi-display me-2"></i> Monitor
                                                        </a></li>

                                                    <!-- HARDWARE -->
                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-cpu">
                                                            <i class="bi bi-cpu me-2"></i> CPU / Processor
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-hdd">
                                                            <i class="bi bi-hdd me-2"></i> Hard Drive
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-memory">
                                                            <i class="bi bi-memory me-2"></i> RAM / Memory
                                                        </a></li>

                                                    <!-- PERIPHERALS -->
                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-keyboard">
                                                            <i class="bi bi-keyboard me-2"></i> Keyboard
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-mouse">
                                                            <i class="bi bi-mouse me-2"></i> Mouse
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-printer">
                                                            <i class="bi bi-printer me-2"></i> Printer
                                                        </a></li>

                                                    <!-- NETWORK -->
                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-router">
                                                            <i class="bi bi-router me-2"></i> Router
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-wifi">
                                                            <i class="bi bi-wifi me-2"></i> WiFi / Network
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-hdd-network">
                                                            <i class="bi bi-hdd-network me-2"></i> Server / NAS
                                                        </a></li>

                                                    <!-- MOBILE -->
                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-phone">
                                                            <i class="bi bi-phone me-2"></i> Mobile Phone
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-tablet">
                                                            <i class="bi bi-tablet me-2"></i> Tablet
                                                        </a></li>

                                                    <!-- GENERAL -->
                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-box-seam">
                                                            <i class="bi bi-box-seam me-2"></i> General Item
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option" href="#"
                                                            data-value="bi-tools">
                                                            <i class="bi bi-tools me-2"></i> Tools / Repair
                                                        </a></li>

                                                </ul>
                                            </div>

                                            <input type="hidden" name="item_category_icon" id="category_icon">
                                        </div>



                                        <button type="submit" class="btn btn-success w-100">Create Category</button>

                                    </form>
                                </div>

                                <!-- ================= RIGHT: LIST ================= -->
                                <div class="col-md-6">

                                    <!-- CATEGORY LIST LABEL -->
                                    <div class="mb-2">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-list-ul me-1"></i> Category List
                                        </label>
                                    </div>

                                    <!-- SEARCH (FLOATING STYLE) -->
                                    <div class="form-floating mb-3">
                                        <input type="text" id="categorySearch" class="form-control"
                                            placeholder="Search category...">
                                        <label for="categorySearch">
                                            <i class="bi bi-search me-1"></i> Search Category
                                        </label>
                                    </div>

                                    <!-- CATEGORY LIST -->
                                    <ul class="list-group shadow-sm rounded" id="categoryList"
                                        style="max-height: 260px; overflow-y: auto;">
                                        @foreach ($item_categories as $category)
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center category-item">

                                                <span class="d-flex align-items-center">
                                                    @if ($category->item_category_icon)
                                                        <i
                                                            class="bi {{ $category->item_category_icon }} me-2 fs-5"></i>
                                                    @endif
                                                    {{ $category->item_category_name }}
                                                </span>

                                                <!-- DELETE -->
                                                <form
                                                    action="{{ route('item-category.destroy', $category->item_category_id) }}"
                                                    method="POST" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        ✕
                                                    </button>
                                                </form>

                                            </li>
                                        @endforeach


                                    </ul>

                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="item_modal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h4 class="modal-title">Add New Item</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form action="{{ route('inventory.store') }}" method="POST"
                            class="needs-validation item-form" novalidate>
                            @csrf

                            <div class="modal-body">
                                <div class="row">

                                    <!-- LEFT SIDE -->
                                    <div class="col-md-6 border-end">
                                        <h6 class="text-muted mb-3">
                                            <i class="bi bi-info-circle me-1"></i> Item Identification
                                        </h6>

                                        <!-- CATEGORY -->
                                        <div class="form-floating mb-3 position-relative">

                                            <!-- ICON -->
                                            <i id="categoryIconPreview"
                                                class="bi position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>

                                            <select name="item_category_id" class="form-select ps-5"
                                                id="item_category_id" required>

                                                <option value="" disabled selected>Select Category</option>

                                                @foreach ($item_categories as $category)
                                                    <option value="{{ $category->item_category_id }}"
                                                        data-name="{{ $category->item_category_name }}"
                                                        data-icon="{{ $category->item_category_icon }}">
                                                        {{ $category->item_category_name }}
                                                    </option>
                                                @endforeach

                                            </select>

                                            <label for="item_category_id" class="ms-4">Category</label>
                                        </div>

                                        <!-- ITEM NAME -->
                                        <div class="form-floating mb-3">
                                            <input type="text" name="item_name" class="form-control"
                                                id="item_name" placeholder="Item Name" required>
                                            <label for="item_name">Item Name</label>
                                        </div>

                                        <!-- BRAND -->
                                        {{-- <div class="form-floating mb-3">
                                            <select name="item_brand_name" class="form-select" id="item_brand_name"
                                                >
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
                                        </div> --}}

                                        <!-- SERIAL -->
                                        <div class="form-floating mb-3">
                                            <input type="text" name="item_serialno" class="form-control"
                                                id="item_serialno" placeholder="Serial Number">
                                            <label for="item_serialno">Serial Number (Optional)</label>
                                        </div>
                                    </div>

                                    <!-- RIGHT SIDE -->
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">
                                            <i class="bi bi-box-seam me-1"></i> Stock Information
                                        </h6>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-floating mb-3">
                                                    <input type="number" name="item_quantity" class="form-control"
                                                        id="item_quantity" min="1" required>
                                                    <label>Quantity</label>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-floating mb-3">
                                                    <select name="item_uom_name" class="form-select"
                                                        id="item_uom_name" required>
                                                        <option value="" disabled selected>Select UOM</option>
                                                        <option value="Pcs">Pcs</option>
                                                        <option value="Set">Set</option>
                                                        <option value="Box">Box</option>
                                                        <option value="Roll">Roll</option>
                                                        <option value="Pack">Pack</option>
                                                        <option value="Pair">Pair</option>
                                                    </select>
                                                    <label>Unit of Measure</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-floating mb-3">
                                            <select name="item_remark" class="form-select" id="item_remark" required>
                                                <option value="" disabled selected>Select Remark</option>
                                                <option value="Good">Good</option>
                                                <option value="Damaged">Damaged</option>
                                                <option value="Missing">Missing</option>
                                            </select>
                                            <label>Condition / Remark</label>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success px-4">Save Item</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

            <div class="table-responsive w-100">
                <table class="table table-striped w-100">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <input type="checkbox" id="select_all" >
                            </th>
                            <th>Product Name</th>
                            <th>Category</th>
                            {{-- <th>Brand Name</th> --}}
                            <th>Serial Number</th>
                            <th>Unit of Measure</th>
                            <th>Total Item</th>
                            <th>Quantity Remaining</th>
                            {{-- <th>Quantity Status</th> --}}
                            <th>Item Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="table-data">
                        @include('inventory.inventory-table')
                    </tbody>
                </table>

            </div>
        </div>

    </body>
    <script>
        window.itemsData = @json(
            $items->map(function ($item) {
                return [
                    'name' => $item->item_name,
                    'category_id' => $item->item_category_id,
                ];
            }));

        window.routes = {
            inventoryIndex: "{{ route('inventory.index') }}",
            checkDuplicate: "{{ route('inventory.checkDuplicate') }}",
            bulkDelete: "{{ route('inventory.bulkDelete') }}"
        };

        window.csrfToken = "{{ csrf_token() }}";
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="storage/js/inventory/inventory.js"></script>

</x-app-layout>
