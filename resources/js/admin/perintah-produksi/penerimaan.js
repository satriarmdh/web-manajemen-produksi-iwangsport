// Penerimaan Hasil Produksi - Modal & AJAX Logic
(function () {
    'use strict';

    // Modal Elements
    const inputModal = document.getElementById('inputPenerimaanModal');
    const photoPreviewModal = document.getElementById('photoPreviewModal');

    // Form Elements
    const formInput = document.getElementById('formInputPenerimaan');
    const inputDetailId = document.getElementById('input_detail_id');
    const hiddenJenisInput = document.getElementById('penerimaan_jenis_value');
    const inputJenisText = document.getElementById('penerimaan_jenis_input');
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

    // Initialize custom dropdown for jenis_penerimaan
    if (typeof initCustomDropdown === 'function') {
        initCustomDropdown('penerimaan_jenis');
    }

    function setJenisPenerimaanSelection(val) {
        if (!hiddenJenisInput) return;
        hiddenJenisInput.value = val;
        const dropdown = document.getElementById('penerimaan_jenis_dropdown');
        if (dropdown) {
            const selectedOpt = dropdown.querySelector(`.dropdown-option[data-value="${val}"]`);
            if (selectedOpt && inputJenisText) {
                inputJenisText.value = selectedOpt.getAttribute('data-text');
            }
            dropdown.querySelectorAll('.dropdown-option').forEach(opt => {
                const check = opt.querySelector('.check-icon');
                if (opt.dataset.value === val) {
                    opt.classList.add('bg-[#0F034D]/5');
                    if (check) check.classList.remove('hidden');
                } else {
                    opt.classList.remove('bg-[#0F034D]/5');
                    if (check) check.classList.add('hidden');
                }
            });
        }
        applyJenisPenerimaanUI(val);
    }

    // ========== DYNAMIC UI BASE ON JENIS PENERIMAAN ==========
    function applyJenisPenerimaanUI(jenisPenerimaan) {
        const karyawanLabel = document.getElementById('karyawan_label');
        if (karyawanLabel) {
            karyawanLabel.innerHTML = jenisPenerimaan === 'cacat'
                ? 'Pilih Karyawan (Semua Peran) <span class="text-red-500">*</span>'
                : 'Pilih Karyawan Finishing <span class="text-red-500">*</span>';
        }

        if (searchInput) {
            searchInput.placeholder = jenisPenerimaan === 'cacat'
                ? 'Cari karyawan yang memiliki barang cacat...'
                : 'Cari karyawan finishing...';
        }

        const labelReady = document.getElementById('stat_label_ready');
        const labelDiserahkan = document.getElementById('stat_label_diserahkan');
        if (labelReady) {
            labelReady.textContent = jenisPenerimaan === 'cacat' ? 'Barang Cacat Ready' : 'Barang Ready';
        }
        if (labelDiserahkan) {
            labelDiserahkan.textContent = jenisPenerimaan === 'cacat' ? 'Cacat Diserahkan' : 'Sudah Diserahkan';
        }

        const helpText = document.getElementById('jenis_penerimaan_help');
        if (helpText) {
            helpText.textContent = jenisPenerimaan === 'cacat'
                ? 'Menampilkan seluruh karyawan yang memiliki stok barang cacat'
                : 'Hanya menampilkan karyawan finishing yang memiliki stok ready baik';
        }
    }

    // Handle select dropdown change inside modal
    if (hiddenJenisInput) {
        hiddenJenisInput.addEventListener('change', async function () {
            const jenisPenerimaan = this.value;
            setJenisPenerimaanSelection(jenisPenerimaan);

            // Reset selected karyawan & stat card
            searchInput.value = '';
            hiddenInput.value = '';
            searchInput.classList.remove('text-gray-900', 'font-medium');
            searchInput.classList.add('text-gray-500');
            karyawanDropdown.classList.add('hidden');
            document.getElementById('karyawan_stat_card').classList.add('hidden');

            const detailId = inputDetailId.value;
            if (detailId) {
                await loadAvailableKaryawan(detailId, jenisPenerimaan);
            }
        });
    }

    // ========== OPEN INPUT MODAL ==========
    document.querySelectorAll('[data-open-penerimaan-modal]').forEach(btn => {
        btn.addEventListener('click', async function () {
            const detailId = this.dataset.detailId;
            const produkNama = this.dataset.produkNama;
            const estimasi = this.dataset.estimasi;
            const diterima = this.dataset.diterima;
            const sisa = this.dataset.sisa;
            const jenisPenerimaan = this.dataset.jenisPenerimaan || 'baik';

            // Set form data
            inputDetailId.value = detailId;
            setJenisPenerimaanSelection(jenisPenerimaan);

            if (inputProdukNama) inputProdukNama.textContent = produkNama;
            if (inputEstimasi) inputEstimasi.textContent = estimasi;
            if (inputDiterima) inputDiterima.textContent = diterima;
            if (inputSisa) inputSisa.textContent = sisa;

            // Reset form except detailId
            const tempDetailId = inputDetailId.value;
            formInput.reset();
            inputDetailId.value = tempDetailId;
            setJenisPenerimaanSelection(jenisPenerimaan);

            // Reset custom dropdown
            searchInput.value = '';
            hiddenInput.value = '';
            searchInput.classList.remove('text-gray-900', 'font-medium');
            searchInput.classList.add('text-gray-500');
            karyawanDropdown.classList.add('hidden');
            karyawanDropdown.innerHTML = '<p class="px-4 py-2 text-xs text-gray-400">Memuat...</p>';
            previewContainer.classList.add('hidden');
            uploadPlaceholder.classList.remove('hidden');
            document.getElementById('karyawan_stat_card').classList.add('hidden');

            // Show modal with animation
            showModal(inputModal);

            // Load available karyawan
            await loadAvailableKaryawan(detailId, jenisPenerimaan);
        });
    });

    // ========== CLOSE INPUT MODAL ==========
    document.querySelectorAll('[data-close-penerimaan-modal]').forEach(btn => {
        btn.addEventListener('click', () => {
            hideModal(inputModal);
        });
    });

    // ========== LOAD AVAILABLE KARYAWAN VIA AJAX ==========
    async function loadAvailableKaryawan(detailId, type = 'baik') {
        try {
            const response = await fetch(`/admin/penerimaan-hasil-produksi/${detailId}/available-karyawan?type=${type}`);
            const data = await response.json();

            karyawanDropdown.innerHTML = '';

            if (data.karyawan.length === 0) {
                karyawanDropdown.innerHTML = type === 'cacat'
                    ? '<p class="px-4 py-2 text-xs text-gray-400">Tidak ada karyawan dengan barang cacat</p>'
                    : '<p class="px-4 py-2 text-xs text-gray-400">Tidak ada karyawan dengan stok ready</p>';
                searchInput.disabled = true;
            } else {
                data.karyawan.forEach(k => {
                    const opt = document.createElement('div');
                    opt.className = 'dropdown-option flex items-center justify-between px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors text-sm';
                    opt.dataset.value = k.karyawan_id;

                    const labelText = type === 'cacat'
                        ? `${k.karyawan_name} (Cacat: ${k.qty_ready} pcs)`
                        : `${k.karyawan_name} (Ready: ${k.qty_ready} pcs)`;

                    opt.dataset.text = labelText;
                    opt.dataset.qtyReady = k.qty_ready;
                    opt.dataset.qtyTotal = k.qty_total;
                    opt.dataset.qtyDiserahkan = k.qty_diserahkan || 0;
                    opt.dataset.karyawanName = k.karyawan_name;
                    opt.innerHTML = `<span class="text-sm font-medium text-gray-700">${labelText}</span><svg class="check-icon w-4 h-4 text-[#0F034D] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`;
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
    hiddenInput.addEventListener('change', function () {
        const statCard = document.getElementById('karyawan_stat_card');

        if (this.value) {
            const selectedOpt = karyawanDropdown.querySelector(`.dropdown-option[data-value="${this.value}"]`);
            if (!selectedOpt) return;
            const qtyReady = parseInt(selectedOpt.dataset.qtyReady || 0); // Sisa
            const qtyTotal = parseInt(selectedOpt.dataset.qtyTotal || 0); // Total
            const qtyDiserahkan = parseInt(selectedOpt.dataset.qtyDiserahkan || 0); // Diserahkan
            const karyawanName = selectedOpt.dataset.karyawanName || '-';

            // Update card values
            document.getElementById('stat_karyawan_nama').textContent = karyawanName;
            document.getElementById('stat_qty_ready').textContent = qtyReady.toLocaleString('id-ID');
            document.getElementById('stat_qty_diserahkan').textContent = qtyDiserahkan.toLocaleString('id-ID');

            // Show card
            statCard.classList.remove('hidden');
        } else {
            // Hide card when no karyawan selected
            statCard.classList.add('hidden');
        }
    });

    // ========== QTY VALIDATION AGAINST KARYAWAN READY STOCK ==========
    inputQty.addEventListener('input', function () {
        const selectedOpt = karyawanDropdown.querySelector(`.dropdown-option[data-value="${hiddenInput.value}"]`);
        if (selectedOpt && selectedOpt.dataset.qtyReady) {
            const qtyReady = parseInt(selectedOpt.dataset.qtyReady);
            const qtyInput = parseInt(this.value);
            const errorMsg = document.getElementById('error_qty');

            if (qtyInput > qtyReady) {
                errorMsg.classList.remove('hidden');
                this.setCustomValidity('Qty melebihi stok yang tersedia');
            } else {
                errorMsg.classList.add('hidden');
                this.setCustomValidity('');
            }
        }
    });

    // ========== PHOTO UPLOAD PREVIEW ==========
    inputBuktiPhoto.addEventListener('change', function (e) {
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
            reader.onload = function (e) {
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
        showPhotoModal(photoPreviewModal);
    }

    document.querySelectorAll('[data-close-photo-preview]').forEach(btn => {
        btn.addEventListener('click', () => {
            hidePhotoModal(photoPreviewModal);
        });
    });


    // ========== MODAL HELPER FUNCTIONS (SLIDE & FADE ANIMATION) ==========
    function showModal(modal) {
        if (!modal) return;
        modal.classList.remove('hidden');
        // Force reflow for smooth enter animation
        void modal.offsetWidth;
        modal.classList.add('is-open');
        document.body.classList.add('overflow-hidden');
    }

    function hideModal(modal) {
        if (!modal) return;
        modal.classList.remove('is-open');
        document.body.classList.remove('overflow-hidden');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 350);
    }

    function showPhotoModal(modal) {
        if (!modal) return;
        modal.classList.remove('hidden');
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        document.body.classList.add('overflow-hidden');
    }

    function hidePhotoModal(modal) {
        if (!modal) return;
        modal.classList.add('opacity-0');
        document.body.classList.remove('overflow-hidden');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    // Click outside to close - for slide panel
    inputModal.addEventListener('click', function (e) {
        if (e.target.classList.contains('slide-panel-backdrop')) {
            hideModal(this);
        }
    });

    // For photo preview
    photoPreviewModal.addEventListener('click', function (e) {
        if (e.target === this) {
            hidePhotoModal(this);
        }
    });
})();
