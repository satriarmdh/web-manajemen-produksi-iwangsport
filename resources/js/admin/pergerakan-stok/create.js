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

    let items = [];

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
                    <button type="button" class="btn-hapus-item text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 transition-colors cursor-pointer" data-id="${item.id}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </td>
            `;
            tableBody.appendChild(tr);
        });

        document.querySelectorAll('.btn-hapus-item').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                items = items.filter(it => it.id !== id);
                renderTable();
            });
        });
    }

    btnTambah.addEventListener('click', () => {
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

        if (items.some(it => it.id === id)) {
            alert('Bahan baku ini sudah ditambahkan ke dalam daftar!');
            return;
        }

        if (jenisPergerakan === 'keluar') {
            if (qty > stokMax) {
                alert(`Stok tidak mencukupi untuk ${nama}! Maksimal pengeluaran: ${stokMax} ${satuan}`);
                return;
            }
        }

        items.push({ id, nama, warna, kode, quantity: qty, satuan });

        // Reset custom dropdown
        resetCustomDropdown('input_bahan_baku');
        inputQty.value = '';

        renderTable();
    });
});
