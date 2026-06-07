// Toggle modal pop-up
window.toggleModal = function (modalID) {
    const modal = document.getElementById(modalID);
    const isHidden = modal.classList.contains("hidden");
    const innerContent = modal.querySelector("div.transform");

    if (isHidden) {
        modal.classList.remove("hidden");
        setTimeout(() => {
            modal.classList.remove("opacity-0");
            innerContent.classList.remove("scale-95");
        }, 10);
    } else {
        modal.classList.add("opacity-0");
        innerContent.classList.add("scale-95");
        setTimeout(() => {
            modal.classList.add("hidden");
        }, 300);
    }
};

window.openEditModal = function (button) {
    const id = button.getAttribute("data-id");
    const kode = button.getAttribute("data-kode");
    const nama = button.getAttribute("data-nama");
    const warna = button.getAttribute("data-warna");
    const kategori = button.getAttribute("data-kategori");
    const satuan = button.getAttribute("data-satuan");

    // Set Form Action URL
    const form = document.getElementById("editForm");
    form.action = `/admin/bahan-baku/${id}`;

    // Isi input fields
    document.getElementById("edit_kode").value = kode;
    document.getElementById("edit_nama").value = nama;
    document.getElementById("edit_warna").value = warna;
    document.getElementById("edit_kategori").value = kategori;
    document.getElementById("edit_satuan").value = satuan;

    window.toggleModal("edit-modal");
};

// Fungsi untuk membuka menu Filter atau Sort
window.toggleFilterMenu = function (menuID) {
    const menus = ["filterDropdown", "sortDropdown"];

    // Tutup menu lain yang sedang terbuka
    menus.forEach((id) => {
        if (id !== menuID) {
            const otherMenu = document.getElementById(id);
            if (otherMenu && !otherMenu.classList.contains("hidden")) {
                otherMenu.classList.add(
                    "opacity-0",
                    "scale-95",
                    "pointer-events-none",
                );
                setTimeout(() => otherMenu.classList.add("hidden"), 200);
            }
        }
    });

    // Toggle menu yang diklik
    const menu = document.getElementById(menuID);
    if (menu.classList.contains("hidden")) {
        menu.classList.remove("hidden");
        setTimeout(() => {
            menu.classList.remove(
                "opacity-0",
                "scale-95",
                "pointer-events-none",
            );
        }, 10);
    } else {
        menu.classList.add("opacity-0", "scale-95", "pointer-events-none");
        setTimeout(() => {
            menu.classList.add("hidden");
        }, 200);
    }
};

// Event listener untuk menutup dropdown ketika mengklik di luar area menu
document.addEventListener("click", function (event) {
    const filterMenu = document.getElementById("filterDropdown");
    const sortMenu = document.getElementById("sortDropdown");
    const filterBtn = filterMenu ? filterMenu.previousElementSibling : null;
    const sortBtn = sortMenu ? sortMenu.previousElementSibling : null;

    if (filterMenu && !filterMenu.classList.contains("hidden")) {
        if (
            !filterMenu.contains(event.target) &&
            !filterBtn.contains(event.target)
        ) {
            filterMenu.classList.add(
                "opacity-0",
                "scale-95",
                "pointer-events-none",
            );
            setTimeout(() => filterMenu.classList.add("hidden"), 200);
        }
    }

    if (sortMenu && !sortMenu.classList.contains("hidden")) {
        if (
            !sortMenu.contains(event.target) &&
            !sortBtn.contains(event.target)
        ) {
            sortMenu.classList.add(
                "opacity-0",
                "scale-95",
                "pointer-events-none",
            );
            setTimeout(() => sortMenu.classList.add("hidden"), 200);
        }
    }
});
