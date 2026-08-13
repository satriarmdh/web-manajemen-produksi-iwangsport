{{-- Modal Preview Gambar --}}
<div id="modal-preview-image" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-5 shadow-2xl border border-gray-100 space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="text-sm font-bold text-[#0F034D]" id="preview-title">Bukti Transfer</h3>
            <button type="button" onclick="document.getElementById('modal-preview-image').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
        </div>
        <div class="text-center p-2 bg-gray-50 rounded-xl max-h-[70vh] overflow-auto">
            <img id="preview-img-target" src="" alt="Resi Transfer" class="max-w-full h-auto mx-auto rounded-lg shadow-sm">
        </div>
    </div>
</div>
