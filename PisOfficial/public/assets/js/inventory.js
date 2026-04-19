// Make this a global variable so the selectFilter function can access it
let currentFilter = "all";

document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("searchInput");
  const cards = document.querySelectorAll(".card-style");
  const noResults = document.getElementById("noResults");

  // This is the main function that handles filtering
  window.filterInventory = function () {
    const searchTerm = searchInput.value.toLowerCase().trim();
    let visibleCount = 0;

    cards.forEach((card) => {
      const productName = card.querySelector("h1").textContent.toLowerCase();
      const productCode = card.querySelector("h2").textContent.toLowerCase();
      const productDesc = card.querySelector("p")
        ? card.querySelector("p").textContent.toLowerCase()
        : "";

      const whEl = card.querySelector(".loc-wh");
      const srEl = card.querySelector(".loc-sr");

      const whStock = whEl ? (parseInt(whEl.textContent.replace(/[^\d]/g, "")) || 0) : 0;
      const srStock = srEl ? (parseInt(srEl.textContent.replace(/[^\d]/g, "")) || 0) : 0;

      // Search Logic
      const matchesSearch =
        productName.includes(searchTerm) ||
        productCode.includes(searchTerm) ||
        productDesc.includes(searchTerm);

      // Location Filter Logic (Using the global currentFilter)
      let matchesLocation = true;
      if (currentFilter === "warehouse") {
        matchesLocation = whStock > 0;
      } else if (currentFilter === "showroom") {
        matchesLocation = srStock > 0;
      }

      // Execution
      if (matchesSearch && matchesLocation) {
        card.style.display = "flex";
        visibleCount++;
      } else {
        card.style.display = "none";
      }
    });

    // Show/Hide No Results
    if (visibleCount === 0) {
      if (noResults) {
          noResults.classList.remove("hidden");
          noResults.classList.add("flex");
      }
    } else {
      if (noResults) {
          noResults.classList.add("hidden");
          noResults.classList.remove("flex");
      }
    }
  };

  // Listen to search input
  if (searchInput) searchInput.addEventListener("input", filterInventory);

  // Add change tracker for forms
  const forms = document.querySelectorAll('form');
  forms.forEach(form => {
      form.addEventListener("input", () => {
          window.formHasUnsavedChanges = true;
      });
      form.addEventListener("change", () => {
          window.formHasUnsavedChanges = true;
      });
  });

  // --- Centralized Event Delegation ---
  document.body.addEventListener('click', (e) => {
      // Toggle Filter Menu
      if (e.target.closest('[data-toggle-filter-menu]')) {
          e.stopPropagation();
          const menu = document.getElementById('filterMenu');
          if (menu) menu.classList.toggle('hidden');
          return;
      }

      // Select Filter
      const filterBtn = e.target.closest('[data-select-filter]');
      if (filterBtn) {
          const val = filterBtn.getAttribute('data-select-filter');
          window.selectFilter(val);
          return;
      }

      // Reset Filters
      if (e.target.closest('[data-reset-filters]')) {
          window.resetFilters();
          return;
      }

      // Open Modal
      const openModalBtn = e.target.closest('[data-open-modal]');
      if (openModalBtn) {
          const target = openModalBtn.getAttribute('data-open-modal');
          if (typeof window.openModal === 'function') window.openModal(target);
          return;
      }

      // Close Modal with Check
      const closeBtn = e.target.closest('[data-close-modal-check]');
      if (closeBtn) {
          const modalId = closeBtn.getAttribute('data-close-modal-check');
          const formId = closeBtn.getAttribute('data-form-id');
          if (typeof window.closeModalWithCheck === 'function') window.closeModalWithCheck(modalId, formId);
          return;
      }

      // Open Stock Modal
      const stockBtn = e.target.closest('[data-open-stock-modal]');
      if (stockBtn) {
          const code = stockBtn.getAttribute('data-open-stock-modal');
          if (typeof window.openStockModal === 'function') window.openStockModal(code);
          return;
      }

      // Open Edit Modal
      const editBtn = e.target.closest('[data-open-edit-modal]');
      if (editBtn) {
          const code = editBtn.getAttribute('data-open-edit-modal');
          if (typeof window.openEditModal === 'function') window.openEditModal(code);
          return;
      }

      // Open Delete Modal
      const deleteBtn = e.target.closest('[data-open-delete-modal]');
      if (deleteBtn) {
          const code = deleteBtn.getAttribute('data-open-delete-modal');
          const name = deleteBtn.getAttribute('data-product-name');
          if (typeof window.openDeleteModal === 'function') window.openDeleteModal(code, name);
          return;
      }

      // Add Component Row (New/Edit)
      if (e.target.closest('[data-add-comp-row]')) {
          window.addComponentRow();
          return;
      }
      if (e.target.closest('[data-add-edit-comp-row]')) {
          window.addEditComponentRow();
          return;
      }

      // Add Variant Row (New/Edit)
      if (e.target.closest('[data-add-variant-row]')) {
          window.addVariantRow();
          return;
      }
      if (e.target.closest('[data-add-edit-variant-row]')) {
          window.addEditVariantRow();
          return;
      }

      // Handle Edit Save
      if (e.target.closest('[data-save-edit]')) {
          window.handleEditSave();
          return;
      }
      
      // Remove Row
      const removeRowBtn = e.target.closest('[data-remove-row]');
      if (removeRowBtn) {
          const type = removeRowBtn.getAttribute('data-remove-row');
          const name = removeRowBtn.getAttribute('data-row-name') || '';
          window.confirmRemoveRow(removeRowBtn, type, name);
          return;
      }

      // Close menu when clicking outside
      if (!e.target.closest('.inline-block')) {
          const menu = document.getElementById("filterMenu");
          if (menu) menu.classList.add("hidden");
      }
  });

  document.body.addEventListener('change', (e) => {
      // Image Preview (Main)
      if (e.target.matches('[data-preview-image]')) {
          const targetId = e.target.getAttribute('data-preview-image');
          window.previewImage(e.target, targetId);
          return;
      }

      // Variant Image Preview (New)
      if (e.target.matches('[data-preview-variant-image]')) {
          const index = e.target.getAttribute('data-preview-variant-image');
          window.previewVariantImage(e.target, index);
          return;
      }

      // Variant Image Preview (Edit)
      if (e.target.matches('[data-preview-edit-variant-image]')) {
          const index = e.target.getAttribute('data-preview-edit-variant-image');
          window.previewEditVariantImage(e.target, index);
          return;
      }

      // Sale Toggle
      if (e.target.matches('[data-sale-toggle]')) {
          window.handleSaleToggle(e.target);
          return;
      }
  });
});

window.selectFilter = function(value) {
  currentFilter = value; // Update the global filter value

  // Update visual feedback
  console.log("Filtering by: " + value);

  // Call the search function
  if (typeof filterInventory === "function") {
    filterInventory();
  }

  const menu = document.getElementById("filterMenu");
  if (menu) menu.classList.add("hidden");
};

window.resetFilters = function () {
  const searchInput = document.getElementById("searchInput");
  if (searchInput) searchInput.value = "";
  currentFilter = "all";
  if (typeof filterInventory === "function") {
    filterInventory();
  }
};

// --- Add Product JS Logic ---

window.previewImage = function (input, previewId) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      const preview = document.getElementById(previewId);
      if (preview) {
        preview.src = e.target.result;
        preview.classList.remove("hidden");

        // Hide placeholder icon if present
        if (previewId === "mainImagePreview") {
          const icon = document.getElementById("placeholderIcon");
          if (icon) icon.classList.add("hidden");
        }
      }
    };
    reader.readAsDataURL(input.files[0]);
  }
};

window.previewVariantImage = function (input, index) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      const preview = document.getElementById("v-prev-" + index);
      const svg = document.getElementById("v-svg-" + index);
      if (preview) {
        preview.src = e.target.result;
        preview.classList.remove("hidden");
      }
      if (svg) {
        svg.classList.add("hidden");
      }
    };
    reader.readAsDataURL(input.files[0]);
  }
};

// --- Confirmation utility for row removal ---
window.confirmRemoveRow = function(btn, type, name = '') {
    const message = name 
        ? `Are you sure you want to remove the ${type} "${name}"?` 
        : `Are you sure you want to remove this ${type}?`;
    
    if (typeof window.showCustomConfirm === "function") {
        const title = type === 'variant' ? 'Remove Variant' : 'Remove Component';
        window.showCustomConfirm(message, () => {
            const row = btn.closest('.group') || btn.parentElement;
            if (row) row.remove();
            window.formHasUnsavedChanges = true;
        }, "Remove", "bg-red-500", title);
    } else {
        if (confirm(message)) {
            const row = btn.closest('.group') || btn.parentElement;
            if (row) row.remove();
            window.formHasUnsavedChanges = true;
        }
    }
};

window.addComponentRow = function (containerId = "componentsContainer") {
  const container = document.getElementById(containerId);
  if (!container) return;

  window.formHasUnsavedChanges = true;

  const html = `
        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl group border border-transparent hover:border-gray-200 transition-all">
            <div class="flex-1">
                <input type="text" name="comp_names[]" list="compSuggestion" placeholder="Search or type part..." class="w-full bg-transparent font-bold text-gray-800 outline-none text-sm" required>
                <div class="flex items-center gap-2 mt-2 border border-gray-200 rounded-lg px-3 py-1 bg-white w-full">
                    <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest shrink-0">LOC:</span>
                    <input type="text" name="comp_locs[]" placeholder="Aisle 0-0" class="w-full bg-transparent outline-none text-[11px] font-bold uppercase text-gray-700" value="" required>
                </div>
            </div>
            <div class="flex items-center gap-2 px-2 border-l border-gray-200">
                <span class="text-[10px] font-bold text-gray-400 uppercase">Qty</span>
                <input type="number" name="comp_qtys[]" placeholder="0" class="w-10 bg-white border border-gray-200 rounded-lg text-center font-bold text-sm py-1 outline-none" required>
            </div>
            <button type="button" data-remove-row="component" class="text-gray-300 hover:text-red-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>
    `;
  container.insertAdjacentHTML("beforeend", html);
};

let variantCounter = 1;
window.addVariantRow = function (containerId = "variantsContainer") {
  const container = document.getElementById(containerId);
  if (!container) return;

  window.formHasUnsavedChanges = true;

  const index = variantCounter++;
  const html = `
        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-2xl group border border-transparent hover:border-gray-200 transition-all">
            <div class="w-14 h-14 bg-white rounded-xl border border-gray-200 flex items-center justify-center shrink-0 relative">
                <img id="v-prev-${index}" class="hidden object-cover w-full h-full rounded-xl">
                <svg id="v-svg-${index}" class="w-4 h-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <label class="absolute inset-0 bg-black/40 opacity-0 hover:opacity-100 flex items-center justify-center cursor-pointer rounded-xl transition-all">
                    <input type="file" name="variant_imgs[]" data-preview-variant-image="${index}" accept=".jpg,.jpeg,.png" class="hidden" required>
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round"></path>
                    </svg>
                </label>
            </div>
            <div class="flex-1">
                <p class="text-[9px] font-black text-gray-400 uppercase mb-0.5 tracking-widest">Variant Name</p>
                <input type="text" name="variant_names[]" placeholder="e.g. Matte Black" class="w-full bg-transparent font-bold text-gray-800 outline-none text-sm" required>
            </div>
            <div class="w-20 border-l border-gray-200 pl-3">
                <p class="text-[9px] font-black text-red-500 uppercase mb-0.5 tracking-widest">Low Stock</p>
                <input type="number" name="variant_low_stocks[]" placeholder="10" class="w-full bg-transparent font-bold text-red-600 outline-none text-sm" required>
            </div>
            <button type="button" data-remove-row="variant" class="text-gray-300 hover:text-red-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>
    `;
  container.insertAdjacentHTML("beforeend", html);
};

window.saveNewProduct = async function () {
  const form = document.getElementById("addProductForm");
  if (!form) return;

  if (!form.reportValidity()) {
    return;
  }

  const formData = new FormData(form);
  formData.append("action", "add_product");

  try {
    const response = await fetch("../include/inc.admin/admin.ctrl.php", {
      method: "POST",
      body: formData,
    });

    let result = await response.json();

    if (result && result.success) {
      window.formHasUnsavedChanges = false;
      if (typeof window.closeModal === "function") {
          window.closeModal("addProductModal");
      }
      
      setTimeout(() => {
          if (typeof window.showCustomSuccess === "function") {
            window.showCustomSuccess("Product successfully added!", () => {
              window.location.reload();
            });
          }
      }, 300);
    } else {
      if (typeof window.showCustomAlert === "function") {
        window.showCustomAlert("Error: " + (result.message || "Failed to add product."));
      }
    }
  } catch (error) {
    console.error("Error adding product:", error);
  }
};

// --- Delete Product JS Logic ---

let productToDeleteCode = null;

window.openDeleteModal = function (code, name) {
  productToDeleteCode = code;
  const nameEl = document.getElementById("deleteProductName");
  if (nameEl) {
    nameEl.textContent = name;
  }

  if (typeof window.openModal === "function") window.openModal("deleteModal");
};

window.closeDeleteModal = function () {
  productToDeleteCode = null;
  if (typeof window.closeModal === "function") window.closeModal("deleteModal");
};

window.confirmDeleteProduct = async function () {
  if (!productToDeleteCode) return;

  const formData = new FormData();
  formData.append("action", "delete_product");
  formData.append("code", productToDeleteCode);

  try {
    const response = await fetch("../include/inc.admin/admin.ctrl.php", {
      method: "POST",
      body: formData,
    });

    let result = await response.json();

    if (result && result.success) {
      closeDeleteModal();
      setTimeout(() => {
          if (typeof window.showCustomSuccess === "function") {
            window.showCustomSuccess("Product deleted successfully!", () => {
              window.location.reload();
            });
          }
      }, 300);
    } else {
      if (typeof window.showCustomAlert === "function") {
        window.showCustomAlert("Error: " + (result.message || "Failed to delete product."));
      }
    }
  } catch (error) {
    console.error("Error deleting product:", error);
  }
};

// --- Edit Product JS Logic ---

window.openEditModal = async function(code) {
  const formData = new FormData();
  formData.append("action", "get_product_details");
  formData.append("code", code);

  window.formHasUnsavedChanges = false; // Reset before loading

  try {
    const response = await fetch("../include/inc.admin/admin.ctrl.php", {
      method: "POST",
      body: formData,
    });
    const result = await response.json();
    if (result && result.success) {
      const p = result.product;
      
      document.getElementById('editProdId').value = p.prod_id || '';
      document.getElementById('editOldCode').value = p.code;
      const headerCode = document.getElementById('editCodeHeader');
      if (headerCode) headerCode.textContent = p.code;
      
      document.getElementById('editName').value = p.name || '';
      document.getElementById('editCode').value = p.code || '';
      document.getElementById('editPrice').value = p.price || 0;
      document.getElementById('editCategory').value = p.category || '';
      document.getElementById('editDescription').value = p.description || '';
      
      const saleToggle = document.getElementById('editSaleToggle');
      const discountInput = document.getElementById('editDiscountInput');
      if (Number(p.is_on_sale) === 1) {
          if (saleToggle) saleToggle.checked = true;
          if (discountInput) {
            discountInput.disabled = false;
            discountInput.value = p.discount || 0;
          }
      } else {
          if (saleToggle) saleToggle.checked = false;
          if (discountInput) {
            discountInput.disabled = true;
            discountInput.value = 0;
          }
      }

      const editImagePreview = document.getElementById('editImagePreview');
      if (editImagePreview) {
        editImagePreview.src = `../../public/assets/img/furnitures/${p.default_image}`;
      }
      
      const compContainer = document.getElementById('editComponentsContainer');
      if (compContainer) {
        compContainer.innerHTML = '';
        if (p.components && p.components.length > 0) {
            p.components.forEach(comp => {
                addEditComponentRow(comp.pc_id, comp.component_name, comp.location || '', comp.qty_needed);
            });
        }
      }

      const varContainer = document.getElementById('editVariantsContainer');
      if (varContainer) {
        varContainer.innerHTML = '';
        if (p.variants && p.variants.length > 0) {
            p.variants.forEach((v, index) => {
                addEditVariantRow(v.variant_id, v.variant, v.variant_image, v.min_buildable_qty, index);
            });
        }
      }

      if (typeof window.openModal === "function") window.openModal('editModal');

      // Reset immediately after population is completely injected
      setTimeout(() => { window.formHasUnsavedChanges = false; }, 50);
    } else {
        if (typeof window.showCustomAlert === "function") {
            window.showCustomAlert("Error: " + (result.message || "Failed to fetch details."));
        }
    }
  } catch (error) {
    console.error(error);
  }
};

window.handleSaleToggle = async function(checkbox) {
    toggleEditSaleDiscount(checkbox);

    const code = document.getElementById('editOldCode')?.value;
    if (!code) return; // Do nothing if product isn't fully loaded

    const newStatus = checkbox.checked ? 1 : 0;
    const formData = new FormData();
    formData.append("action", "toggle_sale");
    formData.append("code", code);
    formData.append("is_on_sale", newStatus);

    try {
        const response = await fetch("../include/inc.admin/admin.ctrl.php", {
            method: "POST",
            body: formData,
        });

        let result = await response.json();

        if (result && result.success) {
            if (typeof window.showCustomSuccess === "function") {
                window.showCustomSuccess("Sale status updated!");
            }
        } else {
            checkbox.checked = !checkbox.checked; // revert
            toggleEditSaleDiscount(checkbox);
            if (typeof window.showCustomAlert === "function") {
                window.showCustomAlert("Error: " + (result.message || "Unknown error"));
            }
        }
    } catch (e) {
        checkbox.checked = !checkbox.checked; // revert
        toggleEditSaleDiscount(checkbox);
        console.error(e);
    }
};

window.toggleEditSaleDiscount = function(checkbox) {
    const input = document.getElementById('editDiscountInput');
    if (input) {
      input.disabled = !checkbox.checked;
      if (!checkbox.checked) input.value = '0';
    }
};

window.addEditComponentRow = function(id = '', name = '', loc = '', qty = '') {
    const container = document.getElementById('editComponentsContainer');
    if(!container) return;
    
    const isExisting = name !== '';
    const disabledAttr = isExisting ? 'disabled' : '';
    const penColor = isExisting ? 'text-gray-300' : 'text-blue-500';

    if (!isExisting) window.formHasUnsavedChanges = true;

    const html = `
        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl group border border-transparent hover:border-gray-200 transition-all">
            <div class="flex-1">
                <input type="hidden" name="pc_ids[]" value="${id}">
                <input type="hidden" name="existing_comp_names[]" value="${name}">
                <input type="text" name="comp_names[]" value="${name}" list="compSuggestion" placeholder="Search or type part..." class="w-full bg-transparent font-bold text-gray-800 outline-none text-sm disabled:opacity-50" ${disabledAttr} required>
                <div class="flex items-center gap-2 mt-2 border border-gray-200 rounded-lg px-3 py-1 bg-white w-full ${isExisting ? 'opacity-50' : ''}">
                    <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest shrink-0">LOC:</span>
                    <input type="text" name="comp_locs[]" value="${loc}" placeholder="Aisle 0-0" class="w-full bg-transparent outline-none text-[11px] font-bold uppercase text-gray-700" ${disabledAttr} required>
                </div>
            </div>
            <div class="flex items-center gap-2 px-2 border-l border-gray-200">
                <span class="text-[10px] font-bold text-gray-400 uppercase">Qty</span>
                <input type="number" name="comp_qtys[]" value="${qty}" placeholder="0" class="w-10 bg-white border border-gray-200 rounded-lg text-center font-bold text-sm py-1 outline-none disabled:bg-gray-100" ${disabledAttr} required>
            </div>
            <div class="flex items-center gap-2 border-l border-gray-200 pl-2">
                <button type="button" onclick="toggleEditRow(this)" class="${penColor} hover:text-blue-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
                <button type="button" data-remove-row="component" class="text-gray-300 hover:text-red-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
};

let editVariantCounter = 100;
window.addEditVariantRow = function(id = '', name = '', image = 'default.png', lowStock = '10', overrideIndex = null) {
  const container = document.getElementById("editVariantsContainer");
  if (!container) return;

  const index = overrideIndex !== null ? overrideIndex : editVariantCounter++;
  const imgSrc = image ? `../../public/assets/img/furnitures/${image}` : '';
  const imgClass = image ? "object-cover w-full h-full rounded-xl" : "hidden object-cover w-full h-full rounded-xl";
  const svgClass = image ? "hidden w-4 h-4 text-gray-200" : "w-4 h-4 text-gray-200";

  const isExisting = name !== '';
  const disabledAttr = isExisting ? 'disabled' : '';
  const penColor = isExisting ? 'text-gray-300' : 'text-blue-500'; 

  if (!isExisting) window.formHasUnsavedChanges = true;

  const html = `
        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-2xl group border border-transparent hover:border-gray-200 transition-all">
            <input type="hidden" name="variant_ids[]" value="${id}">
            <input type="hidden" name="existing_variant_imgs[]" value="${image}">
            <div class="w-14 h-14 bg-white rounded-xl border border-gray-200 flex items-center justify-center shrink-0 relative">
                <img id="v-edit-prev-${index}" src="${imgSrc}" class="${imgClass}" onerror="this.onerror=null; this.src='../../public/assets/img/furnitures/default.png';">
                <svg id="v-edit-svg-${index}" class="${svgClass}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <label class="absolute inset-0 bg-black/40 opacity-0 hover:opacity-100 flex items-center justify-center cursor-pointer rounded-xl transition-all">
                    <input type="file" name="variant_imgs[]" data-preview-edit-variant-image="${index}" accept=".jpg,.jpeg,.png" class="hidden" ${id === '' ? 'required' : ''}>
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round"></path>
                    </svg>
                </label>
            </div>
            <div class="flex-1" >
                <p class="text-[9px] font-black text-gray-400 uppercase mb-0.5 tracking-widest">Variant Name</p>
                <input type="text" name="variant_names[]" value="${name}" placeholder="e.g. Matte Black" class="w-full bg-transparent font-bold text-gray-800 outline-none text-sm disabled:opacity-50" ${disabledAttr} required>
            </div>
            <div class="w-20 border-l border-gray-200 pl-3">
                <p class="text-[9px] font-black text-red-500 uppercase mb-0.5 tracking-widest">Low Stock</p>
                <input type="number" name="variant_low_stocks[]" value="${lowStock}" placeholder="10" class="w-full bg-transparent font-bold text-red-600 outline-none text-sm disabled:opacity-50" ${disabledAttr} required>
            </div>
            <div class="flex items-center gap-2 border-l border-gray-200 pl-2">
                <button type="button" onclick="toggleEditRow(this)" class="${penColor} hover:text-green-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
                <button type="button" data-remove-row="variant" class="text-gray-300 hover:text-red-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
};

window.previewEditVariantImage = function(input, index) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      const preview = document.getElementById("v-edit-prev-" + index);
      const svg = document.getElementById("v-edit-svg-" + index);
      if (preview) {
        preview.src = e.target.result;
        preview.classList.remove("hidden");
      }
      if (svg) {
        svg.classList.add("hidden");
      }
    };
    reader.readAsDataURL(input.files[0]);
  }
};

window.handleEditSave = function () {
  const form = document.getElementById("editProductForm");
  if (!form) return;

  const disabledElements = form.querySelectorAll(':disabled');
  disabledElements.forEach(el => el.disabled = false);

  if (!form.reportValidity()) {
      disabledElements.forEach(el => el.disabled = true);
      return;
  }

  disabledElements.forEach(el => el.disabled = true);

  if (!window.formHasUnsavedChanges) {
    if (typeof window.openModal === "function") {
      window.openModal('editConfirmModal');
    } 
  } else {
    window.executeEditSave();
  }
};

window.executeEditSave = async function () {
  const form = document.getElementById("editProductForm");
  if (!form) return;

  const disabledElements = form.querySelectorAll(':disabled');
  disabledElements.forEach(el => el.disabled = false);

  const formData = new FormData(form);
  formData.append("action", "update_product");

  disabledElements.forEach(el => el.disabled = true);

  if (typeof window.closeModal === "function") {
    window.closeModal('editConfirmModal');
  }

  try {
    const response = await fetch("../include/inc.admin/admin.ctrl.php", {
      method: "POST",
      body: formData,
    });
    let result = await response.json();
    
    if (result && result.success) {
        if (typeof window.closeModal === "function") {
            window.closeModal('editConfirmModal');
            window.closeModal('editModal');
        }

        if (typeof window.showCustomSuccess === "function") {
            window.showCustomSuccess("Product updated successfully", () => {
                window.location.reload();
            });
        }
    } else {
        if (typeof window.showCustomAlert === "function") {
            window.showCustomAlert("Error: " + (result.message || "Unknown error"));
        }
    }
  } catch (error) {
    console.error("Error updating product:", error);
  }
};
