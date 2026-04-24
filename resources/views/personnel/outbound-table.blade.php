@php
    $remarkColor = [
        'To be delivered' => 'bg-info text-dark',
        'Not Receive' => 'bg-danger',
        'Issued' => 'bg-warning text-dark',
        'Received' => 'bg-success',
        'Returned' => 'bg-primary',
    ];
@endphp
@forelse ($outbounds as $outbound)
    <tr>
        <td>
            <input type="checkbox" class="select_item" value="{{ $outbound->personnel_item_id }}">
        </td>
        <td>{{ $outbound->personnel?->personnel_name ?? '-' }}</td>
        <td>{{ $outbound->item?->item_name ?? '-' }}</td>
        <td>{{ $outbound->item?->item_serialno ?? '-' }}</td>
        <td>
            {{ \Carbon\Carbon::parse($outbound->personnel_date_issued)->setTimezone('Asia/Manila')->format('M d, Y ') }}
        </td>
        <td>{{ $outbound->personnel_item_quantity }}</td>
        <td>{{ $outbound->item?->uom?->item_uom_name ?? '-' }}</td>
        <td>
            {{ \Carbon\Carbon::parse($outbound->personnel_date_receive)->setTimezone('Asia/Manila')->format('M d, Y ') }}
        </td>
        <td>{{ $outbound->personnel?->branch?->branch_name ?? '-' }}</td>
        <td>{{ $outbound->personnel?->branch?->branch_department ?? '-' }}</td>
        <td>
            <span class="badge {{ $remarkColor[$outbound->personnel_item_remarks] ?? 'bg-secondary' }}">
                {{ $outbound->personnel_item_remarks ?? '-' }}
            </span>
        </td>
        <td>
            <div class="dropdown position-static">
                <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                    id="actionMenu_{{ $outbound->personnel_item_id }}" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical fs-5"></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="actionMenu_{{ $outbound->personnel_item_id }}">
                    <li>
                        <button type="button" class="dropdown-item text-primary" data-bs-toggle="modal"
                            data-bs-target="#viewOutboundModal_{{ $outbound->personnel_item_id }}">
                            View
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item" data-bs-toggle="modal"
                            data-bs-target="#editOutboundModal_{{ $outbound->personnel_item_id }}">
                            Edit
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item text-warning" data-bs-toggle="modal"
                            data-bs-target="#returnOutboundModal_{{ $outbound->personnel_item_id }}">
                            Return
                        </button>
                    </li>
                    <li>
                        <form action="{{ route('outbound.destroy', $outbound->personnel_item_id) }}" method="POST"
                            class="d-inline delete-form"> @csrf
                            @method('DELETE')
                            <button class="dropdown-item text-danger" type="submit">Delete</button>
                        </form>
                    </li>
                </ul>
            </div>

            <div class="modal fade" id="viewOutboundModal_{{ $outbound->personnel_item_id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">View Record Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-4">
                                <div class="col-md-6 border-end">
                                    <h6 class="text-muted mb-3"><i class="bi bi-info-circle me-1"></i> Personnel
                                        Identification</h6>
                                    <p><strong>Custodian:</strong>
                                        {{ $outbound->personnel ? $outbound->personnel->personnel_name : '-' }}</p>
                                    <p><strong>Branch:</strong>
                                        {{ $outbound->personnel?->branch?->branch_name ?? '-' }}</p>
                                    <p><strong>Department:</strong>
                                        {{ $outbound->personnel?->branch?->branch_department ?? '-' }}</p>

                                </div>

                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3"><i class="bi bi-box-seam me-1"></i> Item
                                        Information</h6>
                                    <p><strong>Item Name:</strong>
                                        {{ $outbound->item->item_name }}</p>
                                    <p><strong>Unit of Measure:</strong>
                                        {{ $outbound->item ? $outbound->item->uom->item_uom_name : '-' }}</p>
                                    <p><strong>Date Issued:</strong>
                                        {{ \Carbon\Carbon::parse($outbound->personnel_date_issued)->setTimezone('Asia/Manila')->format('M d, Y ') }}
                                    </p>
                                    <p><strong>Date Received:</strong>
                                        {{ \Carbon\Carbon::parse($outbound->personnel_date_receive)->setTimezone('Asia/Manila')->format('M d, Y ') }}
                                    </p>

                                    <p><strong>Status:</strong> <span
                                            class="badge {{ $remarkColor[$outbound->personnel_item_remarks] ?? 'bg-secondary' }}">
                                            {{ $outbound->personnel_item_remarks ?? '-' }}
                                        </span></p>
                                    @if (
                                        $outbound->personnel_item_remarks === 'Returned' &&
                                            ($outbound->return_reason_preset || $outbound->return_reason_detail || $outbound->item_remark))
                                        <hr class="my-3">
                                        <h6 class="text-muted mb-2"><i class="bi bi-chat-left-text me-1"></i> Return
                                            notes
                                        </h6>
                                        @if ($outbound->item_remark)
                                            <p class="mb-1"><strong>Stock condition:</strong>
                                                {{ $outbound->item_remark }}
                                            </p>
                                        @endif
                                        @if ($outbound->return_reason_preset)
                                            <p class="mb-1"><strong>Reason:</strong>
                                                {{ ucfirst(str_replace('_', ' ', $outbound->return_reason_preset)) }}
                                            </p>
                                        @endif
                                        @if ($outbound->return_reason_detail)
                                            <p class="mb-0"><strong>Details:</strong>
                                                {{ $outbound->return_reason_detail }}</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <!-- PDF button -->
                            <a href="{{ route('inventory.index', ['export' => 'pdf', 'item_id' => $outbound->item->item_id]) }}"
                                target="_blank" class="btn btn-danger">
                                <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                            </a>

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- EDIT MODAL --}}
            <div class="modal fade" id="editOutboundModal_{{ $outbound->personnel_item_id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        @php
                            $isReturned = $outbound->personnel_item_remarks === 'Returned';
                            $isReceived = $outbound->personnel_item_remarks === 'Received';

                            // Calculate the absolute max they can request:
                            // What they already hold + what is currently left in stock
                            $remainingStock = $outbound->item->item_quantity_remaining ?? 0;
                            $maxAllowed = $outbound->personnel_item_quantity + $remainingStock;
                        @endphp

                        <form action="{{ route('outbound.update', $outbound->personnel_item_id) }}" method="POST"
                            class="needs-validation-update" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Edit Outbound
                                    @if ($isReturned)
                                        <span class="badge bg-danger ms-2">Returned (Locked)</span>
                                    @endif
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                {{-- Blue Info Alert --}}
                                <div class="alert alert-info py-2 mb-4">
                                    <small><i class="bi bi-info-circle"></i> Update the details of the issued item here.
                                        Note that fully returned items cannot be edited.</small>
                                </div>

                                <div class="row">
                                    {{-- LEFT COLUMN --}}
                                    <div class="col-md-6 border-end">

                                        {{-- Custodian (Floating) --}}
                                        <div class="form-floating mb-3">
                                            <select name="personnel_id"
                                                id="edit_custodian_{{ $outbound->personnel_item_id }}"
                                                class="form-select" {{ $isReturned ? 'disabled' : '' }} required>
                                                @foreach ($personnels as $personnel)
                                                    <option value="{{ $personnel->personnel_id }}"
                                                        {{ $personnel->personnel_id == $outbound->personnel_id ? 'selected' : '' }}>
                                                        {{ $personnel->personnel_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label
                                                for="edit_custodian_{{ $outbound->personnel_item_id }}">Custodian</label>
                                        </div>

                                        {{-- Quantity (Floating) --}}
                                        <div class="form-floating mb-3">
                                            <input type="number" name="personnel_item_quantity"
                                                id="edit_qty_{{ $outbound->personnel_item_id }}" class="form-control"
                                                value="{{ $outbound->personnel_item_quantity }}" placeholder="Quantity"
                                                min="1" max="{{ $maxAllowed }}"
                                                {{ $isReturned ? 'disabled' : '' }} required>
                                            <label for="edit_qty_{{ $outbound->personnel_item_id }}">Quantity</label>

                                            {{-- Helper text showing the maximum allowed --}}
                                            <div class="form-text text-muted" style="font-size: 0.75rem;">
                                                Maximum available: {{ $maxAllowed }}
                                            </div>
                                            <div class="invalid-feedback">
                                                Please enter a valid quantity (1 - {{ $maxAllowed }}).
                                            </div>
                                        </div>
                                    </div>

                                    {{-- RIGHT COLUMN --}}
                                    <div class="col-md-6">

                                        {{-- Date Issued (Floating) --}}
                                        <div class="form-floating mb-3">
                                            <input type="date" name="personnel_date_issued"
                                                id="edit_issued_{{ $outbound->personnel_item_id }}"
                                                class="form-control" value="{{ $outbound->personnel_date_issued }}"
                                                placeholder="Date Issued" {{ $isReturned ? 'disabled' : '' }}
                                                required>
                                            <label for="edit_issued_{{ $outbound->personnel_item_id }}">Date
                                                Issued</label>
                                        </div>

                                        {{-- Remarks (Floating) --}}
                                        <div class="form-floating mb-3">
                                            <select name="personnel_item_remarks"
                                                id="edit_remarks_{{ $outbound->personnel_item_id }}"
                                                class="form-select"
                                                onchange="toggleDateReceived(this, '{{ $outbound->personnel_item_id }}')"
                                                {{ $isReturned ? 'disabled' : '' }} required>
                                                <option value="Received"
                                                    {{ $outbound->personnel_item_remarks == 'Received' ? 'selected' : '' }}>
                                                    Received</option>
                                                <option value="Not Receive"
                                                    {{ $outbound->personnel_item_remarks == 'Not Receive' ? 'selected' : '' }}>
                                                    Not Received</option>
                                                @if ($isReturned)
                                                    <option value="Returned" selected>Returned</option>
                                                @endif
                                            </select>
                                            <label
                                                for="edit_remarks_{{ $outbound->personnel_item_id }}">Remarks</label>
                                        </div>

                                        {{-- Date Received (Floating) --}}
                                        <div class="form-floating mb-3"
                                            id="dateReceivedContainer_{{ $outbound->personnel_item_id }}"
                                            style="display: {{ $isReceived ? 'block' : 'none' }};">
                                            <input type="date" name="personnel_item_receive"
                                                id="edit_received_{{ $outbound->personnel_item_id }}"
                                                class="form-control border-primary" placeholder="Date Received"
                                                value="{{ $outbound->personnel_date_receive }}"
                                                {{ $isReturned ? 'disabled' : '' }}>
                                            <label for="edit_received_{{ $outbound->personnel_item_id }}"
                                                class="text-primary fw-bold">Date Received</label>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" {{ $isReturned ? 'disabled' : '' }}>
                                    {{ $isReturned ? 'Cannot Edit (Returned)' : 'Update Outbound' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- RETURN MODAL --}}
            <div class="modal fade" id="returnOutboundModal_{{ $outbound->personnel_item_id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        @php
                            $isReturned = $outbound->personnel_item_remarks === 'Returned';
                        @endphp

                        {{-- Form maintains "needs-validation" and "novalidate" for Bootstrap JS --}}
                        <form action="{{ route('outbound.return', $outbound->personnel_item_id) }}" method="POST"
                            class="needs-validation-return" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Return Item
                                    @if ($isReturned)
                                        <span class="badge bg-danger ms-2">Already Returned</span>
                                    @endif
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                    {{-- LEFT COLUMN: Quantity & Validation --}}
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="number" name="return_quantity"
                                                id="return_quantity_{{ $outbound->personnel_item_id }}"
                                                {{-- Kept @error and is-invalid logic --}}
                                                class="form-control @error('return_quantity') is-invalid @enderror"
                                                placeholder="Return Quantity" min="1"
                                                max="{{ $outbound->personnel_item_quantity }}"
                                                value="{{ old('return_quantity', 1) }}"
                                                {{ $isReturned ? 'readonly' : '' }} required>

                                            <label for="return_quantity_{{ $outbound->personnel_item_id }}">Returned
                                                Qty</label>

                                            {{-- Kept Invalid Feedback --}}
                                            <div class="invalid-feedback">
                                                Please enter a valid return quantity.
                                            </div>
                                            <div class="form-text text-muted">
                                                Max allowed: {{ $outbound->personnel_item_quantity }}
                                            </div>
                                        </div>

                                        <div class="alert alert-info py-2">
                                            <small><i class="bi bi-info-circle"></i> Input the qty of the item u want
                                                to return.</small>
                                        </div>
                                    </div>

                                    {{-- RIGHT COLUMN: Condition & Date Validation --}}
                                    <div class="col-md-6">
                                        {{-- Condition Select (Now Floating) --}}
                                        <div class="form-floating mb-3">
                                            <select name="return_condition"
                                                id="return_condition_{{ $outbound->personnel_item_id }}"
                                                class="form-select" {{ $isReturned ? 'disabled' : '' }} required>
                                                <option value="Good">Good</option>
                                                <option value="Damaged">Damaged</option>
                                            </select>
                                            <label
                                                for="return_condition_{{ $outbound->personnel_item_id }}">Condition</label>
                                        </div>

                                        {{-- Return Date with Past-Date Validation (Already Floating) --}}
                                        <div class="form-floating mb-3">
                                            <input type="date" class="form-control"
                                                id="return_date_{{ $outbound->personnel_item_id }}"
                                                name="return_date" value="{{ date('Y-m-d') }}"
                                                min="{{ date('Y-m-d') }}" {{ $isReturned ? 'disabled' : '' }}
                                                required>

                                            <label for="return_date_{{ $outbound->personnel_item_id }}">Return
                                                Date</label>

                                            <div class="invalid-feedback">
                                                Return date cannot be in the past.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        {{-- Reason Preset Select (Now Floating) --}}
                                        <div class="form-floating mb-3">
                                            <select name="return_reason_preset"
                                                id="return_reason_preset_{{ $outbound->personnel_item_id }}"
                                                class="form-select return-reason-preset-select"
                                                {{ $isReturned ? 'disabled' : '' }} required>
                                            </select>
                                            <label
                                                for="return_reason_preset_{{ $outbound->personnel_item_id }}">Reason
                                                (depends on Good / Damaged)
                                            </label>
                                            <div class="invalid-feedback">Select a reason.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        {{-- Details Textarea (Now Floating) --}}
                                        <div class="form-floating mb-3">
                                            <textarea name="return_reason_detail" id="return_reason_detail_{{ $outbound->personnel_item_id }}"
                                                class="form-control return-reason-detail-input" style="height: 100px;" maxlength="2000"
                                                placeholder="Optional notes. Required if you choose Other." {{ $isReturned ? 'readonly' : '' }}></textarea>
                                            <label
                                                for="return_reason_detail_{{ $outbound->personnel_item_id }}">Details
                                                / explanation</label>
                                            <div class="form-text text-muted">
                                                Good and Damaged each have their own reason list. Choose Other to type a
                                                custom reason (details required).
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    Cancel
                                </button>

                                <button type="submit" class="btn btn-warning" {{ $isReturned ? 'disabled' : '' }}>
                                    {{ $isReturned ? 'Already Returned' : 'Confirm Return' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="12" class="text-center" style="font-size:15px;font-weight:bold; color:gray">
            No Record Found.
        </td>
    </tr>
@endforelse
<tr class="pagination-row">
    <td colspan="12">
        <div id="pagination-container" class="d-flex justify-content-center mt-3">
            {{-- Wrap in a class for easier targeting --}}
            <div class="ajax-pagination">
                {{ $outbounds->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </td>
</tr>
