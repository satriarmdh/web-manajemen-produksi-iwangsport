// Penerimaan Hasil Produksi - Modal & AJAX Logic
(function() {
    'use strict';

    // Modal Elements
    const inputModal = document.getElementById('inputPenerimaanModal');
    const inputModalContent = document.getElementById('inputPenerimaanModalContent');
    const photoPreviewModal = document.getElementById('photoPreviewModal');

    // Form Elements
    const formInput = document.getElementById('formInputPenerimaan');
    const inputDetailId = document.getElementById('input_detail_id');
    const inputProdukNama = document.getElementById('input_produk_nama');
    const inputEstimasi = document.getElementById('input_estimasi');
    const inputDiterima = document.getElementById('input_diterima');
    const inputSisa = document.getElementById('input_sisa');
    const inputQty = document.getElementById('input_qty_diterima');
    const inputBuktiPhoto = document.getElementById('input_bukti_foto');
    const previewContainer = document.getElementById('preview_container');
    const previewImage = document.getElementById('preview_image');
    const uploadPlaceholder = document.getElementById('upload_placeholder');

    // Custom dropdown elements (prefix: penerimaan_karyawan)
    const searchInput = document.getElementById('penerimaan_karyawan_input');
    const hiddenInput = document.getElementById('penerimaan_karyawan_value');
    const karyawanDropdown = document.getElementById('penerimaan_karyawan_dropdown');
    const karyawanNoResults = document.getElementById('penerimaan_karyawan_no_results');
    const karyawanArrow = document.querySelector('#penerimaan_karyawan_wrapper .dropdown-arrow');

    // ========== OPEN INPUT MODAL ==========
    document.querySelectorAll('[data-open-penerimaan-modal]').forEach(btn => {
        btn.addEventListener('click', async function() {
            const detailId = this.dataset.detailId;
            const produkNama = this.dataset.produkNama;
            const estimasi = this.dataset.estimasi;
            const diterima = this.dataset.diterima;
            const sisa = this.dataset.sisa;

            // Set form data
            inputDetailId.value = detailId;
            inputProdukNama.textContent = produkNama;
            inputEstimasi.textContent = estimasi;
            inputDiterima.textContent = diterima;
            inputSisa.textContent = sisa;

            // Reset form
            formInput.reset();
            inputDetailId.value = detailId;
            // Reset custom dropdown
            searchInput.value = '';
            hiddenInput.value = '';
            searchInput.classList.remove('text-gray-900', 'font-medium');
            searchInput.classList.add('text-gray-500');
            karyawanDropdown.classList.add('hidden');
            karyawanDropdown.innerHTML = '<p class="px-4 py-2 text-xs text-gray-400">Memuat...</p>';
            previewContainer.classList.add('hidden');
            uploadPlaceholder.classList.remove('hidden');

            // Show modal
            showModal(inputModal, inputModalContent);

            // Load available karyawan
            await loadAvailableKaryawan(detailId);
        });
    });

    // ========== CLOSE INPUT MODAL ==========
    document.querySelectorAll('[data-close-penerimaan-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            hideModal(inputModal, inputModalContent);
        });
    });

    // ========== LOAD AVAILABLE KARYAWAN VIA AJAX ==========
    async function loadAvailableKaryawan(detailId) {
        try {
            const response = await fetch(`/admin/penerimaan-hasil-produksi/${detailId}/available-karyawan`);
            const data = await response.json();
            
            karyawanDropdown.innerHTML = '';
            
            if (data.karyawan.length === 0) {
                karyawanDropdown.innerHTML = '<p class="px-4 py-2 text-xs text-gray-400">Tidak ada karyawan dengan stok ready</p>';
                searchInput.disabled = true;
            } else {
                data.karyawan.forEach(k => {
                    const opt = document.createElement('div');
                    opt.className = 'dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm';
                    opt.dataset.value = k.karyawan_id;
                    opt.dataset.text = `${k.karyawan_name} (Ready: ${k.qty_ready} pcs)`;
                    opt.dataset.qtyReady = k.qty_ready;
                    opt.dataset.qtyDiserahkan = k.qty_diserahkan || 0;
                    opt.dataset.karyawanName = k.karyawan_name;
                    opt.innerHTML = `<span class="text-sm font-medium text-gray-700">${k.karyawan_name} (Ready: ${k.qty_ready} pcs)</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`;
                    karyawanDropdown.appendChild(opt);
                });
                searchInput.disabled = false;

                // Init custom dropdown behavior
                initCustomDropdown('penerimaan_karyawan');
            }
        } catch (error) {
            console.error('Error loading karyawan:', error);
            karyawanDropdown.innerHTML = '<p class="px-4 py-2 text-xs text-red-400">Error loading karyawan</p>';
        }
    }

    // ========== SHOW KARYAWAN STAT CARD ON SELECTION ==========
    hiddenInput.addEventListener('change', function() {
        const statCard = document.getElementById('karyawan_stat_card');
        
        if (this.value) {
            const selectedOpt = karyawanDropdown.querySelector(`.dropdown-option[data-value="${this.value}"]`);
            if (!selectedOpt) return;
            const qtyReady = parseInt(selectedOpt.dataset.qtyReady || 0);
            const qtyDiserahkan = parseInt(selectedOpt.dataset.qtyDiserahkan || 0);
            const karyawanName = selectedOpt.dataset.karyawanName || '-';
            const qtySisa = qtyReady - qtyDiserahkan;
            
            // Update card values
            document.getElementById('stat_karyawan_nama').textContent = karyawanName;
            document.getElementById('stat_qty_ready').textContent = qtyReady.toLocaleString('id-ID');
            document.getElementById('stat_qty_diserahkan').textContent = qtyDiserahkan.toLocaleString('id-ID');
            document.getElementById('stat_qty_sisa').textContent = qtySisa.toLocaleString('id-ID');
            
            // Show card
            statCard.classList.remove('hidden');
        } else {
            // Hide card when no karyawan selected
            statCard.classList.add('hidden');
        }
    });

    // ========== QTY VALIDATION AGAINST KARYAWAN READY STOCK ==========
    inputQty.addEventListener('input', function() {
        const selectedOpt = karyawanDropdown.querySelector(`.dropdown-option[data-value="${hiddenInput.value}"]`);
        if (selectedOpt && selectedOpt.dataset.qtyReady) {
            const qtyReady = parseInt(selectedOpt.dataset.qtyReady);
            const qtyInput = parseInt(this.value);
            const errorMsg = document.getElementById('error_qty');
            
            if (qtyInput > qtyReady) {
                errorMsg.classList.remove('hidden');
                this.setCustomValidity('Qty melebihi stok ready');
            } else {
                errorMsg.classList.add('hidden');
                this.setCustomValidity('');
            }
        }
    });

    // ========== PHOTO UPLOAD PREVIEW ==========
    inputBuktiPhoto.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate size
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2MB');
                this.value = '';
                return;
            }

            // Validate type
            if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
                alert('Format file harus JPG, JPEG, atau PNG');
                this.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.classList.remove('hidden');
                uploadPlaceholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // ========== PHOTO PREVIEW MODAL ==========
    function showPhotoPreview(photoUrl) {
        const img = document.getElementById('photo_preview_image');
        img.src = photoUrl;
        showModal(photoPreviewModal);
    }

    document.querySelectorAll('[data-close-photo-preview]').forEach(btn => {
        btn.addEventListener('click', () => {
            hideModal(photoPreviewModal);
        });
    });


    // ========== MODAL HELPER FUNCTIONS ==========
    function showModal(modal, content = null) {
        modal.classList.remove('hidden');
        modal.classList.add('is-open');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            if (content) {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }
        }, 10);
    }

    // Modal close/hide helper
    function hideModal(modal, content = null) {
        modal.classList.add('opacity-0');
        if (content) {
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
        }
        modal.classList.remove('is-open');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Click outside to close - for slide panel
    inputModal.addEventListener('click', function(e) {
        if (e.target.classList.contains('slide-panel-backdrop')) {
            hideModal(this);
        }
    });
    
    // For photo preview
    photoPreviewModal.addEventListener('click', function(e) {
        if (e.target === this) {
            hideModal(this);
        }
    });
})();
