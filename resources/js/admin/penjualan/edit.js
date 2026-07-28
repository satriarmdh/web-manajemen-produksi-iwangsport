document.addEventListener('DOMContentLoaded', () => {
    const produkData = JSON.parse(document.getElementById('produk-data').textContent);
    const existingItems = JSON.parse(document.getElementById('existing-items').textContent);
    let items = existingItems.map(item => ({ ...item, product: produkData.find(p => String(p.id) === String(item.produk_id)) }));
    let editingIndex = null;

    const formatRupiah = num => 'Rp ' + Number(num).toLocaleString('id-ID');
    const selectedProduct = () => produkData.find(p => String(p.id) === String(document.getElementById('input_produk_value').value));
    const originalQty = productId => Number(existingItems.find(item => String(item.produk_id) === String(productId))?.qty || 0);

    function availableStock(product) { 
        return Number(product.stok) + originalQty(product.id); 
    }

    function refreshInputPreview() {
        const product = selectedProduct();
        const qty = Number(document.getElementById('input-qty').value || 0);
        document.getElementById('input-harga').value = product ? formatRupiah(product.harga) : '-';
        document.getElementById('input-subtotal').value = product && qty > 0 ? formatRupiah(product.harga * qty) : '-';
        document.getElementById('input-stock-hint').textContent = product ? `(Maks: ${availableStock(product)} pcs)` : '(Pilih produk)';
        document.getElementById('btn-tambah-item').disabled = !product || qty < 1;
    }

    function showItemError(message) { 
        const error = document.getElementById('item-form-error'); 
        error.textContent = message; 
        error.classList.toggle('hidden', !message); 
    }

    function resetItemForm() { 
        editingIndex = null; 
        resetCustomDropdown('input_produk'); 
        document.getElementById('input-qty').value = ''; 
        document.getElementById('btn-tambah-item').textContent = 'Tambah'; 
        showItemError(''); 
        refreshInputPreview(); 
    }

    function saveItem() {
        const product = selectedProduct();
        const qty = Number(document.getElementById('input-qty').value || 0);
        if (!product || qty < 1) return showItemError('Pilih produk dan masukkan jumlah minimal 1.');
        if (qty > availableStock(product)) return showItemError(`Jumlah melebihi batas tersedia (${availableStock(product)} pcs).`);
        if (items.some((item, index) => index !== editingIndex && String(item.produk_id) === String(product.id))) return showItemError('Produk sudah ditambahkan. Edit item yang sudah ada.');
        
        const item = { produk_id: product.id, qty, product };
        if (editingIndex === null) items.push(item); else items[editingIndex] = item;
        resetItemForm(); 
        renderItems();
    }

    function editItem(index) { 
        const item = items[index]; 
        editingIndex = index; 
        setCustomDropdownValue('input_produk', String(item.produk_id)); 
        document.getElementById('input-qty').value = item.qty; 
        document.getElementById('btn-tambah-item').textContent = 'Simpan'; 
        showItemError(''); 
        refreshInputPreview(); 
        document.getElementById('input_produk_input').scrollIntoView({ behavior: 'smooth', block: 'center' }); 
    }

    function removeItem(index) { 
        items.splice(index, 1); 
        if (editingIndex === index) resetItemForm(); 
        renderItems(); 
    }

    function renderItems() {
        const body = document.getElementById('items-body');
        if (!items.length) {
            body.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-sm text-gray-400">Belum ada produk ditambahkan.</td></tr>';
        } else {
            body.innerHTML = items.map((item, index) => `<tr class="border-b border-gray-100 hover:bg-gray-50/50">
                <td class="py-3 px-4 text-gray-500">${index + 1}</td>
                <td class="py-3 px-4"><input type="hidden" name="items[${index}][produk_id]" value="${item.produk_id}"><input type="hidden" name="items[${index}][qty]" value="${item.qty}"><div class="font-medium text-gray-900">${item.product.nama}</div><div class="text-xs text-gray-500">${item.product.warna}</div></td>
                <td class="py-3 px-4 text-right text-gray-600">${formatRupiah(item.product.harga)}</td>
                <td class="py-3 px-4 text-center font-medium text-gray-700">${item.qty} pcs</td>
                <td class="py-3 px-4 text-right font-semibold text-[#0F034D]">${formatRupiah(item.product.harga * item.qty)}</td>
                <td class="py-3 px-4"><div class="flex justify-center gap-1"><button type="button" onclick="editItem(${index})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button><button type="button" onclick="removeItem(${index})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg" title="Hapus"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div></td>
            </tr>`).join('');
        }
        document.getElementById('grand-total').textContent = formatRupiah(items.reduce((total, item) => total + item.product.harga * item.qty, 0));
    }

    // Expose functions to global/window scope for inline onclick triggers
    window.editItem = editItem;
    window.removeItem = removeItem;

    // Initialization
    initCustomDropdown('pelanggan'); 
    initCustomDropdown('input_produk'); 
    renderItems();
    document.getElementById('input_produk_value').addEventListener('change', refreshInputPreview);
    document.getElementById('input-qty').addEventListener('input', refreshInputPreview);
    document.getElementById('btn-tambah-item').addEventListener('click', saveItem);
    
    const pelangganValue = document.getElementById('pelanggan_value').value;
    if (pelangganValue) {
        setCustomDropdownValue('pelanggan', pelangganValue);
    }
});
