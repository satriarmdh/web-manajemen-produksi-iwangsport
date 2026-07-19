// Main tabs switcher (Detail Produk vs Tahapan Produksi)
document.querySelectorAll('[data-main-tab-trigger]').forEach(btn => {
    btn.addEventListener('click', () => {
        const targetKey = btn.dataset.mainTabTrigger;
        const isProduk = targetKey.startsWith('produk-');
        const id = targetKey.replace('produk-', '').replace('tahapan-', '');

        const produkBtn = document.querySelector(`[data-main-tab-trigger="produk-${id}"]`);
        const tahapanBtn = document.querySelector(`[data-main-tab-trigger="tahapan-${id}"]`);
        const produkContent = document.querySelector(`[data-main-tab-content="produk-${id}"]`);
        const tahapanContent = document.querySelector(`[data-main-tab-content="tahapan-${id}"]`);

        if (isProduk) {
            produkBtn.classList.replace('bg-gray-50', 'bg-[#0F034D]');
            produkBtn.classList.replace('text-gray-500', 'text-white');
            produkBtn.classList.replace('hover:bg-gray-100', 'hover:bg-[#24116f]');
            produkBtn.classList.add('shadow-sm');
            tahapanBtn.classList.replace('bg-[#0F034D]', 'bg-gray-50');
            tahapanBtn.classList.replace('text-white', 'text-gray-500');
            tahapanBtn.classList.replace('hover:bg-[#24116f]', 'hover:bg-gray-100');
            tahapanBtn.classList.remove('shadow-sm');

            produkContent.classList.remove('hidden');
            tahapanContent.classList.add('hidden');
        } else {
            tahapanBtn.classList.replace('bg-gray-50', 'bg-[#0F034D]');
            tahapanBtn.classList.replace('text-gray-500', 'text-white');
            tahapanBtn.classList.replace('hover:bg-gray-100', 'hover:bg-[#24116f]');
            tahapanBtn.classList.add('shadow-sm');
            produkBtn.classList.replace('bg-[#0F034D]', 'bg-gray-50');
            produkBtn.classList.replace('text-white', 'text-gray-500');
            produkBtn.classList.replace('hover:bg-[#24116f]', 'hover:bg-gray-100');
            produkBtn.classList.remove('shadow-sm');

            tahapanContent.classList.remove('hidden');
            produkContent.classList.add('hidden');
        }
    });
});

// Sub-tabs switcher (Stok Aktif vs Log Serah Terima vs Riwayat Terima)
document.querySelectorAll('[data-tab-trigger]').forEach(btn => {
    btn.addEventListener('click', () => {
        const targetKey = btn.dataset.tabTrigger;
        const dashIndex = targetKey.indexOf('-');
        const prefix = targetKey.substring(0, dashIndex);
        const id = targetKey.substring(dashIndex + 1);

        const triggers = document.querySelectorAll(`[data-tab-trigger$="-${id}"]`);
        const contents = document.querySelectorAll(`[data-tab-content$="-${id}"]`);

        triggers.forEach(t => {
            const tKey = t.dataset.tabTrigger;
            if (tKey === targetKey) {
                t.classList.replace('bg-gray-50', 'bg-[#0F034D]');
                t.classList.replace('text-gray-500', 'text-white');
                t.classList.replace('hover:bg-gray-100', 'hover:bg-[#24116f]');
            } else {
                t.classList.replace('bg-[#0F034D]', 'bg-gray-50');
                t.classList.replace('text-white', 'text-gray-500');
                t.classList.replace('hover:bg-[#24116f]', 'hover:bg-gray-100');
            }
        });

        contents.forEach(c => {
            const cKey = c.getAttribute('data-tab-content');
            if (cKey === targetKey) {
                c.classList.remove('hidden');
            } else {
                c.classList.add('hidden');
            }
        });
    });
});
