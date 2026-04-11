let selectedOutboundIds = new Set();
let fetchOutboundTable;
let shouldHighlightOutbound = false;

document.addEventListener("DOMContentLoaded", function () {
    // --- 1. ELEMENT SELECTORS ---
    const tableBody = document.getElementById("table-data");
    const searchInput = document.getElementById("OutboundSearch");
    const personnelSelect = document.querySelector("select[name='personnel']");
    const departmentSelect = document.querySelector(
        "select[name='department']",
    );
    const branchSelect = document.querySelector("select[name='branch']");
    const remarksSelect = document.querySelector("select[name='remarks']");
    const bulkDeleteBtn = document.getElementById("bulk_delete_btn");
    const selectAll = document.getElementById("select_all");

    if (!tableBody) return;

    let timer;

    // --- 2. DATA FETCHING & UI SYNC ---
    fetchOutboundTable = function (page = 1) {
        const params = new URLSearchParams({
            search: searchInput?.value.trim() || "",
            personnel: personnelSelect?.value || "",
            department: departmentSelect?.value || "",
            branch: branchSelect?.value || "",
            remarks: remarksSelect?.value || "",
            page: page,
            ajax: 1,
        });

        clearTimeout(timer);
        timer = setTimeout(() => {
            fetch(`${window.outboundData.routes.index}?${params.toString()}`, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
            })
                .then((res) => res.json())
                .then((data) => {
                    tableBody.innerHTML = data.table;

                    if (shouldHighlightOutbound) {
                        const firstRow =
                            tableBody.querySelector("tr:first-child");
                        if (
                            firstRow &&
                            !firstRow.innerText.includes("No Record Found")
                        ) {
                            applyRowHighlight(firstRow);
                        }
                        shouldHighlightOutbound = false;
                    }

                    // RE-SYNC: Repaint checks after table content changes
                    syncCheckboxes();
                })
                .catch((err) => console.error("Fetch Error:", err));
        }, 300);
    };
    // Handle Checkbox Memory (Persistence)
    document.addEventListener("change", function (e) {
        if (e.target.classList.contains("select_item")) {
            const id = e.target.value;
            if (e.target.checked) {
                if (!selectedOutboundIds.includes(id))
                    selectedOutboundIds.push(id);
            } else {
                selectedOutboundIds = selectedOutboundIds.filter(
                    (item) => item !== id,
                );
            }
        }
    });

    // Listen to all filters
    if (searchInput) searchInput.addEventListener("keyup", fetchOutboundTable);
    if (personnelSelect)
        personnelSelect.addEventListener("change", fetchOutboundTable);
    if (departmentSelect)
        departmentSelect.addEventListener("change", fetchOutboundTable);
    if (branchSelect)
        branchSelect.addEventListener("change", fetchOutboundTable);
    if (remarksSelect)
        remarksSelect.addEventListener("change", fetchOutboundTable);
});

document.addEventListener("DOMContentLoaded", function () {
    // 1. Handle Remarks Visibility
    const remarksSelect = document.getElementById("personnel_item_remarks");
    const receiveDateContainer = document.getElementById(
        "receive_date_container",
    );
    const receiveDateInput = document.getElementById("personnel_date_receive");

    if (remarksSelect && receiveDateContainer) {
        remarksSelect.addEventListener("change", function () {
            if (this.value === "Received") {
                receiveDateContainer.style.display = "block";
            } else {
                receiveDateContainer.style.display = "none";
                if (receiveDateInput) receiveDateInput.value = "";
            }
        });
    }

    // 2. Handle Excel Export (Selected or All filtered)
    const exportExcelBtn = document.getElementById("export_excel_btn");

    if (exportExcelBtn) {
        exportExcelBtn.addEventListener("click", function (e) {
            e.preventDefault();

            // 1. Get IDs directly from our global Set memory
            // This ensures selections across multiple pages are captured
            const selectedIdsArray = Array.from(selectedOutboundIds);

            // 2. Get LIVE filter values from the inputs
            const search =
                document.getElementById("OutboundSearch")?.value || "";
            const personnel =
                document.querySelector("select[name='personnel']")?.value || "";
            const department =
                document.querySelector("select[name='department']")?.value ||
                "";
            const branch =
                document.querySelector("select[name='branch']")?.value || "";
            const remarks =
                document.querySelector("select[name='remarks']")?.value || "";

            // 3. Build the URL parameters
            const urlParams = new URLSearchParams({
                export: "excel",
                search: search,
                personnel: personnel,
                department: department,
                branch: branch,
                remarks: remarks,
            });

            // 4. If specific IDs are in memory, add them to the request
            if (selectedIdsArray.length > 0) {
                urlParams.set("ids", selectedIdsArray.join(","));
            }

            // 5. Execute Export
            const baseUrl = window.outboundData.routes.index;
            window.location.href = `${baseUrl}?${urlParams.toString()}`;
        });
    }
});

//add personnel with validation record outbound
document.addEventListener("DOMContentLoaded", function () {
    // Form and Error elements
    const form = document.getElementById("outboundForm");
    const submitBtn = document.getElementById("submitBtn");

    // Personnel Elements
    const pSearchInput = document.getElementById("personnelSearch");
    const personnelItems = document.querySelectorAll(".personnel-item");
    const pHiddenInput = document.getElementById("selected_personnel_id");
    const pDisplayCard = document.getElementById("selectedPersonnelCard");
    const pErrorDiv = document.getElementById("personnelError");

    // Item Elements
    const iSearchInput = document.getElementById("itemSearch");
    const itemBtns = document.querySelectorAll(".item-btn");
    const iHiddenInput = document.getElementById("selected_item_id");
    const iDisplayCard = document.getElementById("selectedItemCard");
    const iErrorDiv = document.getElementById("itemError");

    // Quantity Elements
    const qtyInput = document.getElementById("personnel_item_quantity");
    const qtyErrorText = document.getElementById("qtyErrorText");
    const qtyAvailableText = document.getElementById("qtyAvailableText");
    let maxAvailableQty = 0;

    /* =========================================
               1. Generic Search Function
               ========================================= */
    function attachSearch(searchInput, listItems) {
        if (!searchInput) return;
        searchInput.addEventListener("input", function (e) {
            const searchTerm = e.target.value.toLowerCase();
            listItems.forEach((item) => {
                const textData = item.innerText.toLowerCase();
                if (textData.includes(searchTerm)) {
                    item.classList.remove("d-none");
                } else {
                    item.classList.add("d-none");
                }
            });
        });
    }

    attachSearch(pSearchInput, personnelItems);
    attachSearch(iSearchInput, itemBtns);

    /* =========================================
               2. Personnel Selection Logic
               ========================================= */
    personnelItems.forEach((item) => {
        item.addEventListener("click", function () {
            personnelItems.forEach((btn) =>
                btn.classList.remove("active", "bg-primary", "text-white"),
            );
            this.classList.add("active", "bg-primary", "text-white");

            personnelItems.forEach((btn) => {
                const textElements = btn.querySelectorAll(
                    ".text-muted, .text-light",
                );
                if (btn.classList.contains("active")) {
                    textElements.forEach((el) => {
                        el.classList.remove("text-muted");
                        el.classList.add("text-light");
                    });
                } else {
                    textElements.forEach((el) => {
                        el.classList.remove("text-light");
                        el.classList.add("text-muted");
                    });
                }
            });

            pHiddenInput.value = this.getAttribute("data-id");
            pErrorDiv.classList.remove("d-block");

            document.getElementById("display_personnel_name").innerText =
                this.getAttribute("data-name");
            document.getElementById("display_branch").innerText =
                this.getAttribute("data-branch");
            document.getElementById("display_dept").innerText =
                this.getAttribute("data-dept");
            pDisplayCard.classList.remove("d-none");
        });
    });

    /* =========================================
               3. Item Selection & Stock Logic
               ========================================= */
    itemBtns.forEach((item) => {
        item.addEventListener("click", function () {
            if (this.hasAttribute("disabled")) return; // Extra safety check

            itemBtns.forEach((btn) =>
                btn.classList.remove("active", "bg-success", "text-white"),
            );
            this.classList.add("active", "bg-success", "text-white");

            itemBtns.forEach((btn) => {
                const textElements = btn.querySelectorAll(
                    ".text-muted, .text-light",
                );
                if (btn.classList.contains("active")) {
                    textElements.forEach((el) => {
                        el.classList.remove("text-muted");
                        el.classList.add("text-light");
                    });
                } else {
                    textElements.forEach((el) => {
                        el.classList.remove("text-light");
                        el.classList.add("text-muted");
                    });
                }
            });

            iHiddenInput.value = this.getAttribute("data-id");
            iErrorDiv.classList.remove("d-block");

            // Update Selected Item Card
            document.getElementById("display_item_name").innerText =
                this.getAttribute("data-name");
            document.getElementById("display_item_brand").innerText =
                this.getAttribute("data-brand");
            document.getElementById("display_item_sn").innerText =
                this.getAttribute("data-serial");
            iDisplayCard.classList.remove("d-none");

            // --- Update Quantity Validation Rules ---
            maxAvailableQty = parseInt(this.getAttribute("data-qty"));

            qtyInput.disabled = false;
            qtyInput.max = maxAvailableQty;
            qtyInput.value = 1; // Auto-fill 1 for convenience
            qtyInput.classList.remove("is-invalid");

            qtyAvailableText.classList.remove("d-none");
            qtyAvailableText.innerText = `Max available: ${maxAvailableQty} ${this.getAttribute("data-uom")}`;
            submitBtn.disabled = false;
        });
    });

    /* =========================================
               4. Quantity Input Live Validation
               ========================================= */
    if (qtyInput) {
        qtyInput.addEventListener("input", function () {
            const currentVal = parseInt(this.value);

            if (!iHiddenInput.value) {
                this.classList.add("is-invalid");
                qtyErrorText.innerText = `Please select an item first.`;
                submitBtn.disabled = true;
            } else if (currentVal > maxAvailableQty) {
                this.classList.add("is-invalid");
                qtyErrorText.innerText = `Cannot exceed stock limit (${maxAvailableQty}).`;
                submitBtn.disabled = true;
            } else if (currentVal < 1 || isNaN(currentVal)) {
                this.classList.add("is-invalid");
                qtyErrorText.innerText = `Quantity must be at least 1.`;
                submitBtn.disabled = true;
            } else {
                this.classList.remove("is-invalid");
                submitBtn.disabled = false;
            }
        });
    }
    /* =========================================
               6. Remarks Logic for "Received" Date
               ========================================= */
    const remarkSelect = document.getElementById("personnel_item_remarks");
    const receiveDateContainer = document.getElementById(
        "receive_date_container",
    );
    const receiveDateInput = document.getElementById("personnel_date_receive");

    if (remarkSelect && receiveDateContainer && receiveDateInput) {
        remarkSelect.addEventListener("change", function () {
            if (this.value === "Received") {
                receiveDateContainer.style.display = "block";
                receiveDateInput.setAttribute("required", "required");
            } else {
                receiveDateContainer.style.display = "none";
                receiveDateInput.removeAttribute("required");
                receiveDateInput.value = "";
            }
        });
    }
});

//personnel view search and click
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("personnelSearchView");
    const personnelItems = document.querySelectorAll(".personnel-view-item");
    const container = document.getElementById("assignedItemsContainer");

    if (!searchInput) return;

    // SEARCH
    searchInput.addEventListener("input", function () {
        let val = this.value.toLowerCase();

        personnelItems.forEach((item) => {
            item.classList.toggle(
                "d-none",
                !item.innerText.toLowerCase().includes(val),
            );
        });
    });

    // CLICK PERSON
    personnelItems.forEach((item) => {
        item.addEventListener("click", function () {
            // highlight
            personnelItems.forEach((btn) =>
                btn.classList.remove("active", "bg-primary", "text-white"),
            );
            this.classList.add("active", "bg-primary", "text-white");

            // show selected
            document.getElementById("view_person_name").innerText =
                this.dataset.name;
            document.getElementById("view_branch").innerText =
                this.dataset.branch;
            document.getElementById("view_dept").innerText = this.dataset.dept;
            document
                .getElementById("selectedPersonnelView")
                .classList.remove("d-none");

            let id = this.dataset.id;

            container.innerHTML = `<div class="text-muted">Loading...</div>`;

            fetch(`/personnel/${id}/items`, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((res) => res.text())
                .then((html) => {
                    container.innerHTML = html;
                })
                .catch(() => {
                    container.innerHTML = `<div class="text-danger">Failed to load items.</div>`;
                });
        });
    });
});

//delete personnel with confirmation
$(document).ready(function () {
    $(document).on("submit", "#personnel_modal .delete-form", function (e) {
        e.preventDefault();

        let form = this;
        let url = $(form).attr("action");

        Swal.fire({
            title: "Are you sure?",
            text: "This personnel will be deleted.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        _method: "DELETE",
                    },
                    success: function () {
                        // ✅ remove entire personnel block
                        $(form)
                            .closest(".position-relative")
                            .fadeOut(200, function () {
                                $(this).remove();
                            });

                        // ✅ success toast
                        Swal.fire({
                            icon: "success",
                            title: "Deleted!",
                            timer: 1000,
                            showConfirmButton: false,
                        });

                        // ✅ clear selected view if deleted
                        $("#selectedPersonnelView").addClass("d-none");
                    },
                    error: function () {
                        Swal.fire("Error", "Delete failed.", "error");
                    },
                });
            }
        });
    });
});

//selecting multiple outbound records for bulk delete
const selectAll = document.getElementById("select_all");
const bulkDeleteBtn = document.getElementById("bulk_delete_btn");

window.syncCheckboxes = function () {
    const checkboxes = document.querySelectorAll(".select_item");

    checkboxes.forEach((cb) => {
        cb.checked = selectedOutboundIds.has(cb.value);
    });

    // Update the top "Select All" status for the current page view
    if (selectAll) {
        selectAll.checked =
            checkboxes.length > 0 &&
            [...checkboxes].every((cb) => selectedOutboundIds.has(cb.value));
    }

    updateBulkButtonUI();
};

function updateBulkButtonUI() {
    if (bulkDeleteBtn) {
        const count = selectedOutboundIds.size;
        bulkDeleteBtn.disabled = count === 0;
        bulkDeleteBtn.innerHTML = `<i class="fas fa-trash"></i> Delete Selected (${count})`;
    }
}

document.addEventListener("change", function (e) {
    // Individual Checkbox
    if (e.target.classList.contains("select_item")) {
        const id = e.target.value;
        if (e.target.checked) {
            selectedOutboundIds.add(id);
        } else {
            selectedOutboundIds.delete(id);
        }
        syncCheckboxes();
    }

    // Select All Checkbox
    if (e.target.id === "select_all") {
        const checkboxes = document.querySelectorAll(".select_item");
        checkboxes.forEach((cb) => {
            cb.checked = e.target.checked;
            if (e.target.checked) {
                selectedOutboundIds.add(cb.value);
            } else {
                selectedOutboundIds.delete(cb.value);
            }
        });
        updateBulkButtonUI();
    }
});

// 5. BULK DELETE ACTION
if (bulkDeleteBtn) {
    bulkDeleteBtn.addEventListener("click", function () {
        if (selectedOutboundIds.size === 0) return;

        Swal.fire({
            title: `Delete ${selectedOutboundIds.size} item(s)?`,
            text: "This action will permanently remove these records!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Yes, delete!",
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(window.outboundData.routes.bulkDelete, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": window.outboundData.csrfToken,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: JSON.stringify({
                        ids: Array.from(selectedOutboundIds),
                    }),
                })
                    .then((res) => res.json())
                    .then((data) => {
                        if (data.success) {
                            // Visually fade out the rows
                            selectedOutboundIds.forEach((id) => {
                                const row = document
                                    .querySelector(
                                        `.select_item[value="${id}"]`,
                                    )
                                    ?.closest("tr");
                                if (row) {
                                    row.style.transition = "opacity 0.3s ease";
                                    row.style.opacity = "0";
                                    setTimeout(() => row.remove(), 300);
                                }
                            });

                            selectedOutboundIds.clear();
                            if (selectAll) selectAll.checked = false;

                            Swal.fire({
                                toast: true,
                                position: "top-end",
                                icon: "success",
                                title: "Items deleted!",
                                showConfirmButton: false,
                                timer: 1500,
                            });

                            // Refresh table to fill gaps from next pages
                            setTimeout(() => fetchOutboundTable(), 400);
                        }
                    })
                    .catch((err) => {
                        console.error("Bulk Delete Error:", err);
                        Swal.fire(
                            "Error",
                            "Server connection failed.",
                            "error",
                        );
                    });
            }
        });
    });
}

//update personnel with validation record outbound
document.addEventListener("DOMContentLoaded", function () {
    // 1. Bootstrap Validation
    const forms = document.querySelectorAll(".needs-validation");
    Array.from(forms).forEach((f) => {
        f.addEventListener(
            "submit",
            (event) => {
                if (!f.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                f.classList.add("was-validated");
            },
            false,
        );
    });

    // 2. Confirm & AJAX Submit
    const confirmForms = document.querySelectorAll(".text-confirm-submit");
    confirmForms.forEach(function (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            if (form.checkValidity()) {
                Swal.fire({
                    title: "Confirm Submission?",
                    text: "Are you sure you want to save this record?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#198754",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, submit it!",
                    cancelButtonText: "Cancel",
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData(form);
                        const submitBtn = form.querySelector('[type="submit"]');
                        const originalBtnText = submitBtn
                            ? submitBtn.innerHTML
                            : "";

                        if (submitBtn) {
                            submitBtn.disabled = true;
                            submitBtn.innerHTML =
                                '<span class="spinner-border spinner-border-sm"></span> Saving...';
                        }

                        $.ajax({
                            url: form.getAttribute("action"),
                            method: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Saved!",
                                    text: "Record has been updated.",
                                    timer: 1500,
                                    showConfirmButton: false,
                                });

                                form.reset();
                                form.classList.remove("was-validated");

                                const modalEl = form.closest(".modal");
                                if (modalEl) {
                                    $(modalEl).modal("hide");
                                }

                                // --- REFRESH LOGIC ---

                                // 1. Handle Outbound Table Highlight
                                if (typeof fetchOutboundTable === "function") {
                                    if (
                                        typeof shouldHighlightOutbound !==
                                        "undefined"
                                    ) {
                                        shouldHighlightOutbound = true;
                                    }
                                    fetchOutboundTable();
                                }

                                // 2. Handle General Inventory Table Highlight
                                if (typeof fetchTable === "function") {
                                    if (
                                        typeof shouldHighlight !== "undefined"
                                    ) {
                                        shouldHighlight = true;
                                    }
                                    fetchTable();
                                }
                            },
                            error: function (xhr) {
                                let errorMsg = "Failed to save record.";
                                if (xhr.status === 422) {
                                    errorMsg = Object.values(
                                        xhr.responseJSON.errors,
                                    )
                                        .flat()
                                        .join("<br>");
                                }
                                Swal.fire("Error", errorMsg, "error");
                            },
                            complete: function () {
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = originalBtnText;
                                }
                            },
                        });
                    }
                });
            }
        });
    });
});

// --- Individual Delete with No Reload ---
document.addEventListener("submit", function (e) {
    // Only target forms with the 'delete-form' class
    if (e.target && e.target.classList.contains("delete-form")) {
        e.preventDefault();

        const form = e.target;

        Swal.fire({
            title: "Are you sure?",
            text: "This record will be permanently removed.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                const url = form.getAttribute("action");
                const formData = new FormData(form);

                fetch(url, {
                    method: "POST", // Laravel reads @method('DELETE') from inside formData
                    body: formData,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        // Use the global token object instead of {{ csrf_token() }}
                        "X-CSRF-TOKEN": window.outboundData.csrfToken,
                        Accept: "application/json",
                    },
                })
                    .then((res) => {
                        if (!res.ok)
                            throw new Error("Server error: " + res.status);
                        return res.json();
                    })
                    .then((data) => {
                        if (data.success) {
                            Swal.fire({
                                toast: true,
                                position: "top-end",
                                icon: "success",
                                title: `items deleted!`,
                                showConfirmButton: false,
                                timer: 1500,
                            });

                            // Refresh table data via AJAX
                            if (typeof fetchOutboundTable === "function") {
                                fetchOutboundTable();
                            }
                        } else {
                            Swal.fire(
                                "Error",
                                data.message || "Could not delete.",
                                "error",
                            );
                        }
                    })
                    .catch((err) => {
                        console.error("Detailed Error:", err);
                        Swal.fire(
                            "Error",
                            "Communication failed. Check console.",
                            "error",
                        );
                    });
            }
        });
    }
});

// Add Outbound with AJAX & Validation
$(document).ready(function () {
    /* =========================================
       1. PERSONNEL SELECTION (OUTBOUND MODAL)
       ========================================= */
    // Using delegation: attach to #personnelList so new items are clickable
    $("#personnelList").on("click", ".personnel-item", function () {
        // Highlight selection
        $(".personnel-item").removeClass("active bg-primary text-white");
        $(this).addClass("active bg-primary text-white");

        // Set hidden input values
        $("#selected_personnel_id").val($(this).data("id"));
        $("#personnelError").removeClass("d-block");

        // Update the display card in the modal
        $("#display_personnel_name").text($(this).data("name"));
        $("#display_branch").text($(this).data("branch"));
        $("#display_dept").text($(this).data("dept"));
        $("#selectedPersonnelCard").removeClass("d-none");
    });

    /* =========================================
       2. PERSONNEL VIEW SELECTION (MANAGEMENT MODAL)
       ========================================= */
    // Using delegation: attach to #personnelListView
    $("#personnelListView").on("click", ".personnel-view-item", function () {
        const $this = $(this);
        const id = $this.data("id");

        // Highlight selection
        $(".personnel-view-item").removeClass("active bg-primary text-white");
        $this.addClass("active bg-primary text-white");

        // Update display view
        $("#view_person_name").text($this.data("name"));
        $("#view_branch").text($this.data("branch"));
        $("#view_dept").text($this.data("dept"));
        $("#selectedPersonnelView").removeClass("d-none");

        // AJAX Fetch assigned items for this person
        const $container = $("#assignedItemsContainer");
        $container.html(
            '<div class="text-muted"><span class="spinner-border spinner-border-sm"></span> Loading items...</div>',
        );

        fetch(`/personnel/${id}/items`, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        })
            .then((res) => res.text())
            .then((html) => {
                $container.html(html);
            })
            .catch(() => {
                $container.html(
                    '<div class="text-danger">Failed to load items.</div>',
                );
            });
    });

    /* =========================================
       3. ADD PERSONNEL FORM (YOUR PREVIOUS CODE)
       ========================================= */
    $("#addPersonnelForm").on("submit", function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const formData = new FormData(this);

        if (this.checkValidity() === false) {
            e.stopPropagation();
            form.addClass("was-validated");
            return;
        }

        submitBtn.prop("disabled", true).text("Adding...");

        $.ajax({
            url: form.attr("action"),
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: { "X-CSRF-TOKEN": $('input[name="_token"]').val() },
            success: function (response) {
                const p = response.personnel;
                const branchName = p.branch ? p.branch.branch_name : "N/A";
                const branchDept = p.branch
                    ? p.branch.branch_department
                    : "N/A";

                // Personnel Management Item
                let managementItem = `
                <div class="personnel-row position-relative border-bottom">
                    <form action="/personnel/${p.personnel_id}" method="POST" class="delete-form position-absolute top-50 end-0 translate-middle-y me-2" style="z-index: 5;">
                        <input type="hidden" name="_token" value="${$('input[name="_token"]').val()}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-link text-danger p-1 border-0 shadow-none hover-scale"><i class="bi bi-trash3-fill"></i></button>
                    </form>
                    <button type="button" class="list-group-item list-group-item-action personnel-view-item border-0 py-2 pe-5"
                            data-id="${p.personnel_id}" data-name="${p.personnel_name}" data-branch="${branchName}" data-dept="${branchDept}">
                        <div class="d-flex flex-column text-start">
                            <strong class="text-dark mb-0">${p.personnel_name}</strong>
                            <small class="text-muted text-uppercase" style="font-size: 0.7rem;">${branchName} | ${branchDept}</small>
                        </div>
                    </button>
                </div>`;

                // Outbound Selection Item
                let selectionItem = `
                <button type="button" class="list-group-item list-group-item-action personnel-item"
                        data-id="${p.personnel_id}" data-name="${p.personnel_name}" data-branch="${branchName}" data-dept="${branchDept}">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <h6 class="mb-1 text-truncate">${p.personnel_name}</h6>
                    </div>
                    <p class="mb-0 small text-muted"><i class="bi bi-building me-1"></i>${branchName} | ${branchDept}</p>
                </button>`;

                $("#personnelListView").prepend(managementItem);
                $("#personnelList").prepend(selectionItem);

                $("#personnelCount").text(
                    parseInt($("#personnelCount").text() || 0) + 1,
                );
                form[0].reset();
                form.removeClass("was-validated");

                Swal.fire({
                    toast: true,
                    position: "top-end",
                    icon: "success",
                    title: "Personnel added!",
                    showConfirmButton: false,
                    timer: 1500,
                });
            },
            complete: function () {
                submitBtn.prop("disabled", false).text("Add Personnel");
            },
        });
    });
});

//paginate no reloading
$(document).on("click", "#pagination-container a", function (e) {
    e.preventDefault();
    let url = $(this).attr("href");

    $.ajax({
        url: url,
        type: "GET",
        dataType: "json", // Crucial: forces jQuery to parse the JSON
        success: function (response) {
            // 'response.table' matches the key you set in your Controller
            $("#table-data").html(response.table);

            window.history.pushState({}, "", url);
            window.syncCheckboxes();
        },
    });
});

function toggleDateReceived(selectElement, id) {
    const container = document.getElementById(`dateReceivedContainer_${id}`);
    if (selectElement.value === "Received") {
        container.style.display = "block";
        container.querySelector("input").setAttribute("required", "required");
    } else {
        container.style.display = "none";
        container.querySelector("input").removeAttribute("required");
        container.querySelector("input").value = ""; // Optional: clear date if not received
    }
}

// QUANTITY VALIDATION
document.querySelectorAll('[id^="return_quantity_"]').forEach((input) => {
    input.addEventListener("input", function () {
        const max = parseInt(this.getAttribute("max"));
        const value = parseInt(this.value);

        if (value > max) {
            this.classList.add("is-invalid");
            this.setCustomValidity(`Cannot exceed ${max}`);
            this.parentElement.querySelector(".invalid-feedback").innerText =
                `Cannot exceed ${max}.`;
        } else if (value < 1 || isNaN(value)) {
            this.classList.add("is-invalid");
            this.setCustomValidity(`Minimum is 1`);
            this.parentElement.querySelector(".invalid-feedback").innerText =
                `Minimum is 1.`;
        } else {
            this.classList.remove("is-invalid");
            this.setCustomValidity("");
        }
    });
});

// DATE VALIDATION (NO PAST DATE)
document.querySelectorAll('input[name="return_date"]').forEach((input) => {
    const today = new Date().toISOString().split("T")[0];

    // set min dynamically (extra safety)
    input.setAttribute("min", today);

    input.addEventListener("input", function () {
        if (this.value < today) {
            this.classList.add("is-invalid");
            this.setCustomValidity("Date cannot be in the past");

            // create feedback if not exists
            let feedback =
                this.parentElement.querySelector(".invalid-feedback");
            if (!feedback) {
                feedback = document.createElement("div");
                feedback.className = "invalid-feedback";
                this.parentElement.appendChild(feedback);
            }

            feedback.innerText = "Date cannot be in the past";
        } else {
            this.classList.remove("is-invalid");
            this.setCustomValidity("");
        }
    });
});
