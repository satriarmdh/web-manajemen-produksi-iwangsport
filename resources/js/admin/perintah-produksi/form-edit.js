// Data baseline dari server
        const baselines = JSON.parse(document.getElementById('perintah-produksi-baselines').textContent);

        // Data produk dan bahan baku untuk lookup nama
        const produksData = JSON.parse(document.getElementById('perintah-produksi-produks').textContent);
        const bahanBakusData = JSON.parse(document.getElementById('perintah-produksi-bahan-bakus').textContent);

        // Data detail lama dari server
        const initialDetails = JSON.parse(document.getElementById('perintah-produksi-initial-details').textContent);

        // Array untuk menyimpan detail yang ditambahkan
        let details = [...initialDetails];
        let editingIndex = null;

        function resetDetailForm() {
            editingIndex = null;
            resetSearchableDropdown('input-produk');
            resetSearchableDropdown('input-bahan');
            document.getElementById('input-qty').value = '';
            document.getElementById('input-estimasi').value = '-';
            document.getElementById('baseline-alert').classList.add('hidden');
            document.getElementById('btn-tambah-detail').disabled = true;
            document.getElementById('btn-tambah-detail').textContent = 'Tambah';
        }

        function editDetailRow(index) {
            const detail = details[index];
            editingIndex = index;
            document.getElementById('input-produk').value = detail.produk_id;
            document.getElementById('input-bahan').value = detail.bahan_baku_id;
            document.getElementById('input-qty').value = detail.qty_roll_pakai;

            const produkOpt = document.querySelector('#input-produk-dropdown .dropdown-option[data-value="' + detail.produk_id + '"]');
            if (produkOpt) {
                document.getElementById('input-produk-search').value = produkOpt.dataset.text;
                document.getElementById('input-produk-search').classList.add('font-medium', 'text-gray-900');
                document.querySelectorAll('#input-produk-dropdown .dropdown-option').forEach(o => {
                    o.classList.remove('bg-gray-100');
                    o.querySelector('.check-icon')?.classList.add('hidden');
                });
                produkOpt.classList.add('bg-gray-100');
                produkOpt.querySelector('.check-icon')?.classList.remove('hidden');
            }

            const bahanOpt = document.querySelector('#input-bahan-dropdown .dropdown-option[data-value="' + detail.bahan_baku_id + '"]');
            if (bahanOpt) {
                document.getElementById('input-bahan-search').value = bahanOpt.dataset.text;
                document.getElementById('input-bahan-search').classList.add('font-medium', 'text-gray-900');
                document.querySelectorAll('#input-bahan-dropdown .dropdown-option').forEach(o => {
                    o.classList.remove('bg-gray-100');
                    o.querySelector('.check-icon')?.classList.add('hidden');
                });
                bahanOpt.classList.add('bg-gray-100');
                bahanOpt.querySelector('.check-icon')?.classList.remove('hidden');
            }

            calculateEstimasi();
            document.getElementById('btn-tambah-detail').textContent = 'Simpan';
            document.getElementById('input-qty').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function removeDetailRow(index) {
            details.splice(index, 1);
            if (editingIndex === index) resetDetailForm();
            renderTable();
        }
        const tglMulaiInput = document.getElementById('tgl_mulai');
        const tglSelesaiInput = document.getElementById('tgl_selesai');

        function syncTanggalSelesaiMin() {
            tglSelesaiInput.min = tglMulaiInput.value;

            if (tglSelesaiInput.value && tglSelesaiInput.value < tglMulaiInput.value) {
                tglSelesaiInput.value = '';
            }
        }

        tglMulaiInput.addEventListener('change', syncTanggalSelesaiMin);
        syncTanggalSelesaiMin();

        // Hitung estimasi saat input berubah
        function calculateEstimasi() {
            const produkId = document.getElementById('input-produk').value;
            const bahanId = document.getElementById('input-bahan').value;
            const qty = parseInt(document.getElementById('input-qty').value) || 0;
            const estimasiInput = document.getElementById('input-estimasi');
            const baselineAlert = document.getElementById('baseline-alert');
            const tambahButton = document.getElementById('btn-tambah-detail');

            baselineAlert.classList.add('hidden');
            tambahButton.disabled = true;

            if (!produkId || !bahanId) {
                estimasiInput.value = '-';
                return;
            }

            // Cari baseline yang sesuai
            const baseline = baselines.find(b => b.produk_id == produkId && b.bahan_baku_id == bahanId);

            if (!baseline) {
                estimasiInput.value = 'Baseline belum tersedia';
                baselineAlert.classList.remove('hidden');
                return;
            }

            if (qty < 1) {
                estimasiInput.value = '-';
                return;
            }

            const estimasi = qty * baseline.pcs_per_roll;
            estimasiInput.value = estimasi + ' pcs';
            tambahButton.disabled = false;
        }

        // Event listeners untuk kalkulasi otomatis
        document.getElementById('input-produk').addEventListener('change', calculateEstimasi);
        document.getElementById('input-bahan').addEventListener('change', calculateEstimasi);
        document.getElementById('input-qty').addEventListener('input', calculateEstimasi);

        function initSearchableDropdown(prefix) {
            const hiddenInput = document.getElementById(prefix);
            const searchInput = document.getElementById(`${prefix}-search`);
            const dropdown = document.getElementById(`${prefix}-dropdown`);
            const noResults = document.getElementById(`${prefix}-no-results`);
            const arrow = searchInput.parentElement.querySelector('.dropdown-arrow');
            const options = dropdown.querySelectorAll('.dropdown-option');

            function filterOptions() {
                const term = searchInput.value.toLowerCase();
                let visibleCount = 0;

                options.forEach((option) => {
                    const text = option.dataset.text.toLowerCase();
                    const isVisible = text.includes(term);
                    option.style.display = isVisible ? 'flex' : 'none';
                    if (isVisible) visibleCount++;
                });

                if (noResults) {
                    noResults.classList.toggle('hidden', visibleCount > 0);
                }
            }

            function openDropdown() {
                dropdown.classList.remove('hidden');
                arrow?.classList.add('rotate-180');
                searchInput.value = '';
                filterOptions();
            }

            function closeDropdown() {
                dropdown.classList.add('hidden');
                arrow?.classList.remove('rotate-180');

                if (!hiddenInput.value) {
                    searchInput.value = '';
                } else {
                    const selected = dropdown.querySelector(`.dropdown-option[data-value="${hiddenInput.value}"]`);
                    searchInput.value = selected ? selected.dataset.text : '';
                }
            }

            function selectOption(option) {
                options.forEach((item) => {
                    item.classList.remove('bg-gray-100');
                    item.querySelector('.check-icon')?.classList.add('hidden');
                });

                option.classList.add('bg-gray-100');
                option.querySelector('.check-icon')?.classList.remove('hidden');

                hiddenInput.value = option.dataset.value;
                searchInput.value = option.dataset.text;
                searchInput.classList.add('font-medium', 'text-gray-900');
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                closeDropdown();
            }

            searchInput.addEventListener('focus', openDropdown);
            searchInput.addEventListener('input', filterOptions);
            options.forEach((option) => option.addEventListener('click', () => selectOption(option)));

            document.addEventListener('click', (event) => {
                if (!searchInput.parentElement.contains(event.target)) {
                    closeDropdown();
                }
            });
        }

        function resetSearchableDropdown(prefix) {
            const hiddenInput = document.getElementById(prefix);
            const searchInput = document.getElementById(`${prefix}-search`);
            const dropdown = document.getElementById(`${prefix}-dropdown`);

            hiddenInput.value = '';
            searchInput.value = '';
            searchInput.classList.remove('font-medium', 'text-gray-900');
            dropdown.querySelectorAll('.dropdown-option').forEach((item) => {
                item.classList.remove('bg-gray-100');
                item.querySelector('.check-icon')?.classList.add('hidden');
                item.style.display = 'flex';
            });
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        }

        initSearchableDropdown('input-produk');
        initSearchableDropdown('input-bahan');

        // Tambah detail ke tabel
        function addDetailRow() {
            const produkId = document.getElementById('input-produk').value;
            const bahanId = document.getElementById('input-bahan').value;
            const qty = parseInt(document.getElementById('input-qty').value) || 0;

            if (!produkId || !bahanId || qty < 1) {
                alert('Mohon lengkapi semua field dan pastikan jumlah roll minimal 1');
                return;
            }

            const baseline = baselines.find(b => b.produk_id == produkId && b.bahan_baku_id == bahanId);
            if (!baseline) {
                alert('Kombinasi produk dan bahan baku tidak memiliki standar baseline. Silakan hubungi administrator.');
                return;
            }

            const exists = details.some((d, idx) => idx !== editingIndex && d.produk_id == produkId && d.bahan_baku_id == bahanId);
            if (exists) {
                alert('Kombinasi produk dan bahan baku ini sudah ditambahkan. Silakan edit atau hapus yang sudah ada.');
                return;
            }

            const produk = produksData.find(p => p.id == produkId);
            const bahan = bahanBakusData.find(b => b.id == bahanId);
            const estimasi = qty * baseline.pcs_per_roll;

            const newItem = {
                produk_id: produkId,
                bahan_baku_id: bahanId,
                qty_roll_pakai: qty,
                estimasi_pcs: estimasi,
                produk_nama: produk.nama_produk,
                bahan_nama: bahan.nama_bahan
            };

            if (editingIndex === null) {
                details.push(newItem);
            } else {
                details[editingIndex] = newItem;
            }

            renderTable();
            resetDetailForm();
        }

        // Render tabel detail
        function renderTable() {
            const tbody = document.getElementById('detail-table-body');
            tbody.innerHTML = '';

            if (details.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400">
                            Belum ada detail produk. Silakan tambahkan di form atas.
                        </td>
                    </tr>
                `;
                document.getElementById('total-estimasi').textContent = '0 pcs';
                return;
            }

            let totalEstimasi = 0;

            details.forEach((detail, index) => {
                totalEstimasi += detail.estimasi_pcs;

                const row = document.createElement('tr');
                row.className = 'border-b border-gray-100 hover:bg-gray-50 transition-colors';
                row.innerHTML = `
                    <td class="py-3 px-4 text-gray-600">${index + 1}</td>
                    <td class="py-3 px-4 font-medium text-[#0F034D]">${detail.produk_nama}</td>
                    <td class="py-3 px-4 text-gray-600">${detail.bahan_nama}</td>
                    <td class="py-3 px-4 text-center">${detail.qty_roll_pakai}</td>
                    <td class="py-3 px-4 text-center font-semibold text-green-600">${detail.estimasi_pcs} pcs</td>
                    <td class="py-3 px-4 text-center">
                        <div class="flex justify-center gap-1">
                            <button type="button" onclick="editDetailRow(${index})"
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button type="button" onclick="removeDetailRow(${index})"
                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </td>
                    <input type="hidden" name="details[${index}][produk_id]" value="${detail.produk_id}">
                    <input type="hidden" name="details[${index}][bahan_baku_id]" value="${detail.bahan_baku_id}">
                    <input type="hidden" name="details[${index}][qty_roll_pakai]" value="${detail.qty_roll_pakai}">
                `;
                tbody.appendChild(row);
            });

            document.getElementById('total-estimasi').textContent = totalEstimasi + ' pcs';
        }

        // Render awal saat halaman dimuat
        renderTable();


window.removeDetailRow = removeDetailRow;
window.editDetailRow = editDetailRow;
document.querySelector('[data-add-detail]')?.addEventListener('click', addDetailRow);
