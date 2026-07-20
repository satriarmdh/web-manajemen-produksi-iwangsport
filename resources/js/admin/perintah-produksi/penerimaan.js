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
    const inputKaryawanSelect = document.getElementById('input_dari_karyawan');
    const inputQty = document.getElementById('input_qty_diterima');
    const inputBuktiPhoto = document.getElementById('input_bukti_foto');
    const previewContainer = document.getElementById('preview_container');
    const previewImage = document.getElementById('preview_image');
    const uploadPlaceholder = document.getElementById('upload_placeholder');

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
            inputKaryawanSelect.innerHTML = '<option value="">Memuat...</option>';
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
            
            inputKaryawanSelect.innerHTML = '<option value="">-- Pilih Karyawan --</option>';
            
            if (data.karyawan.length === 0) {
                inputKaryawanSelect.innerHTML = '<option value="">Tidak ada karyawan dengan stok ready</option>';
                inputKaryawanSelect.disabled = true;
            } else {
                data.karyawan.forEach(k => {
                    const option = document.createElement('option');
                    option.value = k.karyawan_id;
                    option.textContent = `${k.karyawan_name} (Ready: ${k.qty_ready} pcs)`;
                    option.dataset.qtyReady = k.qty_ready;
                    option.dataset.qtyDiserahkan = k.qty_diserahkan || 0;
                    option.dataset.karyawanName = k.karyawan_name;
                    inputKaryawanSelect.appendChild(option);
                });
                inputKaryawanSelect.disabled = false;
            }
        } catch (error) {
            console.error('Error loading karyawan:', error);
            inputKaryawanSelect.innerHTML = '<option value="">Error loading karyawan</option>';
        }
    }

    // ========== SHOW KARYAWAN STAT CARD ON SELECTION ==========
    inputKaryawanSelect.addEventListener('change', function() {
        const statCard = document.getElementById('karyawan_stat_card');
        
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            const qtyReady = parseInt(selectedOption.dataset.qtyReady || 0);
            const qtyDiserahkan = parseInt(selectedOption.dataset.qtyDiserahkan || 0);
            const karyawanName = selectedOption.dataset.karyawanName || '-';
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
        const selectedOption = inputKaryawanSelect.options[inputKaryawanSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.qtyReady) {
            const qtyReady = parseInt(selectedOption.dataset.qtyReady);
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
