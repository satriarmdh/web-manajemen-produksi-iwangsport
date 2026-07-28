document.addEventListener('DOMContentLoaded', () => {
    const config = window.dashboardConfig;
    if (!config) return;

    const initial = config.initialSalesTrend;
    const endpoint = config.salesTrendEndpoint;
    const canvas = document.getElementById('salesTrendChart');
    if (!canvas) return;

    const totalEl = document.getElementById('sales-total');
    const deltaEl = document.getElementById('sales-delta');
    const granularityEl = document.getElementById('sales-granularity');
    const emptyEl = document.getElementById('sales-empty');
    const loadingEl = document.getElementById('sales-loading');
    const rangeBtns = document.querySelectorAll('.range-btn');
    const startInput = document.getElementById('sales-start');
    const endInput = document.getElementById('sales-end');
    const applyBtn = document.getElementById('sales-apply');
    const resetBtn = document.getElementById('sales-reset');
    const dateToggle = document.getElementById('sales-date-toggle');
    const dateDropdown = document.getElementById('sales-date-dropdown');
    const dateArrow = document.getElementById('sales-date-arrow');
    const dateLabel = document.getElementById('sales-date-label');

    const NAVY = '#0F034D';
    let chart;

    function formatNumber(n) {
        return new Intl.NumberFormat('id-ID').format(n);
    }

    function renderChart(data) {
        const hasData = (data.values || []).some(v => v > 0);
        emptyEl.classList.toggle('hidden', hasData);
        totalEl.innerHTML = `${formatNumber(data.total || 0)} <span class="text-xs font-normal text-gray-400">Pcs</span>`;

        // Keterangan granularitas.
        if (granularityEl) {
            granularityEl.textContent = data.granularity === 'month'
                ? '(Dikelompokkan per bulan)'
                : '(Dikelompokkan per hari)';
        }

        // Indikator perbandingan periode sebelumnya.
        if (deltaEl) {
            const pct = data.change_percent;
            deltaEl.classList.remove('hidden', 'text-green-600', 'text-rose-600', 'text-gray-400');
            if (pct === null || pct === undefined) {
                deltaEl.classList.add('text-gray-400');
                deltaEl.innerHTML = 'Baru · tidak ada data periode lalu';
            } else {
                const up = pct >= 0;
                deltaEl.classList.add(up ? 'text-green-600' : 'text-rose-600');
                const arrow = up
                    ? '<svg class="w-3 h-3 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>'
                    : '<svg class="w-3 h-3 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>';
                deltaEl.innerHTML = `${arrow} ${Math.abs(pct)}% <span class="text-gray-400 font-normal">dari periode lalu</span>`;
            }
        }

        const cfgData = {
            labels: data.labels || [],
            datasets: [{
                label: 'Terjual (Pcs)',
                data: data.values || [],
                backgroundColor: NAVY,
                hoverBackgroundColor: '#1a0a6b',
                borderRadius: 6,
                maxBarThickness: 40,
            }]
        };

        if (chart) {
            chart.data = cfgData;
            chart.update();
        } else {
            chart = new Chart(canvas, {
                type: 'bar',
                data: cfgData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: NAVY,
                            titleFont: { size: 11, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: (ctx) => ` Terjual: ${formatNumber(ctx.raw)} Pcs`
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 }, color: '#9ca3af' }
                        },
                        y: {
                            grid: { color: '#f3f4f6' },
                            border: { dash: [4, 4] },
                            ticks: { font: { size: 10 }, color: '#9ca3af', precision: 0 }
                        }
                    }
                }
            });
        }
    }

    function fetchTrend(params, activeBtn) {
        loadingEl.classList.remove('hidden');
        const url = new URL(endpoint, window.location.origin);
        Object.entries(params).forEach(([k, v]) => url.searchParams.append(k, v));

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.json();
            })
            .then(data => {
                if (activeBtn) setActiveButton(activeBtn);
                renderChart(data);
            })
            .catch(err => {
                console.error(err);
                alert('Gagal memuat data tren penjualan.');
            })
            .finally(() => {
                loadingEl.classList.add('hidden');
            });
    }

    function setActiveButton(btn) {
        rangeBtns.forEach(b => {
            b.classList.remove('bg-[#0F034D]', 'text-white', 'shadow-sm');
            b.classList.add('text-gray-500', 'hover:text-gray-800');
        });
        if (btn) {
            btn.classList.add('bg-[#0F034D]', 'text-white', 'shadow-sm');
            btn.classList.remove('text-gray-500', 'hover:text-gray-800');
        }
    }

    // Dropdown Handlers
    function isDropdownOpen() {
        return !dateDropdown.classList.contains('hidden');
    }

    function openDropdown() {
        dateDropdown.classList.remove('hidden');
        requestAnimationFrame(() => {
            dateDropdown.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
            dateDropdown.classList.add('opacity-100', 'scale-100');
        });
        dateArrow.classList.add('rotate-180');
    }

    function closeDropdown() {
        if (dateDropdown.classList.contains('hidden')) return;
        dateDropdown.classList.remove('opacity-100', 'scale-100');
        dateDropdown.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
        dateArrow.classList.remove('rotate-180');
        setTimeout(() => {
            if (dateDropdown.classList.contains('opacity-0')) {
                dateDropdown.classList.add('hidden');
            }
        }, 200);
    }

    function formatDateLabel(dStr) {
        const parts = dStr.split('-');
        if (parts.length !== 3) return dStr;
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        return `${parts[2]} ${months[parseInt(parts[1]) - 1]} ${parts[0]}`;
    }

    function markCustomActive(start, end) {
        dateLabel.textContent = `${formatDateLabel(start)} — ${formatDateLabel(end)}`;
        dateToggle.classList.remove('border-gray-200', 'text-gray-600', 'hover:bg-gray-50');
        dateToggle.classList.add('border-[#0F034D]', 'text-[#0F034D]');
        setActiveButton(null);
    }

    function resetCustomLabel() {
        dateLabel.textContent = 'Rentang Tanggal';
        dateToggle.classList.add('border-gray-200', 'text-gray-600', 'hover:bg-gray-50');
        dateToggle.classList.remove('border-[#0F034D]', 'text-[#0F034D]');
    }

    dateToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        isDropdownOpen() ? closeDropdown() : openDropdown();
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('[data-date-dropdown]')) closeDropdown();
    });

    rangeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            startInput.value = '';
            endInput.value = '';
            resetCustomLabel();
            fetchTrend({ range: btn.dataset.range }, btn);
        });
    });

    applyBtn.addEventListener('click', () => {
        if (!startInput.value || !endInput.value) {
            alert('Silakan isi tanggal awal dan tanggal akhir.');
            return;
        }
        if (startInput.value > endInput.value) {
            alert('Tanggal awal tidak boleh melebihi tanggal akhir.');
            return;
        }
        markCustomActive(startInput.value, endInput.value);
        closeDropdown();
        fetchTrend({ start: startInput.value, end: endInput.value }, null);
    });

    resetBtn.addEventListener('click', () => {
        startInput.value = '';
        endInput.value = '';
        resetCustomLabel();
        closeDropdown();
        const defaultBtn = document.querySelector('.range-btn[data-range="30d"]');
        fetchTrend({ range: '30d' }, defaultBtn);
    });

    // Render awal (default 1 Bulan / 30d dari server).
    renderChart(initial);
});
