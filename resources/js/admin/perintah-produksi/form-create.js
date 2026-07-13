// Data baseline dari server
        const baselines = JSON.parse(document.getElementById('perintah-produksi-baselines').textContent);

        // Data produk dan bahan baku untuk lookup nama
        const produksData = JSON.parse(document.getElementById('perintah-produksi-produks').textContent);
        const bahanBakusData = JSON.parse(document.getElementById('perintah-produksi-bahan-bakus').textContent);

        // Array untuk menyimpan detail yang ditambahkan
        let details = [];

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

            // Validasi
            if (!produkId || !bahanId || qty < 1) {
                alert('Mohon lengkapi semua field dan pastikan jumlah roll minimal 1');
                return;
            }

            // Cari baseline
            const baseline = baselines.find(b => b.produk_id == produkId && b.bahan_baku_id == bahanId);
            if (!baseline) {
                alert('Kombinasi produk dan bahan baku tidak memiliki standar baseline. Silakan hubungi administrator.');
                return;
            }

            // Cek duplikat
            const exists = details.some(d => d.produk_id == produkId && d.bahan_baku_id == bahanId);
            if (exists) {
                alert('Kombinasi produk dan bahan baku ini sudah ditambahkan. Silakan edit atau hapus yang sudah ada.');
                return;
            }

            // Cari nama produk dan bahan baku
            const produk = produksData.find(p => p.id == produkId);
            const bahan = bahanBakusData.find(b => b.id == bahanId);

            // Hitung estimasi
            const estimasi = qty * baseline.pcs_per_roll;

            // Tambah ke array
            details.push({
                produk_id: produkId,
                bahan_baku_id: bahanId,
                qty_roll_pakai: qty,
                estimasi_pcs: estimasi,
                produk_nama: produk.nama_produk,
                bahan_nama: bahan.nama_bahan
            });

            // Render ulang tabel
            renderTable();

            // Reset form input
            resetSearchableDropdown('input-produk');
            resetSearchableDropdown('input-bahan');
            document.getElementById('input-qty').value = '';
            document.getElementById('input-estimasi').value = '-';
            document.getElementById('baseline-alert').classList.add('hidden');
            document.getElementById('btn-tambah-detail').disabled = true;
        }

        // Hapus detail dari tabel
        function removeDetailRow(index) {
            if (confirm('Hapus detail ini?')) {
                details.splice(index, 1);
                renderTable();
            }
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
                        <button type="button" onclick="removeDetailRow(${index})"
                            class="text-red-600 hover:text-red-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
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
document.querySelector('[data-add-detail]')?.addEventListener('click', addDetailRow);
