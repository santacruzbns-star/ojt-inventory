  @php
      $remarkColor = [
          'Good' => 'bg-success',
          'Damaged' => 'bg-danger',
          'Missing' => 'bg-warning text-dark',
          'Returned' => 'bg-primary ',
      ];
  @endphp
  @forelse($items as $item)
      <tr>
          <td>
              <input type="checkbox" class="select_item" value="{{ $item->item_id }}">
          </td>
          <td>{{ $item->item_name }}</td>
          <td>
              @if ($item->category)
                  <i class="bi {{ $item->category->item_category_icon }} me-1"></i>
                  {{ $item->category->item_category_name }}
              @else
                  -
              @endif
          </td>
          {{-- <td>{{ $item->brand ? $item->brand->item_brand_name : '-' }}</td> --}}
          <td>{{ $item->item_serialno ?? '-' }}</td>
          <td>{{ $item->uom ? $item->uom->item_uom_name : '-' }}</td>
          <td>{{ $item->item_quantity ?? '-' }}</td>
          <td>{{ $item->item_quantity_remaining ?? '-' }}</td>
          {{-- <td>
              @if ($item->item_quantity_status == 'Out of Stock')
                  <span class="badge bg-danger">Out of Stock</span>
              @elseif ($item->item_quantity_status == 'Low Stock')
                  <span class="badge bg-warning text-dark">Low Stock</span>
              @else
                  <span class="badge bg-success">Available</span>
              @endif
          </td> --}}
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
                  <div class="modal-dialog modal-lg">
                      <div class="modal-content">
                          <div class="modal-header">
                              <h5 class="modal-title">View Item Details</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                              <div class="row">
                                  <div class="col-md-6 border-end">
                                      <h6 class="text-muted mb-3"><i class="bi bi-info-circle me-1"></i> Item
                                          Identification</h6>
                                      <p><strong>Name:</strong> {{ $item->item_name }}</p>
                                      <p><strong>Category:</strong>
                                          {{ $item->category ? $item->category->item_category_name : '-' }}</p>
                                      {{-- <p><strong>Brand:</strong>
                                          {{ $item->brand ? $item->brand->item_brand_name : '-' }}</p> --}}
                                      <p><strong>Serial Number:</strong> {{ $item->item_serialno ?? '-' }}</p>
                                  </div>

                                  <div class="col-md-6">
                                      <h6 class="text-muted mb-3"><i class="bi bi-box-seam me-1"></i> Stock
                                          Information</h6>
                                      <p><strong>Unit of Measure:</strong>
                                          {{ $item->uom ? $item->uom->item_uom_name : '-' }}</p>
                                      <p><strong>Quantity:</strong> {{ $item->item_quantity ?? '-' }}</p>
                                      <p><strong>Remaining Quantity:</strong>
                                          {{ $item->item_quantity_remaining ?? '-' }}</p>
                                      <p><strong>Remark:</strong> {{ $item->item_remark ?? '-' }}</p>
                                  </div>
                              </div>
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

              <div class="modal fade" id="edit
              ItemModal_{{ $item->item_id }}" tabindex="-1">
                  <div class="modal-dialog modal-xl">
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
                                  <div class="row">
                                      {{-- COLUMN 1: Basic Info --}}
                                      <div class="col-md-4 border-end">
                                          <div class="form-floating mb-3">
                                              <input type="text" class="form-control" name="item_name"
                                                  id="edit_item_name_{{ $item->item_id }}"
                                                  value="{{ old('item_name', $item->item_name) }}"
                                                  placeholder="Item Name" required>
                                              <label>Item Name</label>
                                              <div class="invalid-feedback">Item Name is required.</div>
                                          </div>

                                          <div class="form-floating mb-3">
                                              <input type="text" name="item_serialno" class="form-control"
                                                  value="{{ old('item_serialno', $item->item_serialno) }}"
                                                  placeholder="Serial Number">
                                              <label>Serial Number (Optional)</label>
                                          </div>

                                          <div class="form-floating mb-3">
                                              <select name="item_uom_name" class="form-select">
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

                                      {{-- COLUMN 2: Classification --}}
                                      <div class="col-md-4 border-end">
                                          <div class="form-floating mb-3">
                                              <select name="item_category_id" class="form-select" required>
                                                  <option value="" disabled>Select Category</option>
                                                  @foreach ($item_categories as $category)
                                                      <option value="{{ $category->item_category_id }}"
                                                          {{ $item->item_category_id == $category->item_category_id ? 'selected' : '' }}>
                                                          {{ $category->item_category_name }}
                                                      </option>
                                                  @endforeach
                                              </select>
                                              <label>Category</label>
                                              <div class="invalid-feedback">Category is required.</div>
                                          </div>

                                          {{-- <div class="form-floating mb-3">
                                              <select name="item_brand_name" class="form-select" required>
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
                                              <label>Brand</label>
                                          </div> --}}

                                          <div class="form-floating mb-3">
                                              <select name="item_remark" class="form-select" required>
                                                  <option value="" disabled>Select Remark</option>
                                                  <option value="Good"
                                                      {{ $item->item_remark == 'Good' ? 'selected' : '' }}>Good
                                                  </option>
                                                  <option value="Damaged"
                                                      {{ $item->item_remark == 'Damaged' ? 'selected' : '' }}>Damaged
                                                  </option>
                                                  <option value="Missing"
                                                      {{ $item->item_remark == 'Missing' ? 'selected' : '' }}>Missing
                                                  </option>
                                              </select>
                                              <label>Remark</label>
                                          </div>
                                      </div>

                                      {{-- COLUMN 3: Stock Control --}}
                                      <div class="col-md-4">
                                          <div class="form-floating mb-3">
                                              <input type="number" name="item_quantity" class="form-control"
                                                  value="{{ old('item_quantity', $item->item_quantity) }}"
                                                  placeholder="Quantity" min="1" max="9999" required
                                                  oninput="this.value=this.value.slice(0,4)">
                                              <label>Quantity</label>
                                              <div class="invalid-feedback">Quantity is required.</div>
                                          </div>

                                          <div class="alert alert-info py-2">
                                              <small><i class="bi bi-info-circle"></i> Update the stock levels and
                                                  condition remarks here.</small>
                                          </div>
                                      </div>
                                  </div>
                              </div>

                              <div class="modal-footer">
                                  <button type="button" class="btn btn-outline-secondary"
                                      data-bs-dismiss="modal">Cancel</button>
                                  <button type="submit" class="btn btn-primary">Update Item</button>
                              </div>

                          </form>
                      </div>
                  </div>
              </div>
          </td>
      </tr>
  @empty
      <tr>
          <td colspan="11" class="text-center" style="font-size:15px;font-weight:bold; color:gray">No Product
              Found.</td>
      </tr>
  @endforelse
  <tr class="pagination-row">
      <td colspan="11">
          <div id="pagination-container" class="d-flex justify-content-center mt-3">
              {{ $items->links('pagination::bootstrap-4') }}
          </div>
      </td>
  </tr>
