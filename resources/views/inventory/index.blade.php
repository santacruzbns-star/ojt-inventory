<x-app-layout>
    <x-slot name="header">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">

        </h2>
    </x-slot>

    <body>
        <div class="inventory_form container-fluid px-4 mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal"
                            data-bs-target="#category_modal">
                            <i class="bi bi-plus-circle"></i>
                            Create Item Type
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#item_modal">
                            <i class="bi bi-plus-square"></i>
                            Add New Item
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
                            <option value="" disabled>Select Item Type </option>
                            <option value="">Item Type </option>
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

            <div class="modal fade" id="category_modal" tabindex="-1">
                <div class="modal-dialog modal-lg ">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h4 class="modal-title">Manage Item Type</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row">

                                <!-- ================= LEFT: FORM ================= -->
                                <div class="col-md-6 border-end">

                                    <form action="{{ route('item-category.store') }}" method="POST"
                                        class="needs-validation category-form text-uppercase" novalidate>
                                        @csrf

                                        <div class="form-floating mb-3 text-uppercase">
                                            <input type="text" class="form-control text-uppercase" id="category_name"
                                                name="item_category_name" placeholder="ENTER ITEM TYPE" required>
                                            <label for="category_name">ITEM TYPE NAME</label>
                                            <div class="invalid-feedback">
                                                PLEASE ENTER A CATEGORY NAME.
                                            </div>
                                        </div>

                                        <div class="mb-3 text-uppercase">
                                            <label class="form-label">ITEM TYPE ICON</label>

                                            <div class="dropdown w-100 text-uppercase">
                                                <button
                                                    class="btn btn-outline-secondary w-100 text-start text-uppercase"
                                                    type="button" id="iconDropdown" data-bs-toggle="dropdown">
                                                    <i id="selectedIcon" class="bi"></i>
                                                    <span id="selectedIconText">NONE</span>
                                                </button>

                                                <ul class="dropdown-menu w-100 text-uppercase"
                                                    style="max-height: 250px; overflow-y: auto;">
                                                    <li>
                                                        <a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="">
                                                            NONE
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-printer">
                                                            <i class="bi bi-printer me-2"></i> PRINTER / SCANNER
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-battery-charging">
                                                            <i class="bi bi-battery-charging me-2"></i> UPS / BATTERY
                                                            BACKUP
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-hdd-stack">
                                                            <i class="bi bi-hdd-stack me-2"></i> NETWORK SWITCH / HUB
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-plug">
                                                            <i class="bi bi-plug me-2"></i> POWER SUPPLY / PSU
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-fingerprint">
                                                            <i class="bi bi-fingerprint me-2"></i> BIOMETRIC DEVICE
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-person-bounding-box">
                                                            <i class="bi bi-person-bounding-box me-2"></i> FACE
                                                            RECOGNITION
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-cpu-fill">
                                                            <i class="bi bi-cpu-fill me-2"></i> MOTHERBOARD / MAINBOARD
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-ethernet">
                                                            <i class="bi bi-ethernet me-2"></i> ETHERNET ADAPTER / NIC
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-droplet-half">
                                                            <i class="bi bi-droplet-half me-2"></i> PRINTER INK / TONER
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-lightning-charge">
                                                            <i class="bi bi-lightning-charge me-2"></i> CHARGER / POWER
                                                            ADAPTER
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-telephone">
                                                            <i class="bi bi-telephone me-2"></i> TELEPHONE / LANDLINE
                                                        </a>
                                                    </li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-pc-display">
                                                            <i class="bi bi-pc-display me-2"></i> DESKTOP PC
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-laptop">
                                                            <i class="bi bi-laptop me-2"></i> LAPTOP
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-display">
                                                            <i class="bi bi-display me-2"></i> MONITOR
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-cpu">
                                                            <i class="bi bi-cpu me-2"></i> CPU / PROCESSOR
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-hdd">
                                                            <i class="bi bi-hdd me-2"></i> HARD DRIVE
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-memory">
                                                            <i class="bi bi-memory me-2"></i> RAM / MEMORY
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-gpu-card">
                                                            <i class="bi bi-gpu-card me-2"></i> GRAPHICS CARD / GPU
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-keyboard">
                                                            <i class="bi bi-keyboard me-2"></i> KEYBOARD
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-mouse">
                                                            <i class="bi bi-mouse me-2"></i> MOUSE
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-headset">
                                                            <i class="bi bi-headset me-2"></i> HEADSET / AUDIO
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-webcam">
                                                            <i class="bi bi-webcam me-2"></i> WEBCAM
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-router">
                                                            <i class="bi bi-router me-2"></i> ROUTER
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-wifi">
                                                            <i class="bi bi-wifi me-2"></i> WIFI / NETWORK
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-hdd-network">
                                                            <i class="bi bi-hdd-network me-2"></i> SERVER / NAS
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-shield-lock">
                                                            <i class="bi bi-shield-lock me-2"></i> FIREWALL / SECURITY
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-phone">
                                                            <i class="bi bi-phone me-2"></i> MOBILE PHONE
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-tablet">
                                                            <i class="bi bi-tablet me-2"></i> TABLET
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-usb-drive">
                                                            <i class="bi bi-usb-drive me-2"></i> USB FLASH DRIVE
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-box-seam">
                                                            <i class="bi bi-box-seam me-2"></i> GENERAL ITEM
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-tools">
                                                            <i class="bi bi-tools me-2"></i> TOOLS / REPAIR
                                                        </a></li>

                                                    <li><a class="dropdown-item icon-option text-uppercase"
                                                            href="#" data-value="bi-diagram-3">
                                                            <i class="bi bi-diagram-3 me-2"></i> INFRASTRUCTURE
                                                        </a></li>

                                                </ul>
                                            </div>

                                            <input type="hidden" name="item_category_icon" id="category_icon">
                                        </div>



                                        <button type="submit" class="btn btn-success w-100 text-uppercase">CREATE
                                            ITEM TYPE</button>

                                    </form>
                                </div>

                                <div class="col-md-6">

                                    <div class="mb-2 text-uppercase">
                                        <label class="form-label fw-bold">
                                            <i class="bi bi-list-ul me-1"></i> ITEM TYPE LIST
                                        </label>
                                    </div>

                                    <div class="form-floating mb-3 text-uppercase">
                                        <input type="text" id="categorySearch" class="form-control text-uppercase"
                                            placeholder="SEARCH CATEGORY...">
                                        <label for="categorySearch">
                                            <i class="bi bi-search me-1"></i> SEARCH ITEM TYPE
                                        </label>
                                    </div>

                                    <ul class="list-group shadow-sm rounded text-uppercase" id="categoryList"
                                        style="max-height: 260px; overflow-y: auto;">
                                        @foreach ($item_categories as $category)
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center category-item text-uppercase">

                                                <span class="d-flex align-items-center text-uppercase">
                                                    @if ($category->item_category_icon)
                                                        <i
                                                            class="bi {{ $category->item_category_icon }} me-2 fs-5"></i>
                                                    @endif
                                                    {{ $category->item_category_name }}
                                                </span>

                                                <form
                                                    action="{{ route('item-category.destroy', $category->item_category_id) }}"
                                                    method="POST" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-danger text-uppercase">
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
                            class="needs-validation item-form text-uppercase" novalidate>
                            @csrf

                            <div class="modal-body">
                                <div class="row">

                                    <div class="col-md-6 border-end">
                                        <h6 class="text-muted mb-3 text-uppercase">
                                            <i class="bi bi-info-circle me-1"></i> ITEM IDENTIFICATION
                                        </h6>

                                        <div class="form-floating mb-3 position-relative">

                                            <i id="categoryIconPreview"
                                                class="bi position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary"></i>

                                            <select name="item_category_id" class="form-select ps-5 text-uppercase"
                                                id="item_category_id" required>
                                                <option value="" disabled selected>SELECT ITEM TYPE</option>

                                                {{-- Added sortByDesc() to the collection --}}
                                                @foreach ($item_categories->sortByDesc('item_category_id') as $category)
                                                    <option value="{{ $category->item_category_id }}"
                                                        data-name="{{ $category->item_category_name }}"
                                                        data-icon="{{ $category->item_category_icon }}"
                                                        class="text-uppercase">
                                                        {{ $category->item_category_name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <label for="item_category_id" class="ms-4 text-uppercase">ITEM
                                                TYPE</label>
                                        </div>

                                        <div class="form-floating mb-3">
                                            <input type="text" name="item_name"
                                                class="form-control text-uppercase" id="item_name"
                                                placeholder="ITEM NAME" required>
                                            <label for="item_name" class="text-uppercase">ITEM NAME</label>
                                        </div>


                                        <div class="form-floating mb-3">
                                            <input type="text" name="item_serialno"
                                                class="form-control text-uppercase" id="item_serialno"
                                                placeholder="SERIAL NUMBER">
                                            <label for="item_serialno" class="text-uppercase">SERIAL NUMBER
                                                (OPTIONAL)</label>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3 text-uppercase">
                                            <i class="bi bi-box-seam me-1"></i> STOCK INFORMATION
                                        </h6>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-floating mb-3">
                                                    <input type="number" name="item_quantity"
                                                        class="form-control text-uppercase" id="item_quantity"
                                                        min="1" required>
                                                    <label class="text-uppercase">QUANTITY</label>
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="form-floating mb-3">
                                                    <select name="item_uom_name" class="form-select text-uppercase"
                                                        id="item_uom_name" required>
                                                        <option value="" disabled selected>SELECT UOM</option>
                                                        <option value="Pcs">PCS</option>
                                                        <option value="Set">SET</option>
                                                        <option value="Box">BOX</option>
                                                        <option value="Roll">ROLL</option>
                                                        <option value="Pack">PACK</option>
                                                        <option value="Pair">PAIR</option>
                                                    </select>
                                                    <label class="text-uppercase">UNIT OF MEASURE</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-floating mb-3">
                                            <select name="item_remark" class="form-select text-uppercase"
                                                id="item_remark" required>
                                                <option value="" disabled selected>SELECT REMARK</option>
                                                <option value="Good">GOOD</option>
                                                <option value="Damaged">DAMAGED</option>
                                                <option value="Missing">MISSING</option>
                                            </select>
                                            <label class="text-uppercase">CONDITION / REMARK</label>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-outline-secondary text-uppercase"
                                    data-bs-dismiss="modal">CANCEL</button>
                                <button type="submit" class="btn btn-success px-4 text-uppercase">SAVE ITEM</button>
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
                                <input type="checkbox" id="select_all">
                            </th>
                            <th>Product Name</th>
                            <th>Item Type</th>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/storage/js/inventory/inventory.js"></script>

</x-app-layout>
