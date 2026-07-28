<div data-reject-modal class="fixed inset-0 z-[70] hidden items-center justify-center bg-[#0F034D]/40 px-4 backdrop-blur-sm opacity-0 transition-opacity duration-300">
    <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl shadow-[#0F034D]/20 transform scale-95 transition-transform duration-300">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-[#0F034D]">Tolak Ajuan?</h3>
                <p class="mt-1 text-sm text-gray-500">Isi alasan penolakan agar pengaju tahu penyebabnya.</p>
            </div>
            <button type="button" data-close-reject-modal class="rounded-full p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors" aria-label="Tutup modal">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" class="mt-4 space-y-4">
            @csrf
            <textarea name="catatan_respon" rows="4" required maxlength="1000" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-[#0F034D] focus:ring-1 focus:ring-[#0F034D]/20" placeholder="Contoh: Barang belum siap diambil hari ini"></textarea>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" data-close-reject-modal class="rounded-xl border border-gray-200 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-colors">Batal</button>
                <button type="submit" class="rounded-xl bg-red-600 py-2.5 text-sm font-bold text-white hover:bg-red-700 transition-colors">Tolak Ajuan</button>
            </div>
        </form>
    </div>
</div>