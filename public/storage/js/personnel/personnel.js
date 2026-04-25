//checking
let selectedOutboundIds = new Set();
let fetchOutboundTable;
let shouldHighlightOutbound = false; // For New Items
let shouldHighlightOutboundUpdated = false; // For Updated Items 🔥 ADDED
let shouldHighlightOutboundReturned = false;

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

                    // 🔥 UPDATED HIGHLIGHT LOGIC FOR NEW, UPDATED, AND RETURNED
                    if (
                        shouldHighlightOutbound ||
                        shouldHighlightOutboundUpdated ||
                        shouldHighlightOutboundReturned
                    ) {
                        const firstRow =
                            tableBody.querySelector("tr:first-child");

                        if (
                            firstRow &&
                            !firstRow.innerText.includes("No Record Found")
                        ) {
                            let config = {
                                color: "#d1e7dd", // Default Green
                                badgeClass: "bg-success",
                                text: "New",
                            };

                            if (shouldHighlightOutboundUpdated) {
                                config = {
                                    color: "#cfe2ff", // Blue
                                    badgeClass: "bg-primary",
                                    text: "Updated",
                                };
                            } else if (shouldHighlightOutboundReturned) {
                                // 🔥 YELLOW/ORANGE CONFIG FOR RETURNED
                                config = {
                                    color: "#fff3cd", // Light Yellow
                                    badgeClass: "bg-warning text-dark",
                                    text: "Returned",
                                };
                            }

                            applyRowHighlight(firstRow, config);
                        }

                        // Reset all flags
                        shouldHighlightOutbound = false;
                        shouldHighlightOutboundUpdated = false;
                        shouldHighlightOutboundReturned = false;
                    }

                    if (typeof syncCheckboxes === "function") syncCheckboxes();
                    if (typeof initAllReturnReasonSelects === "function") {
                        initAllReturnReasonSelects();
                    }
                })
                .catch((err) => console.error("Fetch Error:", err));
        }, 300);
    };

    // --- 3. HELPER: APPLY HIGHLIGHT ---
    function applyRowHighlight(row, config) {
        // 1. Set background
        row.style.backgroundColor = config.color;
        row.style.transition = "none";

        // 2. Add Badge (Assuming Item Name is in Cell index 1)
        const nameCell = row.cells[1];
        if (nameCell) {
            const badge = document.createElement("span");
            badge.className = `badge rounded-pill ${config.badgeClass} ms-2 animate__animated animate__fadeIn`;
            badge.style.fontSize = "0.7rem";
            badge.innerText = config.text;
            nameCell.appendChild(badge);
        }

        // 3. Fade out
        setTimeout(() => {
            row.style.transition = "background-color 2.0s ease-out";
            row.style.backgroundColor = "transparent";
        }, 1500);
    }

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

    // 2. PDF / Excel export (checked rows only when count > 0; else current filters)
    const exportPdfBtn = document.getElementById("export_pdf_btn");
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener("click", function (e) {
            e.preventDefault();
            window.open(buildOutboundExportUrl("pdf"), "_blank");
        });
    }

    const exportExcelBtn = document.getElementById("export_excel_btn");
    if (exportExcelBtn) {
        exportExcelBtn.addEventListener("click", function (e) {
            e.preventDefault();
            window.open(buildOutboundExportUrl("excel"), "_blank");
        });
    }

    if (typeof updateBulkButtonUI === "function") {
        updateBulkButtonUI();
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
    const count = selectedOutboundIds.size;
    if (bulkDeleteBtn) {
        bulkDeleteBtn.disabled = count === 0;
        bulkDeleteBtn.innerHTML = `<i class="bi bi-trash"></i> Delete Selected (${count})`;
    }
    const pdfBtn = document.getElementById("export_pdf_btn");
    if (pdfBtn) {
        pdfBtn.innerHTML = `<i class="bi bi-file-earmark-pdf"></i> Print PDF (${count})`;
    }
    const excelBtn = document.getElementById("export_excel_btn");
    if (excelBtn) {
        excelBtn.innerHTML = `<i class="bi bi-file-earmark-excel"></i> Excel (${count})`;
    }
}

function buildOutboundExportUrl(exportType) {
    const params = new URLSearchParams({ export: exportType });
    const search =
        document.getElementById("OutboundSearch")?.value?.trim() || "";
    if (search) {
        params.set("search", search);
    }
    const personnel =
        document.querySelector("select[name='personnel']")?.value || "";
    if (personnel) {
        params.set("personnel", personnel);
    }
    const department =
        document.querySelector("select[name='department']")?.value || "";
    if (department) {
        params.set("department", department);
    }
    const branch = document.querySelector("select[name='branch']")?.value || "";
    if (branch) {
        params.set("branch", branch);
    }
    const remarks =
        document.querySelector("select[name='remarks']")?.value || "";
    if (remarks) {
        params.set("remarks", remarks);
    }
    if (selectedOutboundIds.size > 0) {
        params.set("ids", Array.from(selectedOutboundIds).join(","));
    }
    return `${window.outboundData.routes.index}?${params.toString()}`;
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

// 🔥 UPDATE, RETURN, ADD ACTIONS 🔥
$(document).ready(function () {
    // --- 1. UPDATE RECORD ---
    $(document).on("submit", ".needs-validation-update", function (e) {
        e.preventDefault();
        let form = this;
        let $form = $(form);

        if (!form.checkValidity()) {
            form.classList.add("was-validated");
            return;
        }

        Swal.fire({
            title: "Update Record?",
            text: "Are you sure you want to update this item?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, update it!",
            cancelButtonText: "No",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: $form.attr("action"),
                    type: "POST",
                    data: $form.serialize(),
                    success: function (response) {
                        let modalEl = $form.closest(".modal");
                        if (modalEl.length) {
                            let modal = bootstrap.Modal.getInstance(modalEl[0]);
                            if (modal) modal.hide();
                            $(".modal-backdrop").remove();
                            $("body")
                                .removeClass("modal-open")
                                .css("overflow", "");
                        }

                        $form.removeClass("was-validated");
                        form.reset();

                        Swal.fire({
                            toast: true,
                            position: "top-end",
                            icon: "success",
                            title: response.message || "Updated successfully!",
                            showConfirmButton: false,
                            timer: 1500,
                        });

                        shouldHighlightOutboundUpdated = true;
                        if (typeof fetchOutboundTable === "function")
                            fetchOutboundTable();
                    },
                    error: function (xhr) {
                        let msg =
                            xhr.responseJSON?.message ||
                            "Failed to update record.";
                        Swal.fire("Error", msg, "error");
                    },
                });
            }
        });
    });

    // --- 2. RETURN RECORD ---
    $(document).on("submit", ".needs-validation-return", function (e) {
        e.preventDefault();
        let form = this;
        let $form = $(form);

        if (!form.checkValidity()) {
            form.classList.add("was-validated");
            return;
        }

        Swal.fire({
            title: "Confirm Return?",
            text: "Adjust stock and record this return.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ffc107",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, return it!",
            cancelButtonText: "No",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: $form.attr("action"),
                    type: "POST",
                    data: $form.serialize(),
                    success: function (response) {
                        let modalEl = $form.closest(".modal");
                        if (modalEl.length) {
                            let modal = bootstrap.Modal.getInstance(modalEl[0]);
                            if (modal) modal.hide();
                            $(".modal-backdrop").remove();
                            $("body")
                                .removeClass("modal-open")
                                .css("overflow", "");
                        }

                        $form.removeClass("was-validated");
                        form.reset();

                        Swal.fire({
                            toast: true,
                            position: "top-end",
                            icon: "success",
                            title: response.message || "Item returned!",
                            showConfirmButton: false,
                            timer: 1500,
                        });

                        shouldHighlightOutboundReturned = true;
                        if (typeof fetchOutboundTable === "function")
                            fetchOutboundTable();
                    },
                    error: function (xhr) {
                        let msg = "Failed to return item.";
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            const first = Object.values(
                                xhr.responseJSON.errors,
                            )[0];
                            msg = Array.isArray(first) ? first[0] : first;
                        } else if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire("Error", msg, "error");
                    },
                });
            }
        });
    });

    // --- 3. ADD OUTBOUND RECORD ---
    // --- 3. ADD OUTBOUND RECORD ---
    $(document).on("submit", ".text-confirm-submit", function (e) {
        e.preventDefault();
        let form = this;
        let $form = $(form);

        if (!form.checkValidity()) {
            form.classList.add("was-validated");
            return;
        }

        Swal.fire({
            title: "Record Outbound?",
            text: "Are you sure you want to save this record?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#198754",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, save it!",
            cancelButtonText: "No"
        }).then((result) => {
            if (result.isConfirmed) {
                
                // 🔥 GRAB THE ISSUED QUANTITY AND ITEM ID BEFORE FORM RESETS 🔥
                let issuedQty = parseInt($form.find('input[name="personnel_item_quantity"]').val());
                let selectedItemId = $form.find('input[name="item_id"]').val();

                $.ajax({
                    url: $form.attr("action"),
                    type: "POST",
                    data: $form.serialize(),
                    success: function (response) {
                        
                        // 1. UPDATE STOCK VISUALLY
                        let itemBtn = $(`.item-btn[data-id='${selectedItemId}']`);
                        if (itemBtn.length) {
                            let currentQty = parseInt(itemBtn.attr("data-qty"));
                            let newQty = currentQty - issuedQty;
                            
                            itemBtn.attr("data-qty", newQty);
                            
                            let badge = itemBtn.find('.badge');
                            if (badge.length) {
                                badge.text(`${newQty} Left`);
                                if (newQty <= 0) {
                                    badge.removeClass('bg-success').addClass('bg-danger');
                                    // Fix the white text bug by removing active classes early
                                    itemBtn.removeClass("active bg-success text-white");
                                    itemBtn.addClass('disabled bg-light opacity-75').attr('disabled', true);
                                }
                            }
                        }

                        // 2. 🔥 FULLY RESET THE CUSTOM UI 🔥
                        
                        // Clear Item Selection
                        $(".item-btn").removeClass("active bg-success text-white");
                        $(".item-btn .text-light").removeClass("text-light").addClass("text-muted");
                        $("#selectedItemCard").addClass("d-none");
                        $("#selected_item_id").val("");

                        // Clear Personnel Selection
                        $(".personnel-item").removeClass("active bg-primary text-white");
                        $(".personnel-item .text-light").removeClass("text-light").addClass("text-muted");
                        $("#selectedPersonnelCard").addClass("d-none");
                        $("#selected_personnel_id").val("");

                        // Lock Quantity Input again
                        $("#personnel_item_quantity").prop("disabled", true).val("").removeClass("is-invalid");
                        $("#qtyAvailableText").addClass("d-none");
                        $("#submitBtn").prop("disabled", true);

                        // 3. CLOSE MODAL & SHOW TOAST
                        let modalEl = $form.closest(".modal");
                        if (modalEl.length) {
                            let modal = bootstrap.Modal.getInstance(modalEl[0]);
                            if (modal) modal.hide();
                            $('.modal-backdrop').remove(); 
                            $('body').removeClass('modal-open').css('overflow', '');
                        }

                        $form.removeClass("was-validated");
                        form.reset();

                        Swal.fire({
                            toast: true,
                            position: "top-end",
                            icon: "success",
                            title: response.message || "Record added!",
                            showConfirmButton: false,
                            timer: 1500,
                        });

                        shouldHighlightOutbound = true;
                        if (typeof fetchOutboundTable === "function") fetchOutboundTable();
                    },
                    error: function (xhr) {
                        let msg = xhr.responseJSON?.message || "Failed to add record.";
                        Swal.fire("Error", msg, "error");
                    }
                });
            }
        });
    });
});


const RETURN_REASONS_GOOD = [
    ["no_longer_needed", "No longer needed"],
    ["end_of_assignment", "End of assignment / project"],
    ["replaced_upgraded", "Replaced or upgraded equipment"],
    ["transfer_reassign", "Transfer / reassignment"],
    ["other", "Other (describe on right side field)"],
];

const RETURN_REASONS_DAMAGED = [
    ["physical_damage", "Physical damage (drops, cracks, etc.)"],
    ["malfunction", "Malfunction / not working"],
    ["wear_unusable", "Wear and tear — unusable"],
    ["missing_accessories", "Missing parts or accessories"],
    ["other", "Other (describe below)"],
];

function fillReturnReasonPresetSelect(selectEl, isGood) {
    if (!selectEl || selectEl.disabled) return;
    const list = isGood ? RETURN_REASONS_GOOD : RETURN_REASONS_DAMAGED;
    const prev = selectEl.value;
    selectEl.innerHTML = list
        .map(([v, l]) => `<option value="${v}">${l}</option>`)
        .join("");
    const hasPrev = list.some(([v]) => v === prev);
    selectEl.value = hasPrev ? prev : list[0][0];
    const form = selectEl.closest("form");
    if (form) syncReturnReasonDetailRequired(form);
}

function syncReturnReasonDetailRequired(form) {
    const preset = form.querySelector(".return-reason-preset-select");
    const detail = form.querySelector(".return-reason-detail-input");
    if (!preset || !detail || preset.disabled) return;
    detail.required = preset.value === "other";
}

function initAllReturnReasonSelects() {
    document.querySelectorAll('[id^="return_condition_"]').forEach((cond) => {
        const suffix = cond.id.replace("return_condition_", "");
        const preset = document.getElementById(
            "return_reason_preset_" + suffix,
        );
        if (preset && !preset.disabled) {
            fillReturnReasonPresetSelect(preset, cond.value === "Good");
        }
    });
}

window.initAllReturnReasonSelects = initAllReturnReasonSelects;

document.addEventListener("change", function (e) {
    const t = e.target;
    if (t.id && t.id.startsWith("return_condition_")) {
        const suffix = t.id.replace("return_condition_", "");
        const preset = document.getElementById(
            "return_reason_preset_" + suffix,
        );
        if (preset) {
            fillReturnReasonPresetSelect(preset, t.value === "Good");
        }
    }
    if (t.classList && t.classList.contains("return-reason-preset-select")) {
        const form = t.closest("form");
        if (form) syncReturnReasonDetailRequired(form);
    }
});

document.addEventListener("show.bs.modal", function (e) {
    if (!e.target.id || !e.target.id.startsWith("returnOutboundModal_")) return;
    const modal = e.target;
    const suffix = modal.id.replace("returnOutboundModal_", "");
    const cond = document.getElementById("return_condition_" + suffix);
    const preset = document.getElementById("return_reason_preset_" + suffix);
    if (cond && preset && !preset.disabled) {
        fillReturnReasonPresetSelect(preset, cond.value === "Good");
    }
});

document.addEventListener("DOMContentLoaded", initAllReturnReasonSelects);

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

// QUANTITY AND DATE VALIDATION (Delegated to document)
document.addEventListener("input", function (e) {
    // QUANTITY VALIDATION
    if (e.target && e.target.id && e.target.id.startsWith("return_quantity_")) {
        const input = e.target;
        const max = parseInt(input.getAttribute("max"));
        const value = parseInt(input.value);

        if (value > max) {
            input.classList.add("is-invalid");
            input.setCustomValidity(`Cannot exceed ${max}`);
            input.parentElement.querySelector(".invalid-feedback").innerText =
                `Cannot exceed ${max}.`;
        } else if (value < 1 || isNaN(value)) {
            input.classList.add("is-invalid");
            input.setCustomValidity(`Minimum is 1`);
            input.parentElement.querySelector(".invalid-feedback").innerText =
                `Minimum is 1.`;
        } else {
            input.classList.remove("is-invalid");
            input.setCustomValidity("");
        }
    }

    // DATE VALIDATION (NO PAST DATE)
    if (e.target && e.target.name === "return_date") {
        const input = e.target;
        const today = new Date().toISOString().split("T")[0];

        // set min dynamically (extra safety)
        input.setAttribute("min", today);

        if (input.value < today) {
            input.classList.add("is-invalid");
            input.setCustomValidity("Date cannot be in the past");

            // create feedback if not exists
            let feedback =
                input.parentElement.querySelector(".invalid-feedback");
            if (!feedback) {
                feedback = document.createElement("div");
                feedback.className = "invalid-feedback";
                input.parentElement.appendChild(feedback);
            }

            feedback.innerText = "Date cannot be in the past";
        } else {
            input.classList.remove("is-invalid");
            input.setCustomValidity("");
        }
    }
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
       3. ADD PERSONNEL FORM
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
            // 🔥 FIX: Changed from $('input[name="_token"]').val() to global token to ensure safety
            headers: { "X-CSRF-TOKEN": window.outboundData.csrfToken },
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
                        <input type="hidden" name="_token" value="${window.outboundData.csrfToken}">
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
