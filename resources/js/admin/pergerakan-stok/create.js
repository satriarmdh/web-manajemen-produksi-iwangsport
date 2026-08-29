document.addEventListener('DOMContentLoaded', () => {
    const config = window.pergerakanStokConfig;
    if (!config) return;

    const oldSupplier = config.oldSupplier;
    const oldPenerima = config.oldPenerima;
    const jenisPergerakan = config.jenisPergerakan;

    // Inisialisasi dropdown kustom
    initCustomDropdown('input_bahan_baku');
    if (document.getElementById('supplier_input')) {
        initCustomDropdown('supplier');
        if (oldSupplier) {
            setCustomDropdownValue('supplier', oldSupplier);
        }
    }
    if (document.getElementById('penerima_input')) {
        initCustomDropdown('penerima');
        if (oldPenerima) {
            setCustomDropdownValue('penerima', oldPenerima);
        }
    }

    const hiddenBahanInput = document.getElementById('input_bahan_baku_value');
    const dropdownBahan = document.getElementById('input_bahan_baku_dropdown');
    const inputQty = document.getElementById('input-quantity');
    const btnTambah = document.getElementById('btn-tambah-item');
    const tableBody = document.getElementById('table-items-body');
    const trEmpty = document.getElementById('tr-empty-state');
    const infoStokBaku = document.getElementById('info_stok_baku');
    const infoQtyWarning = document.getElementById('info_qty_baku_warning');

    let items = [];
    let editingIndex = null;

    function updateStockInfo() {
        const id = hiddenBahanInput.value;
        if (!id) {
            if (infoStokBaku) { infoStokBaku.classList.add('hidden'); infoStokBaku.textContent = ''; }
            if (infoQtyWarning) { infoQtyWarning.classList.add('hidden'); infoQtyWarning.textContent = ''; }
            btnTambah.disabled = false;
            return null;
        }

        const selectedOpt = dropdownBahan.querySelector(`.dropdown-option[data-value="${id}"]`);
        if (!selectedOpt) return null;

        const stok = parseInt(selectedOpt.getAttribute('data-stok')) || 0;
        const satuan = selectedOpt.getAttribute('data-satuan') || 'pcs';
        const nama = selectedOpt.getAttribute('data-nama') || 'Bahan Baku';

        if (infoStokBaku) {
            infoStokBaku.classList.remove('hidden', 'text-emerald-600', 'text-red-500');
            if (stok > 0) {
                infoStokBaku.classList.add('text-emerald-600', 'font-semibold');
                infoStokBaku.innerHTML = `Stok Tersedia: <strong>${stok.toLocaleString('id-ID')} ${satuan}</strong>`;
            } else {
                infoStokBaku.classList.add('text-red-500', 'font-semibold');
                infoStokBaku.innerHTML = `Stok Kosong (0 ${satuan})`;
            }
        }

        const qty = parseInt(inputQty.value) || 0;
        if (jenisPergerakan === 'keluar' && qty > 0) {
            if (qty > stok) {
                if (infoQtyWarning) {
                    infoQtyWarning.classList.remove('hidden');
                    infoQtyWarning.textContent = `Jumlah keluar (${qty}) melebihi stok tersedia (${stok} ${satuan})`;
                }
                btnTambah.disabled = true;
            } else {
                if (infoQtyWarning) {
                    infoQtyWarning.classList.add('hidden');
                    infoQtyWarning.textContent = '';
                }
                btnTambah.disabled = false;
            }
        } else {
            if (infoQtyWarning) {
                infoQtyWarning.classList.add('hidden');
                infoQtyWarning.textContent = '';
            }
            btnTambah.disabled = false;
        }

        return { stok, satuan, nama };
    }

    hiddenBahanInput.addEventListener('change', updateStockInfo);
    inputQty.addEventListener('input', updateStockInfo);

    function resetItemForm() {
        editingIndex = null;
        resetCustomDropdown('input_bahan_baku');
        inputQty.value = '';
        btnTambah.textContent = 'Tambah';
        btnTambah.disabled = false;
        if (infoStokBaku) { infoStokBaku.classList.add('hidden'); infoStokBaku.textContent = ''; }
        if (infoQtyWarning) { infoQtyWarning.classList.add('hidden'); infoQtyWarning.textContent = ''; }
    }

    function editItem(index) {
        const item = items[index];
        editingIndex = index;
        setCustomDropdownValue('input_bahan_baku', String(item.id));
        inputQty.value = item.quantity;
        btnTambah.textContent = 'Simpan';
        inputQty.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function removeItem(index) {
        items.splice(index, 1);
        if (editingIndex === index) resetItemForm();
        renderTable();
    }

    function renderTable() {
        const rows = tableBody.querySelectorAll('tr:not(#tr-empty-state)');
        rows.forEach(r => r.remove());

        if (items.length === 0) {
            trEmpty.classList.remove('hidden');
            return;
        }

        trEmpty.classList.add('hidden');

        items.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50 transition-colors';
            tr.innerHTML = `
                <td class="px-4 py-3 text-gray-500 font-mono">${item.kode}</td>
                <td class="px-4 py-3 font-medium text-gray-900">${item.nama} - ${item.warna}</td>
                <td class="px-4 py-3 text-right font-semibold text-gray-900 whitespace-nowrap">
                    ${item.quantity} ${item.satuan}
                    <input type="hidden" name="items[${index}][bahan_baku_id]" value="${item.id}">
                    <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-1">
                        <button type="button" class="btn-edit-item p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer" data-index="${index}" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button type="button" class="btn-hapus-item p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors cursor-pointer" data-index="${index}" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            `;
            tableBody.appendChild(tr);
        });

        document.querySelectorAll('.btn-edit-item').forEach(btn => {
            btn.addEventListener('click', () => editItem(parseInt(btn.getAttribute('data-index'))));
        });

        document.querySelectorAll('.btn-hapus-item').forEach(btn => {
            btn.addEventListener('click', () => removeItem(parseInt(btn.getAttribute('data-index'))));
        });
    }

    function addItem() {
        const id = hiddenBahanInput.value;
        const qty = parseInt(inputQty.value) || 0;

        if (!id) {
            alert('Pilih bahan baku terlebih dahulu!');
            return;
        }

        if (qty <= 0) {
            alert('Jumlah kuantiti harus minimal 1!');
            return;
        }

        // Ambil option terpilih dari custom dropdown
        const selectedOpt = dropdownBahan.querySelector(`.dropdown-option[data-value="${id}"]`);
        if (!selectedOpt) return;

        const nama = selectedOpt.getAttribute('data-nama');
        const warna = selectedOpt.getAttribute('data-warna') || '-';
        const kode = selectedOpt.getAttribute('data-kode');
        const satuan = selectedOpt.getAttribute('data-satuan');
        const stokMax = parseInt(selectedOpt.getAttribute('data-stok')) || 0;

        // Cek duplikat (kecuali item yang sedang diedit)
        if (items.some((it, idx) => idx !== editingIndex && it.id === id)) {
            alert('Bahan baku ini sudah ditambahkan ke dalam daftar!');
            return;
        }

        if (jenisPergerakan === 'keluar') {
            if (qty > stokMax) {
                alert(`Stok tidak mencukupi untuk ${nama}! Maksimal pengeluaran: ${stokMax} ${satuan}`);
                return;
            }
        }

        const newItem = { id, nama, warna, kode, quantity: qty, satuan };
        if (editingIndex === null) {
            items.push(newItem);
        } else {
            items[editingIndex] = newItem;
        }

        resetItemForm();
        renderTable();
    }

    btnTambah.addEventListener('click', addItem);
});
