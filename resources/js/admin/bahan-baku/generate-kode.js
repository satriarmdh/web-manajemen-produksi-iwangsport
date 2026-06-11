document.addEventListener('DOMContentLoaded', function() {
    const kategoriSelect = document.getElementById('add_kategori_value');
    const kodeInput = document.getElementById('add_kode_bahan');

    if(kategoriSelect && kodeInput) {
        // Ambil string JSON dari atribut 'data-prefixes', lalu ubah jadi Objek JS
        const rawData = kategoriSelect.getAttribute('data-prefixes');
        const prefixMap = rawData ? JSON.parse(rawData) : {};

        kategoriSelect.addEventListener('change', function() {
            const kategoriTerpilih = this.value;
            const kodeAsli = prefixMap[kategoriTerpilih];

            if(kodeAsli) {
                // Berikan efek visual kalau kode sudah terisi otomatis
                kodeInput.value = kodeAsli;
                kodeInput.classList.remove('text-gray-500', 'italic');
                kodeInput.classList.add('text-[#0F034D]', 'font-bold', 'bg-blue-50/50', 'border-blue-200');
            } else {
                // Kembalikan ke mode awal jika tidak memilih apapun
                kodeInput.value = '';
                kodeInput.classList.add('text-gray-500', 'italic');
                kodeInput.classList.remove('text-[#0F034D]', 'font-bold', 'bg-blue-50/50', 'border-blue-200');
            }
        });
    }
});