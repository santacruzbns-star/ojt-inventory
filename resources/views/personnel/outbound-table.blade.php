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
        <td class="text-center">{{ $outbound->personnel?->personnel_name ?? '-' }}</td>
        <td class="text-center">{{ $outbound->item?->item_name ?? '-' }}</td>
        <td class="text-center">
            {{ \Carbon\Carbon::parse($outbound->personnel_date_issued)->setTimezone('Asia/Manila')->format('M d, Y ') }}
        </td>
        <td class="text-center">{{ $outbound->personnel_item_quantity }}</td>
        <td class="text-center">{{ $outbound->item?->uom?->item_uom_name ?? '-' }}</td>
        <td class="text-center">
            {{ \Carbon\Carbon::parse($outbound->personnel_date_receive)->setTimezone('Asia/Manila')->format('M d, Y ') }}
        </td>
        <td class="text-center">{{ $outbound->personnel?->branch?->branch_name ?? '-' }}</td>
        <td class="text-center">{{ $outbound->personnel?->branch?->branch_department ?? '-' }}</td>
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
                            class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="dropdown-item text-danger" type="submit">Delete</button>
                        </form>
                    </li>
                </ul>
            </div>
        </td>
    </tr>

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
                            <p><strong>Branch:</strong> {{ $outbound->personnel?->branch?->branch_name ?? '-' }}</p>
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
                @endphp

                <form action="{{ route('outbound.update', $outbound->personnel_item_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Edit Outbound
                            @if ($isReturned)
                                {{-- <span class="badge bg-danger ms-2">Returned (Locked)</span> --}}
                            @endif
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        {{-- Custodian --}}
                        <div class="mb-3">
                            <label class="form-label">Custodian</label>
                            <select name="personnel_id" class="form-select" {{ $isReturned ? 'disabled' : '' }}
                                required>
                                @foreach ($personnels as $personnel)
                                    <option value="{{ $personnel->personnel_id }}"
                                        {{ $personnel->personnel_id == $outbound->personnel_id ? 'selected' : '' }}>
                                        {{ $personnel->personnel_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Quantity --}}
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="personnel_item_quantity" class="form-control"
                                value="{{ $outbound->personnel_item_quantity }}" {{ $isReturned ? 'disabled' : '' }}
                                required>
                        </div>

                        {{-- Date Received --}}
                        <div class="mb-3">
                            <label class="form-label">Date Received</label>
                            <input type="date" name="personnel_item_receive" class="form-control"
                                value="{{ $outbound->personnel_date_receive }}" {{ $isReturned ? 'disabled' : '' }}
                                required>
                        </div>

                        {{-- Remarks --}}
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <select name="personnel_item_remarks" class="form-select"
                                {{ $isReturned ? 'disabled' : '' }} required>
                                <option value="Issued"
                                    {{ $outbound->personnel_item_remarks == 'Issued' ? 'selected' : '' }}>
                                    Issued
                                </option>
                                <option value="Returned"
                                    {{ $outbound->personnel_item_remarks == 'Returned' ? 'selected' : '' }}>
                                    Returned
                                </option>
                                <option value="Received"
                                    {{ $outbound->personnel_item_remarks == 'Received' ? 'selected' : '' }}>
                                    Received
                                </option>
                                <option value="Not Receive"
                                    {{ $outbound->personnel_item_remarks == 'Not Receive' ? 'selected' : '' }}>
                                    Not Received
                                </option>
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-light text-dark" data-bs-dismiss="modal">
                            Cancel
                        </button>

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
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                @php
                    $isReturned = $outbound->personnel_item_remarks === 'Returned';
                @endphp

                <form action="{{ route('outbound.return', $outbound->personnel_item_id) }}" method="POST"
                    class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Return Item
                            @if ($isReturned)
                                {{-- <span class="badge bg-danger ms-2">Already Returned</span> --}}
                            @endif
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        {{-- Returned Quantity --}}
                        <div class="form-floating mb-3">
                            <input type="number" name="return_quantity"
                                id="return_quantity_{{ $outbound->personnel_item_id }}"
                                class="form-control @error('return_quantity') is-invalid @enderror"
                                placeholder="Return Quantity" min="1"
                                max="{{ $outbound->personnel_item_quantity }}" value="{{ old('return_quantity') }}"
                                {{ $isReturned ? 'readonly' : '' }} required>

                            <label for="return_quantity_{{ $outbound->personnel_item_id }}" value="1">
                                Returned Quantity
                            </label>

                            <div class="invalid-feedback">
                                Please enter a valid return quantity.
                            </div>
                            <div class="form-text text-muted">
                                Max allowed: {{ $outbound->personnel_item_quantity }}
                            </div>
                        </div>

                        {{-- Condition --}}
                        <div class="mb-3">
                            <label for="return_condition_{{ $outbound->personnel_item_id }}"
                                class="form-label">Condition</label>

                            <select name="return_condition" id="return_condition_{{ $outbound->personnel_item_id }}"
                                class="form-select" {{ $isReturned ? 'disabled' : '' }} required>
                                <option value="Good">Good</option>
                                <option value="Damaged">Damaged</option>
                            </select>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="return_date" name="return_date"
                                value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}"
                                {{ $isReturned ? 'disabled' : '' }} required>

                            <label for="return_date">Return Date</label>

                            <div class="invalid-feedback">
                                Return date cannot be in the past.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-light text-dark" data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-warning" {{ $isReturned ? 'disabled' : '' }}>
                            {{ $isReturned ? 'Already Returned' : 'Return Item' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@empty
    <tr>
        <td colspan="11" class="text-center" style="font-size:15px;font-weight:bold; color:gray">
            No Record Found.
        </td>
    </tr>
@endforelse

<script>
    // QUANTITY VALIDATION
    document.querySelectorAll('[id^="return_quantity_"]').forEach(input => {
        input.addEventListener('input', function() {
            const max = parseInt(this.getAttribute('max'));
            const value = parseInt(this.value);

            if (value > max) {
                this.classList.add('is-invalid');
                this.setCustomValidity(`Cannot exceed ${max}`);
                this.parentElement.querySelector('.invalid-feedback').innerText =
                    `Cannot exceed ${max}.`;
            } else if (value < 1 || isNaN(value)) {
                this.classList.add('is-invalid');
                this.setCustomValidity(`Minimum is 1`);
                this.parentElement.querySelector('.invalid-feedback').innerText =
                    `Minimum is 1.`;
            } else {
                this.classList.remove('is-invalid');
                this.setCustomValidity('');
            }
        });
    });

    // DATE VALIDATION (NO PAST DATE)
    document.querySelectorAll('input[name="return_date"]').forEach(input => {

        const today = new Date().toISOString().split('T')[0];

        // set min dynamically (extra safety)
        input.setAttribute('min', today);

        input.addEventListener('input', function() {
            if (this.value < today) {
                this.classList.add('is-invalid');
                this.setCustomValidity('Date cannot be in the past');

                // create feedback if not exists
                let feedback = this.parentElement.querySelector('.invalid-feedback');
                if (!feedback) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    this.parentElement.appendChild(feedback);
                }

                feedback.innerText = 'Date cannot be in the past';
            } else {
                this.classList.remove('is-invalid');
                this.setCustomValidity('');
            }
        });
    });
</script>
