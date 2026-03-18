@foreach ($outbounds as $outbound)
<tr>
    <td>
        <input type="checkbox" class="select_item" value="{{ $outbound->personnel_item_id }}">
    </td>
    <td>{{ $outbound->personnel?->personnel_name ?? '-' }}</td>
    <td>{{ $outbound->item?->item_name ?? '-' }}</td>
    <td>{{ $outbound->personnel_date_issued }}</td>
    <td>{{ $outbound->personnel_item_quantity }}</td>
    <td>{{ $outbound->item?->uom?->item_uom_name ?? '-' }}</td>
    <td>{{ $outbound->personnel_date_receive }}</td>
    <td>{{ $outbound->personnel?->branch?->branch_name ?? '-' }}</td>
    <td>{{ $outbound->personnel?->branch?->branch_department ?? '-' }}</td>
    <td>{{ $outbound->personnel_item_remarks }}</td>
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

{{-- EDIT MODAL --}}
<div class="modal fade" id="editOutboundModal_{{ $outbound->personnel_item_id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('outbound.update', $outbound->personnel_item_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Outbound</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    {{-- Custodian --}}
                    <div class="mb-3">
                        <label class="form-label">Custodian</label>
                        <select name="personnel_id" class="form-select" required>
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
                            value="{{ $outbound->personnel_item_quantity }}" required>
                    </div>

                    {{-- Date Received --}}
                    <div class="mb-3">
                        <label class="form-label">Date Received</label>
                        <input type="date" name="personnel_item_receive" class="form-control"
                            value="{{ $outbound->personnel_date_receive }}" required>
                    </div>

                    {{-- Remarks --}}
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <select name="personnel_item_remarks" class="form-select" required>
                            <option value="Issued" {{ $outbound->personnel_item_remarks == 'Issued' ? 'selected' : '' }}>
                                Issued
                            </option>
                            <option value="Returned"
                                {{ $outbound->personnel_item_remarks == 'Returned' ? 'selected' : '' }}>
                                Returned
                            </option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light text-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Outbound</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- RETURN MODAL --}}
<div class="modal fade" id="returnOutboundModal_{{ $outbound->personnel_item_id }}" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
           <form action="{{ route('outbound.return', $outbound->personnel_item_id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="modal-header">
        <h5 class="modal-title">Return Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        {{-- Returned Quantity --}}
        <div class="mb-3">
            <label for="return_quantity_{{ $outbound->personnel_item_id }}" class="form-label">Returned Quantity</label>
            <input type="number" 
                   name="return_quantity" 
                   id="return_quantity_{{ $outbound->personnel_item_id }}" 
                   class="form-control" 
                   min="1"
                   max="{{ $outbound->personnel_item_quantity }}" 
                   required>
        </div>

        {{-- Condition --}}
        <div class="mb-3">
            <label for="return_condition_{{ $outbound->personnel_item_id }}" class="form-label">Condition</label>
            <select name="return_condition" 
                    id="return_condition_{{ $outbound->personnel_item_id }}" 
                    class="form-select" 
                    required>
                <option value="Good">Good</option>
                <option value="Damaged">Damaged</option>
            </select>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-outline-light text-dark" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-warning">Return Item</button>
        </div>
    </form>
        </div>
    </div>
</div>
@endforeach