document.addEventListener('DOMContentLoaded', function() {
    // Modal Tambah - Preview kode bahan saat kategori dipilih
    const addKategoriSelect = document.getElementById('add_kategori_value');
    const addKodeInput = document.getElementById('add_kode_bahan');

    if(addKategoriSelect && addKodeInput) {
        // Ambil string JSON dari atribut 'data-prefixes', lalu ubah jadi Objek JS
        const rawData = addKategoriSelect.getAttribute('data-prefixes');
        const prefixMap = rawData ? JSON.parse(rawData) : {};

        addKategoriSelect.addEventListener('change', function() {
            const kategoriTerpilih = this.value;
            const kodeAsli = prefixMap[kategoriTerpilih];

            if(kodeAsli) {
                addKodeInput.value = kodeAsli;
            } else {
                addKodeInput.value = '';
            }

            // Sync Satuan (kain -> roll, lainnya -> pcs)
            if (kategoriTerpilih) {
                const targetSatuan = (kategoriTerpilih.toLowerCase() === 'kain') ? 'roll' : 'pcs';
                if (typeof window.setCustomDropdownValue === 'function') {
                    window.setCustomDropdownValue('add_satuan', targetSatuan);
                }
            }
        });
    }

    // Modal Edit - Preview kode bahan saat kategori berubah
    const editKategoriSelect = document.getElementById('edit_kategori_value');
    const editKodeInput = document.getElementById('edit_kode');

    if(editKategoriSelect && editKodeInput) {
        // Simpan kode dan kategori asli
        let originalKode = '';
        let originalKategori = '';
        let isFirstChange = true;

        // Listen to change event dari custom dropdown
        editKategoriSelect.addEventListener('change', function() {
            const kategoriBaru = this.value;
            
            // Sync Satuan (kain -> roll, lainnya -> pcs)
            if (kategoriBaru) {
                const targetSatuan = (kategoriBaru.toLowerCase() === 'kain') ? 'roll' : 'pcs';
                if (typeof window.setCustomDropdownValue === 'function') {
                    window.setCustomDropdownValue('edit_satuan', targetSatuan);
                }
            }

            // Simpan kode asli saat pertama kali modal dibuka
            if (isFirstChange) {
                originalKode = editKodeInput.value;
                originalKategori = kategoriBaru;
                isFirstChange = false;
                return; // Skip first change (saat modal baru dibuka)
            }

            // Jika kategori berubah, fetch kode baru
            if (kategoriBaru && kategoriBaru !== originalKategori) {
                // Fetch kode baru via AJAX
                fetch(`/admin/bahan-baku/generate-kode/${kategoriBaru}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.kode) {
                            editKodeInput.value = data.kode;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching kode:', error);
                    });
            } else {
                // Kembalikan ke kode asli jika kategori tidak berubah
                editKodeInput.value = originalKode;
            }
        });

        // Reset saat modal ditutup
        const editModal = document.getElementById('edit-modal');
        if (editModal) {
            editModal.addEventListener('transitionend', function() {
                if (editModal.classList.contains('hidden')) {
                    originalKode = '';
                    originalKategori = '';
                    isFirstChange = true;
                }
            });
        }
    }
});