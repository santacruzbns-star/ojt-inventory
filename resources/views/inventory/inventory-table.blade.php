  @forelse($items as $item)
      @php
          $remarkColor = [
              'Good' => 'bg-success',
              'Damaged' => 'bg-danger',
              'Missing' => 'bg-warning text-dark',
          ];
      @endphp
      <tr>
          <td>
              <input type="checkbox" class="select_item" value="{{ $item->item_id }}">
          </td>
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
                  <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="actionMenu_{{ $item->item_id }}"
                      data-bs-toggle="dropdown" aria-expanded="false">

                      <i class="bi bi-three-dots-vertical fs-5"></i>

                  </button>
                  <ul class="dropdown-menu" aria-labelledby="actionMenu_{{ $item->item_id }}">

                      <li>
                          <button type="button" class="dropdown-item text-primary" data-bs-toggle="modal"
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
                          <form action="{{ route('inventory.destroy', $item->item_id) }}" method="POST"
                              class="d-inline">
                              @csrf
                              @method('DELETE')
                              <button class="dropdown-item text-danger" type="submit">Delete</button>
                          </form>
                      </li>
                  </ul>
              </div>

              <div class="modal fade" id="viewItemModal_{{ $item->item_id }}" tabindex="-1">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header">
                              <h5 class="modal-title">View Item Details</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                              <ul class="list-group list-group-flush">
                                  <li class="list-group-item"><strong>Item Name:</strong> {{ $item->item_name }}</li>
                                  <li class="list-group-item"><strong>Category:</strong>
                                      {{ $item->category ? $item->category->item_category_name : '-' }}</li>
                                  <li class="list-group-item"><strong>Brand:</strong>
                                      {{ $item->brand ? $item->brand->item_brand_name : '-' }}</li>
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
                              <!-- PDF button -->
                              <a href="{{ route('inventory.index', ['export' => 'pdf', 'item_id' => $item->item_id]) }}"
                                  target="_blank" class="btn btn-danger">
                                  <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                              </a>

                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          </div>
                      </div>
                  </div>
              </div>

              <div class="modal fade" id="editItemModal_{{ $item->item_id }}" tabindex="-1">
                  <div class="modal-dialog modal-lg">
                      <div class="modal-content">

                          <form action="{{ route('inventory.update', $item->item_id) }}" method="POST"
                              class="needs-validation-update" novalidate>
                              @csrf
                              @method('PUT')

                              <div class="modal-header">
                                  <h5 class="modal-title">Edit Item</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                              </div>

                              <div class="modal-body">

                                  {{-- Item Name --}}
                                  <div class="form-floating mb-3">
                                      <input type="text" class="form-control" name="item_name"
                                          id="edit_item_name_{{ $item->item_id }}"
                                          value="{{ old('item_name', $item->item_name) }}" placeholder="Item Name"
                                          required>

                                      <label>Item Name</label>

                                      <div class="invalid-feedback">
                                          Item Name is required.
                                      </div>
                                  </div>


                                  {{-- Serial Number --}}
                                  <div class="form-floating mb-3">
                                      <input type="text" name="item_serialno" class="form-control"
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
                                      <input type="number" name="item_quantity" class="form-control"
                                          value="{{ old('item_quantity', $item->item_quantity) }}"
                                          placeholder="Quantity" min="1" max="99" required
                                          oninput="this.value=this.value.slice(0,4)">

                                      <label>Quantity</label>

                                      <div class="invalid-feedback">
                                          Quantity is required.
                                      </div>
                                  </div>


                                  {{-- Remark --}}
                                  <div class="form-floating mb-3">
                                      <select name="item_remark" class="form-select" required>

                                          <option value="" disabled>Select Remark</option>

                                          <option value="Good" {{ $item->item_remark == 'Good' ? 'selected' : '' }}>
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
                                      <select name="item_category_id" class="form-select" required>

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
                                      <select name="item_brand_name" class="form-select" required>

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
          <td colspan="9" class="text-center" style="font-size:15px;font-weight:bold; color:gray">No Product
              Found.</td>
      </tr>
  @endforelse
