document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("role_dropdown_btn");
    const menu = document.getElementById("role_dropdown_menu");
    const chevron = document.getElementById("role_chevron");
    const hiddenInput = document.getElementById("role_input");
    const selectedText = document.getElementById("role_selected_text");
    const options = document.querySelectorAll(".role-option");

    // 1. Fungsi Buka/Tutup Dropdown
    btn.addEventListener("click", function (e) {
        e.preventDefault(); // Mencegah form tersubmit tak sengaja
        const isHidden = menu.classList.contains("hidden");

        if (isHidden) {
            // Buka menu
            menu.classList.remove("hidden");
            setTimeout(() => {
                menu.classList.remove("opacity-0", "scale-95");
                chevron.classList.add("rotate-180"); // Putar panah
            }, 10);
        } else {
            closeDropdown();
        }
    });

    // 2. Fungsi Menutup Dropdown (untuk dipakai ulang)
    function closeDropdown() {
        menu.classList.add("opacity-0", "scale-95");
        chevron.classList.remove("rotate-180"); // Balikkan panah
        setTimeout(() => menu.classList.add("hidden"), 200);
    }

    // 3. Menutup dropdown jika user klik di luar area
    document.addEventListener("click", function (e) {
        if (!btn.contains(e.target) && !menu.contains(e.target)) {
            if (!menu.classList.contains("hidden")) {
                closeDropdown();
            }
        }
    });

    // 4. Logika ketika salah satu opsi dipilih
    options.forEach((option) => {
        option.addEventListener("click", function () {
            const value = this.getAttribute("data-value");

            // Ambil elemen span teks dan icon bawaan
            const optionSpan = this.querySelector(".option-text");
            const lucideIcon = optionSpan.querySelector("svg");
            const iconSvgClone = lucideIcon.cloneNode(true);

            // Ekstrak hanya teks (hilangkan elemen SVG dari bacaan)
            let rawText = "";
            optionSpan.childNodes.forEach((node) => {
                if (node.nodeType === Node.TEXT_NODE) {
                    rawText += node.textContent;
                }
            });

            // Update input form & tombol utama
            hiddenInput.value = value;
            selectedText.innerText = rawText.trim();
            selectedText.classList.replace("text-gray-500", "text-gray-900");
            selectedText.classList.add("font-medium");

            // Tempelkan ikon di tombol utama
            const iconContainer = document.getElementById("role_selected_icon");
            iconContainer.innerHTML = "";
            iconSvgClone.setAttribute("class", "w-4 h-4 text-[#0F034D]"); // Hapus semua class ribet bawaan dari list
            iconContainer.appendChild(iconSvgClone);
            iconContainer.classList.remove("hidden");

            // ==========================================
            // MAGIC RESET & SET ACTIVE STATE
            // ==========================================
            // Cukup hapus class 'is-active' dari semua opsi
            options.forEach((opt) => opt.classList.remove("is-active"));

            // Lalu tambahkan class 'is-active' hanya pada opsi yang baru diklik
            this.classList.add("is-active");

            closeDropdown();
        });
    });

    // 5. Fitur "Ingat Pilihan" jika ada error validasi dari Laravel (old data)
    if (hiddenInput.value) {
        const activeOption = document.querySelector(
            `.role-option[data-value="${hiddenInput.value}"]`,
        );
        if (activeOption) {
            activeOption.click(); // Otomatis klik opsi yang lama jika halaman direfresh karena error
        }
    }
});
