<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">

        </h2>
    </x-slot>
    <style>
    </style>

    <body>
        <div class="inventory_form container mt-4">
            <!-- Button to Open Modal -->
            <button style="font-weight:bold;" type="button" class="btn btn-outline-dark" data-bs-toggle="modal"
                data-bs-target="#category_modal">
                Create Category
            </button>
            <button style="font-weight:bold;" type="button" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#item_modal">
                Add New Item
            </button>

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
                                    <input type="text" name="item_serialno" class="form-control" id="item_serialno"
                                        placeholder="Serial Number">
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
                                    <input type="number" name="item_quantity" class="form-control" id="item_quantity"
                                        placeholder="Quantity" min="1" max="99" required
                                        oninput="this.value=this.value.slice(0,4)">
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

            <!-- Inventory Table -->
            <div class="table-responsive mt-4">
        
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
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
                        @forelse($items as $item)
                            @php
                                $remarkColor = [
                                    'Good' => 'bg-success',
                                    'Damaged' => 'bg-danger',
                                    'Missing' => 'bg-warning text-dark',
                                ];
                            @endphp
                            <tr>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->category ? $item->category->item_category_name : '-' }}</td>
                                <td>{{ $item->brand ? $item->brand->item_brand_name : '-' }}</td>
                                <td>{{ $item->item_serialno }}</td>
                                <td>{{ $item->uom ? $item->uom->item_uom_name : '-' }}</td>
                                <td>{{ $item->item_quantity ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $remarkColor[$item->item_remark] ?? 'bg-secondary' }}">
                                        {{ $item->item_remark ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown position-static">
                                        <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                                            id="actionMenu_{{ $item->item_id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">

                                            <i class="bi bi-three-dots-vertical fs-5"></i>

                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="actionMenu_{{ $item->item_id }}">

                                            <li>
                                                <button type="button" class="dropdown-item text-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewItemModal_{{ $item->item_id }}">
                                                    View
                                                </button>
                                            </li>

                                            <li>
                                                <button type="button" class="dropdown-item" data-bs-toggle="modal"
                                                    data-bs-target="#editItemModal_{{ $item->item_id }}">
                                                    Edit
                                                </button>
                                            </li>

                                            <li>
                                                <form action="{{ route('inventory.destroy', $item->item_id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="dropdown-item text-danger"
                                                        type="submit">Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="modal fade" id="viewItemModal_{{ $item->item_id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">View Item Details</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">

                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item"><strong>Item Name:</strong>
                                                            {{ $item->item_name }}</li>
                                                        <li class="list-group-item"><strong>Category:</strong>
                                                            {{ $item->category ? $item->category->item_category_name : '-' }}
                                                        </li>
                                                        <li class="list-group-item"><strong>Brand:</strong>
                                                            {{ $item->brand ? $item->brand->item_brand_name : '-' }}
                                                        </li>
                                                        <li class="list-group-item"><strong>Serial Number:</strong>
                                                            {{ $item->item_serialno ?? 'N/A' }}</li>
                                                        <li class="list-group-item"><strong>Quantity:</strong>
                                                            {{ $item->item_quantity ?? 'N/A' }}</li>
                                                        <li class="list-group-item"><strong>Remark:</strong>
                                                            {{ ucfirst($item->item_remark) ?? 'N/A' }}</li>
                                                        <li class="list-group-item"><strong>Unit of Measure:</strong>
                                                            {{ $item->uom ? $item->uom->item_uom_name : '-' }}</li>
                                                    </ul>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="editItemModal_{{ $item->item_id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">

                                                <form action="{{ route('inventory.update', $item->item_id) }}"
                                                    method="POST" class="needs-validation-update" novalidate>
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Item</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">

                                                        {{-- Item Name --}}
                                                        <div class="form-floating mb-3">
                                                            <input type="text" class="form-control"
                                                                name="item_name"
                                                                id="edit_item_name_{{ $item->item_id }}"
                                                                value="{{ old('item_name', $item->item_name) }}"
                                                                placeholder="Item Name" required>

                                                            <label>Item Name</label>

                                                            <div class="invalid-feedback">
                                                                Item Name is required.
                                                            </div>
                                                        </div>


                                                        {{-- Serial Number --}}
                                                        <div class="form-floating mb-3">
                                                            <input type="text" name="item_serialno"
                                                                class="form-control"
                                                                value="{{ old('item_serialno', $item->item_serialno) }}"
                                                                placeholder="Serial Number">

                                                            <label>Serial Number (Optional)</label>
                                                        </div>


                                                        {{-- Unit of Measure --}}
                                                        <div class="form-floating mb-3">
                                                            <select name="item_uom_name" class="form-select">

                                                                <option value="" disabled>Select Unit of Measure
                                                                </option>

                                                                <option value="Pcs"
                                                                    {{ $item->uom?->item_uom_name == 'Pcs' ? 'selected' : '' }}>
                                                                    Pcs</option>
                                                                <option value="Set"
                                                                    {{ $item->uom?->item_uom_name == 'Set' ? 'selected' : '' }}>
                                                                    Set</option>
                                                                <option value="Box"
                                                                    {{ $item->uom?->item_uom_name == 'Box' ? 'selected' : '' }}>
                                                                    Box</option>
                                                                <option value="Roll"
                                                                    {{ $item->uom?->item_uom_name == 'Roll' ? 'selected' : '' }}>
                                                                    Roll</option>
                                                                <option value="Pack"
                                                                    {{ $item->uom?->item_uom_name == 'Pack' ? 'selected' : '' }}>
                                                                    Pack</option>
                                                                <option value="Pair"
                                                                    {{ $item->uom?->item_uom_name == 'Pair' ? 'selected' : '' }}>
                                                                    Pair</option>

                                                            </select>

                                                            <label>Unit of Measure</label>
                                                        </div>


                                                        {{-- Quantity --}}
                                                        <div class="form-floating mb-3">
                                                            <input type="number" name="item_quantity"
                                                                class="form-control"
                                                                value="{{ old('item_quantity', $item->item_quantity) }}"
                                                                placeholder="Quantity" min="1" max="99"
                                                                required oninput="this.value=this.value.slice(0,4)">

                                                            <label>Quantity</label>

                                                            <div class="invalid-feedback">
                                                                Quantity is required.
                                                            </div>
                                                        </div>


                                                        {{-- Remark --}}
                                                        <div class="form-floating mb-3">
                                                            <select name="item_remark" class="form-select" required>

                                                                <option value="" disabled>Select Remark</option>

                                                                <option value="Good"
                                                                    {{ $item->item_remark == 'Good' ? 'selected' : '' }}>
                                                                    Good</option>
                                                                <option value="Damaged"
                                                                    {{ $item->item_remark == 'Damaged' ? 'selected' : '' }}>
                                                                    Damaged</option>
                                                                <option value="Missing"
                                                                    {{ $item->item_remark == 'Missing' ? 'selected' : '' }}>
                                                                    Missing</option>

                                                            </select>

                                                            <label>Remark</label>
                                                        </div>


                                                        {{-- Category --}}
                                                        <div class="form-floating mb-3">
                                                            <select name="item_category_id" class="form-select"
                                                                required>

                                                                <option value="" disabled>Select Category
                                                                </option>

                                                                @foreach ($item_categories as $category)
                                                                    <option value="{{ $category->item_category_id }}"
                                                                        {{ $item->item_category_id == $category->item_category_id ? 'selected' : '' }}>

                                                                        {{ $category->item_category_name }}

                                                                    </option>
                                                                @endforeach

                                                            </select>

                                                            <label>Category</label>

                                                            <div class="invalid-feedback">
                                                                Category is required.
                                                            </div>
                                                        </div>


                                                        {{-- Brand --}}
                                                        <div class="form-floating mb-3">
                                                            <select name="item_brand_name" class="form-select"
                                                                required>

                                                                <option value="" disabled>Select Brand</option>

                                                                <option value="Logitech"
                                                                    {{ $item->brand?->item_brand_name == 'Logitech' ? 'selected' : '' }}>
                                                                    Logitech</option>
                                                                <option value="Microsoft"
                                                                    {{ $item->brand?->item_brand_name == 'Microsoft' ? 'selected' : '' }}>
                                                                    Microsoft</option>
                                                                <option value="HP"
                                                                    {{ $item->brand?->item_brand_name == 'HP' ? 'selected' : '' }}>
                                                                    HP</option>
                                                                <option value="Dell"
                                                                    {{ $item->brand?->item_brand_name == 'Dell' ? 'selected' : '' }}>
                                                                    Dell</option>
                                                                <option value="Corsair"
                                                                    {{ $item->brand?->item_brand_name == 'Corsair' ? 'selected' : '' }}>
                                                                    Corsair</option>

                                                                <option value="TP-Link"
                                                                    {{ $item->brand?->item_brand_name == 'TP-Link' ? 'selected' : '' }}>
                                                                    TP-Link</option>
                                                                <option value="Cisco"
                                                                    {{ $item->brand?->item_brand_name == 'Cisco' ? 'selected' : '' }}>
                                                                    Cisco</option>
                                                                <option value="Netgear"
                                                                    {{ $item->brand?->item_brand_name == 'Netgear' ? 'selected' : '' }}>
                                                                    Netgear</option>
                                                                <option value="Ubiquiti"
                                                                    {{ $item->brand?->item_brand_name == 'Ubiquiti' ? 'selected' : '' }}>
                                                                    Ubiquiti</option>

                                                                <option value="Seagate"
                                                                    {{ $item->brand?->item_brand_name == 'Seagate' ? 'selected' : '' }}>
                                                                    Seagate</option>
                                                                <option value="Western Digital (WD)"
                                                                    {{ $item->brand?->item_brand_name == 'Western Digital (WD)' ? 'selected' : '' }}>
                                                                    Western Digital (WD)</option>
                                                                <option value="Samsung"
                                                                    {{ $item->brand?->item_brand_name == 'Samsung' ? 'selected' : '' }}>
                                                                    Samsung</option>
                                                                <option value="Kingston"
                                                                    {{ $item->brand?->item_brand_name == 'Kingston' ? 'selected' : '' }}>
                                                                    Kingston</option>

                                                                <option value="Intel"
                                                                    {{ $item->brand?->item_brand_name == 'Intel' ? 'selected' : '' }}>
                                                                    Intel</option>
                                                                <option value="AMD"
                                                                    {{ $item->brand?->item_brand_name == 'AMD' ? 'selected' : '' }}>
                                                                    AMD</option>
                                                                <option value="Nvidia"
                                                                    {{ $item->brand?->item_brand_name == 'Nvidia' ? 'selected' : '' }}>
                                                                    Nvidia</option>
                                                                <option value="ASUS"
                                                                    {{ $item->brand?->item_brand_name == 'ASUS' ? 'selected' : '' }}>
                                                                    ASUS</option>
                                                                <option value="MSI"
                                                                    {{ $item->brand?->item_brand_name == 'MSI' ? 'selected' : '' }}>
                                                                    MSI</option>

                                                                <option value="Canon"
                                                                    {{ $item->brand?->item_brand_name == 'Canon' ? 'selected' : '' }}>
                                                                    Canon</option>
                                                                <option value="Epson"
                                                                    {{ $item->brand?->item_brand_name == 'Epson' ? 'selected' : '' }}>
                                                                    Epson</option>
                                                                <option value="Brother"
                                                                    {{ $item->brand?->item_brand_name == 'Brother' ? 'selected' : '' }}>
                                                                    Brother</option>

                                                                <option value="Apple"
                                                                    {{ $item->brand?->item_brand_name == 'Apple' ? 'selected' : '' }}>
                                                                    Apple</option>
                                                                <option value="Xiaomi"
                                                                    {{ $item->brand?->item_brand_name == 'Xiaomi' ? 'selected' : '' }}>
                                                                    Xiaomi</option>
                                                                <option value="Lenovo"
                                                                    {{ $item->brand?->item_brand_name == 'Lenovo' ? 'selected' : '' }}>
                                                                    Lenovo</option>
                                                                <option value="Huawei"
                                                                    {{ $item->brand?->item_brand_name == 'Huawei' ? 'selected' : '' }}>
                                                                    Huawei</option>

                                                                <option value="Belkin"
                                                                    {{ $item->brand?->item_brand_name == 'Belkin' ? 'selected' : '' }}>
                                                                    Belkin</option>
                                                                <option value="UGREEN"
                                                                    {{ $item->brand?->item_brand_name == 'UGREEN' ? 'selected' : '' }}>
                                                                    UGREEN</option>
                                                                <option value="Anker"
                                                                    {{ $item->brand?->item_brand_name == 'Anker' ? 'selected' : '' }}>
                                                                    Anker</option>

                                                            </select>

                                                            <label>Brand</label>

                                                            <div class="invalid-feedback">
                                                                Brand is required.
                                                            </div>

                                                        </div>

                                                    </div>


                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-light text-dark"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Update
                                                            Item</button>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No Product Found.</td>
                            </tr>
                        @endforelse

                        </tbody>


                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $items->links('pagination::bootstrap-4') }}
                    </div>
            </div>

        </div>


    </body>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
