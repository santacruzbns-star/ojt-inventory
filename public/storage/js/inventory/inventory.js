//this for the Manage Categories section in inventory page  okay?? yes sir
//no need to change anything here its working.

//search bar for inventory page
let fetchTable;
let shouldHighlight = false; // For New Items (Green)
let shouldHighlightUpdated = false; // For Updated Items (Blue)

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("inventorySearch");
    const remarkSelect = document.querySelector("select[name='remark']");
    const categorySelect = document.querySelector("select[name='category']");
    const brandSelect = document.querySelector("select[name='brand']");
    const tableBody = document.getElementById("table-data");

    if (!searchInput || !tableBody) return;

    let timer;

    fetchTable = function () {
        const query = searchInput.value.trim();
        const remark = remarkSelect ? remarkSelect.value : "";
        const category = categorySelect ? categorySelect.value : "";
        const brand = brandSelect ? brandSelect.value : "";

        const urlParams = new URLSearchParams(window.location.search);
        const currentPage = urlParams.get("page") || 1;

        clearTimeout(timer);
        timer = setTimeout(() => {
            const baseUrl = window.routes.inventoryIndex;
            const url = `${baseUrl}?search=${encodeURIComponent(query)}&remark=${encodeURIComponent(remark)}&category=${encodeURIComponent(category)}&brand=${encodeURIComponent(brand)}&page=${currentPage}&ajax=1`;

            fetch(url, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
            })
                .then((res) => res.json())
                .then((data) => {
                    tableBody.innerHTML = data.table;
                    tableBody.style.opacity = "1";

                    const firstRow = tableBody.querySelector("tr:first-child");
                    if (!firstRow) return;

                    // --- LOGIC FOR NEW OR UPDATED HIGHLIGHT ---
                    let highlightColor = "";
                    let badgeClass = "";
                    let badgeText = "";

                    if (shouldHighlight) {
                        highlightColor = "#d1e7dd"; // Light Green
                        badgeClass = "bg-success";
                        badgeText = "New";
                    } else if (shouldHighlightUpdated) {
                        highlightColor = "#cfe2ff"; // Light Blue
                        badgeClass = "bg-primary";
                        badgeText = "Updated";
                    }

                    if (highlightColor) {
                        // 1. Highlight the row
                        firstRow.style.backgroundColor = highlightColor;
                        firstRow.style.transition = "none";

                        // 2. Append the specific badge
                        const nameCell = firstRow.cells[1];
                        if (nameCell) {
                            const badge = document.createElement("span");
                            badge.className = `badge rounded-pill ${badgeClass} ms-2 animate__animated animate__fadeIn`;
                            badge.style.fontSize = "0.7rem";
                            badge.innerText = badgeText;
                            nameCell.appendChild(badge);
                        }

                        // 3. Fade out the row highlight
                        setTimeout(() => {
                            firstRow.style.transition =
                                "background-color 2.0s ease-out";
                            firstRow.style.backgroundColor = "transparent";
                        }, 1500);
                    }

                    // Reset both flags
                    shouldHighlight = false;
                    shouldHighlightUpdated = false;
                })
                .catch((err) => {
                    console.error("Fetch Error:", err);
                    tableBody.style.opacity = "1";
                });
        }, 300);
    };

    // Event Listeners
    searchInput.addEventListener("keyup", fetchTable);
    if (remarkSelect) remarkSelect.addEventListener("change", fetchTable);
    if (categorySelect) categorySelect.addEventListener("change", fetchTable);
    if (brandSelect) brandSelect.addEventListener("change", fetchTable);
});

$(document).ready(function () {
    // add category form submission with ajax
    $(document).on("submit", ".category-form", function (e) {
        e.preventDefault();

        let form = this;
        let url = $(form).attr("action");

        // SIMPLE VALIDATION
        let name = $("#category_name").val().trim();

        if (name === "") {
            $("#category_name").addClass("is-invalid");
            return;
        }

        $.ajax({
            url: url,
            type: "POST",
            data: $(form).serialize(),

            success: function (response) {
                // 1. Update the Category List UI (What you already had)
                let icon = response.item_category_icon
                    ? `<i class="bi ${response.item_category_icon} me-2 fs-5"></i>`
                    : "";

                let newItem = `
                    <li class="list-group-item d-flex justify-content-between align-items-center category-item">
                        <span class="d-flex align-items-center">
                            ${icon}
                            ${response.item_category_name}
                        </span>
                        <form action="/item-category/${response.item_category_id}" method="POST" class="delete-form">
                            <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-sm btn-danger">✕</button>
                        </form>
                    </li>
                `;

                $("#categoryList").prepend(newItem);

                // 🔥 2. SYNC WITH THE ITEM MODAL DROPDOWN
                // This adds the new category to the "Add Item" select menu immediately
                let newOption = `
                    <option value="${response.item_category_id}" 
                            data-name="${response.item_category_name}" 
                            data-icon="${response.item_category_icon || "bi-question-circle"}">
                        ${response.item_category_name}
                    </option>`;

                // Append to the dropdown (Check if your ID is #item_category_id)
                $("#item_category_id").append(newOption);

                // 🔥 3. UPDATE GLOBAL DATA FOR AUTO-GENERATION
                // This ensures "Category 001" logic works for this new category right away
                if (window.itemsData) {
                    // We add a dummy entry so the filter finds this category ID
                    // and starts the count at 001
                    window.itemsData.push({
                        item_id: null,
                        name: response.item_category_name + " 000",
                        category_id: response.item_category_id,
                    });
                }

                // 4. Reset Form and Validation
                form.reset();
                $("#category_name").removeClass("is-invalid is-valid");
                form.classList.remove("was-validated");
                $(form)
                    .find(".is-invalid, .is-valid")
                    .removeClass("is-invalid is-valid");
                $(form).find(".invalid-feedback, .valid-feedback").hide();

                // reset icon UI
                $("#selectedIcon").attr("class", "bi");
                $("#selectedIconText").text("None");

                Swal.fire({
                    toast: true,
                    position: "top-end",
                    icon: "success",
                    title: "Category added!",
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true,
                });
            },

            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let firstError = Object.values(errors)[0][0];

                    Swal.fire({
                        icon: "warning",
                        title: "Validation Error",
                        text: firstError,
                    });
                } else {
                    Swal.fire("Error", "Failed to add category.", "error");
                }
            },
        });
    });

    $("#category_name").on("input", function () {
        $(this).removeClass("is-invalid");
    });
});
   

//searchbar of the category list in the manage categories section of inventory page
$(document).ready(function () {
    $("#categorySearch").on("keyup", function () {
        let value = $(this).val().toLowerCase();

        $("#categoryList .category-item").each(function () {
            let text = $(this).text().toLowerCase();

            if (text.includes(value)) {
                $(this).removeClass("d-none");
            } else {
                $(this).addClass("d-none");
            }
        });
    });

    //for the icon selector
    $(document).on("click", ".icon-option", function (e) {
        e.preventDefault();

        const value = $(this).data("value");
        const text = $(this).text().trim();

        $("#category_icon").val(value);

        $("#selectedIcon").attr("class", "bi " + value);
        $("#selectedIconText").text(text);
    });
});

function buildInventoryListExportUrl(exportType) {
    const params = new URLSearchParams();
    params.set("export", exportType);

    const searchInput = document.getElementById("inventorySearch");
    if (searchInput?.value?.trim()) {
        params.set("search", searchInput.value.trim());
    }

    const remark = document.querySelector("select[name='remark']")?.value || "";
    if (remark !== "") {
        params.set("remark", remark);
    }
    const category = document.querySelector("select[name='category']")?.value || "";
    if (category !== "") {
        params.set("category", category);
    }
    const brand = document.querySelector("select[name='brand']")?.value || "";
    if (brand !== "") {
        params.set("brand", brand);
    }

    if (selectedIds.size > 0) {
        params.set("ids", Array.from(selectedIds).join(","));
    }

    return `${window.routes.inventoryIndex}?${params.toString()}`;
}

document.addEventListener("DOMContentLoaded", function () {
    const pdfBtn = document.getElementById("export_pdf_btn");
    if (pdfBtn) {
        pdfBtn.addEventListener("click", function (e) {
            e.preventDefault();
            window.open(buildInventoryListExportUrl("pdf"), "_blank");
        });
    }

    const excelBtn = document.getElementById("export_excel_btn");
    if (excelBtn) {
        excelBtn.addEventListener("click", function (e) {
            e.preventDefault();
            window.open(buildInventoryListExportUrl("excel"), "_blank");
        });
    }

    if (typeof updateSelectionActionLabels === "function") {
        updateSelectionActionLabels();
    }
});

//ambot para asa ni siya pero basin useful ni siya sa future, this is for the form validation in the inventory page, it will prevent the form from submitting if there are invalid fields and will show the validation messages.
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

//to auto generate item name based on the selected category in the inventory page, it will also show the category icon in the item name input field.
document.addEventListener("DOMContentLoaded", function () {
    const categorySelect = document.getElementById("item_category_id");
    const itemNameInput = document.getElementById("item_name");
    const iconPreview = document.getElementById("categoryIconPreview");

    let manuallyEdited = false;

    if (!categorySelect || !itemNameInput) return;

    itemNameInput.addEventListener("input", function () {
        manuallyEdited = true;
    });

    categorySelect.addEventListener("change", function () {
        const selectedOption = this.options[this.selectedIndex];
        const categoryId = this.value;
        const iconClass = selectedOption.getAttribute("data-icon");

        // Icon preview
        if (iconPreview) {
            iconPreview.className =
                "bi position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary";
            iconPreview.classList.add(
                iconClass ? iconClass : "bi-question-circle",
            );
        }

        if (!categoryId) return;

        // 🔥 FETCH REAL LATEST FROM BACKEND
        fetch(`/get-latest-item?category_id=${categoryId}`)
            .then((res) => res.json())
            .then((data) => {
                if (!manuallyEdited || itemNameInput.value === "") {
                    if (data.item_name) {
                        itemNameInput.value = data.item_name;
                    } else {
                        itemNameInput.value = "";
                    }
                    manuallyEdited = false;
                }
            })
            .catch((err) => {
                console.error("Fetch latest item error:", err);
            });
    });
});

// Duplicate item check with merge logic and status separation
$(document).ready(function () {
    // Add item form submission with ajax
    $("form.item-form").on("submit", function (e) {
        e.preventDefault();
        let form = this;
        let $form = $(form);
        let url = $form.attr("action");

        // Bootstrap Validation Check
        if (!form.checkValidity()) {
            form.classList.add("was-validated");
            return;
        }

        // 1. Perform the Duplicate Check (Now checks Name, Category, Brand, Serial, AND Status)
        $.ajax({
            url: window.routes.checkDuplicate,
            type: "POST",
            data: $form.serialize(),
            success: function (response) {
                // response.exists is only true if ALL fields (including Category) match
                if (response.exists) {
                    Swal.fire({
                        title: "Exact Match Found!",
                        // Updated text to reflect the Category requirement
                        text: "This item already exists in this Category with the same Brand, Serial, and Status. Merge the quantities?",
                        icon: "info",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, Merge it!",
                        cancelButtonText: "No, Cancel",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            saveItem(form, url);
                        }
                    });
                } else {
                    /** * If the Name exists but Category is different, 
                     * response.exists will be false. 
                     * It proceeds here to create a new record.
                     */
                    saveItem(form, url);
                }
            },
            error: function () {
                Swal.fire(
                    "Error",
                    "Could not verify duplicate status.",
                    "error",
                );
            },
        });
    });


    // Helper function to handle the actual AJAX saving
    function saveItem(form, url) {
        $.ajax({
            url: url,
            type: "POST",
            data: $(form).serialize(),
            success: function (res) {
                // Reset Form
                form.reset();
                $(form).removeClass("was-validated");

                // Reset Category Icon Preview (if applicable)
                const iconPreview = document.getElementById(
                    "categoryIconPreview",
                );
                if (iconPreview) {
                    iconPreview.className =
                        "bi position-absolute top-50 start-0 translate-middle-y ms-3 text-secondary";
                }

                // Close Modal
                let modalEl = document.getElementById("item_modal");
                let modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                // Success Notification
                Swal.fire({
                    toast: true,
                    position: "top-end",
                    icon: "success",
                    title: res.message || "Action successful!",
                    showConfirmButton: false,
                    timer: 2000,
                });

                // Refresh Table and Highlight
                shouldHighlight = true;
                if (typeof fetchTable === "function") {
                    fetchTable();
                }
            },
            error: function (xhr) {
                // Handle 422 Serial Conflict or Validation errors
                if (xhr.status === 422) {
                    let msg = xhr.responseJSON.message || "Validation Error";
                    Swal.fire("Action Denied", msg, "warning");
                } else {
                    Swal.fire("Error", "Failed to save item.", "error");
                }
            },
        });
    }
});
//basta mga sweetalert

//confirmation for update item in inventory page
document.addEventListener("DOMContentLoaded", function () {
    // We use jQuery here to make the AJAX call easier to read
    $(document).on("submit", ".needs-validation-update", function (e) {
        e.preventDefault(); // Stop the initial reload

        let form = this;
        let $form = $(form);
        let url = $form.attr("action");

        Swal.fire({
            title: "Update Item?",
            text: "Are you sure you want to update this item?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, update it!",
            cancelButtonText: "No",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: $form.serialize(),
                    success: function (response) {
                        // 1. Hide the modal
                        let modalEl = $form.closest(".modal");
                        if (modalEl.length) {
                            let modal = bootstrap.Modal.getInstance(modalEl[0]);
                            if (modal) modal.hide();
                        }

                        // 2. Show success toast
                        Swal.fire({
                            toast: true,
                            position: "top-end",
                            icon: "success",
                            title: "Updated successfully!",
                            showConfirmButton: false,
                            timer: 1500,
                        });

                        // 🔥 ADD THIS LINE HERE:
                        shouldHighlightUpdated = true;

                        // 3. REFRESH THE TABLE DATA
                        if (typeof fetchTable === "function") {
                            fetchTable();
                        }
                    },
                    error: function (xhr) {
                        Swal.fire("Error", "Failed to update item.", "error");
                    },
                });
            }
        });
    });
});

//delete category with ajax and sweetalert confirmation in manage categories section of inventory page
$(document).on("submit", ".delete-form", function (e) {
    e.preventDefault();

    let form = this;
    let url = $(form).attr("action");

    Swal.fire({
        title: "Delete Category?",
        text: "This will permanently remove the category.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    _method: "DELETE",
                },
                success: function (response) {
                    // Success logic
                    $(form)
                        .closest("li")
                        .fadeOut(200, function () {
                            $(this).remove();
                        });

                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "success",
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500,
                    });
                },
                error: function (xhr) {
                    // This handles the 'Category in use' error
                    if (xhr.status === 422) {
                        Swal.fire({
                            icon: "error",
                            title: "Action Denied",
                            text: xhr.responseJSON.message, // Shows the "Cannot delete..." message
                        });
                    } else {
                        Swal.fire(
                            "Error",
                            "Failed to delete category.",
                            "error",
                        );
                    }
                },
            });
        }
    });
});

// delete item individually with ajax and sweetalert confirmation
$(document).on(
    "submit",
    'form[action*="inventory"][method="POST"]',
    function (e) {
        if (!$(this).find('input[name="_method"][value="DELETE"]').length)
            return;
        e.preventDefault();

        let form = this;
        let url = $(form).attr("action");

        // 1. Get current quantity from the table row
        let currentRow = $(form).closest("tr");
        let quantityText = currentRow.find(".item-qty-value").text().trim();
        let currentQty = parseInt(quantityText) || 0;

        // 2. If already 0, just delete the record
        if (currentQty <= 0) {
            Swal.fire({
                title: "Delete Record?",
                text: "This item is out of stock. Delete the entire record?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                confirmButtonText: "Yes, delete it",
            }).then((result) => {
                if (result.isConfirmed) {
                    executeAjaxDelete(url, "", currentRow);
                }
            });
            return;
        }

        // 3. Ask for quantity to remove
        Swal.fire({
            title: "Remove Stock",
            text: `Currently ${currentQty} in stock. How many to remove?`,
            icon: "warning",
            input: "number",
            inputAttributes: {
                min: 1,
                max: currentQty,
                step: 1,
            },
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Confirm",
            cancelButtonText: "Cancel",
            inputPlaceholder: "Leave empty to delete entire record",
        }).then((result) => {
            if (result.isConfirmed) {
                executeAjaxDelete(url, result.value, currentRow);
            }
        });
    },
);

// Helper function to keep the code clean
function executeAjaxDelete(url, deleteQty, rowElement) {
    $.ajax({
        url: url,
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            _method: "DELETE",
            delete_qty: deleteQty,
        },
        success: function (response) {
            if (response.deleted_entirely) {
                rowElement.fadeOut(300, function () {
                    $(this).remove();
                    if (typeof fetchTable === "function") fetchTable();
                });
            } else {
                if (typeof fetchTable === "function") fetchTable();
            }

            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: response.message,
                showConfirmButton: false,
                timer: 1500,
            });
        },
        error: function () {
            Swal.fire("Error", "Failed to process request.", "error");
        },
    });
}

//bulk deleteings
// 1. DATA STORE: Persistent memory for selected IDs
let selectedIds = new Set();

const selectAll = document.getElementById("select_all");
const bulkDeleteBtn = document.getElementById("bulk_delete_btn");

/**
 * 2. THE SYNC FUNCTION
 * This "re-paints" the blue checks every time the table content changes.
 * CALL THIS inside your pagination AJAX success callback!
 */
window.syncCheckboxes = function () {
    const checkboxes = document.querySelectorAll(".select_item");

    checkboxes.forEach((cb) => {
        // Force the blue check based on our memory
        cb.checked = selectedIds.has(cb.value);
    });

    // Update the top "Select All" checkbox status for this page
    if (checkboxes.length > 0) {
        selectAll.checked = [...checkboxes].every((cb) =>
            selectedIds.has(cb.value),
        );
    } else {
        selectAll.checked = false;
    }

    updateSelectionActionLabels();
};

function updateSelectionActionLabels() {
    const n = selectedIds.size;
    if (bulkDeleteBtn) {
        bulkDeleteBtn.disabled = n === 0;
        bulkDeleteBtn.innerHTML = `<i class="bi bi-trash"></i> Delete Selected (${n})`;
    }
    const pdfBtn = document.getElementById("export_pdf_btn");
    if (pdfBtn) {
        pdfBtn.innerHTML = `<i class="bi bi-file-earmark-pdf"></i> Print PDF (${n})`;
    }
    const excelBtn = document.getElementById("export_excel_btn");
    if (excelBtn) {
        excelBtn.innerHTML = `<i class="bi bi-file-earmark-excel"></i> Excel (${n})`;
    }
}

// 3. LISTEN FOR CHANGES (Individual Checkboxes)
document.addEventListener("change", function (e) {
    if (e.target.classList.contains("select_item")) {
        const id = e.target.value;
        if (e.target.checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }
        updateSelectionActionLabels();

        const checkboxes = document.querySelectorAll(".select_item");
        selectAll.checked = [...checkboxes].every((cb) =>
            selectedIds.has(cb.value),
        );
    }
});

// 4. HANDLE "SELECT ALL"
selectAll.addEventListener("change", function () {
    const checkboxes = document.querySelectorAll(".select_item");
    checkboxes.forEach((cb) => {
        cb.checked = this.checked;
        if (this.checked) {
            selectedIds.add(cb.value);
        } else {
            selectedIds.delete(cb.value);
        }
    });
    updateSelectionActionLabels();
});

// 5. BULK DELETE ACTION
bulkDeleteBtn.addEventListener("click", function () {
    if (selectedIds.size === 0) return;

    Swal.fire({
        title: `Delete ${selectedIds.size} item(s)?`,
        text: "This action cannot be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        confirmButtonText: "Yes, delete!",
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(window.routes.bulkDelete, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content",
                    ), // Ensure CSRF is pulled correctly
                },
                body: JSON.stringify({
                    ids: Array.from(selectedIds),
                }),
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        // 1. Visually fade out the deleted rows
                        selectedIds.forEach((id) => {
                            const cb = document.querySelector(
                                `.select_item[value="${id}"]`,
                            );
                            if (cb) {
                                $(cb)
                                    .closest("tr")
                                    .fadeOut(300, function () {
                                        $(this).remove();
                                    });
                            }
                        });

                        // 2. Clear the memory immediately
                        selectedIds.clear();
                        updateSelectionActionLabels();
                        if (selectAll) selectAll.checked = false;

                        // 3. Success Notification
                        Swal.fire({
                            toast: true,
                            position: "top-end",
                            icon: "success",
                            title: "Items deleted!",
                            showConfirmButton: false,
                            timer: 1500,
                        });

                        // 4. 🔥 REFILL THE TABLE: Just like the individual delete
                        // This pulls items from the next page to fill the gaps
                        setTimeout(() => {
                            if (typeof fetchTable === "function") fetchTable();
                        }, 400); // Small delay to allow fadeOut to finish
                    }
                })
                .catch((err) => {
                    console.error("Bulk Delete Error:", err);
                    Swal.fire("Error", "Bulk delete failed.", "error");
                });
        }
    });
});

/** Serial numbers imply a single unit: force quantity to 1 and lock the field. */
function applySerialQuantityLock(serialInput) {
    const form = serialInput.closest("form");
    if (!form) return;
    const qtyInput = form.querySelector('input[name="item_quantity"]');
    if (!qtyInput) return;
    const hasSerial = serialInput.value.trim() !== "";
    if (hasSerial) {
        qtyInput.value = "1";
        qtyInput.setAttribute("min", "1");
        qtyInput.setAttribute("max", "1");
        qtyInput.readOnly = true;
    } else {
        qtyInput.readOnly = false;
        qtyInput.removeAttribute("max");
        qtyInput.setAttribute("min", "1");
        if (form.querySelector('input[name="_method"][value="PUT"]')) {
            qtyInput.setAttribute("max", "9999");
        }
    }
}

document.addEventListener("input", function (e) {
    if (e.target.matches('input[name="item_serialno"]')) {
        applySerialQuantityLock(e.target);
    }
});
document.addEventListener("change", function (e) {
    if (e.target.matches('input[name="item_serialno"]')) {
        applySerialQuantityLock(e.target);
    }
});
document.addEventListener("shown.bs.modal", function (e) {
    e.target
        .querySelectorAll('input[name="item_serialno"]')
        .forEach(applySerialQuantityLock);
});

