/**
 * Global Slide Panel Controller
 * Desktop: slide from right | Mobile: centered popup
 */

class SlidePanelController {
    constructor() {
        this.init();
    }

    init() {
        // Auto-bind close buttons
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-panel-close]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const panel = e.currentTarget.closest('.slide-panel');
                    if (panel) this.close(panel.id);
                });
            });

            // Close on backdrop click
            document.querySelectorAll('.slide-panel-backdrop').forEach(backdrop => {
                backdrop.addEventListener('click', (e) => {
                    const panel = e.currentTarget.closest('.slide-panel');
                    if (panel) this.close(panel.id);
                });
            });
        });

        // Close on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAll();
            }
        });
    }

    /**
     * Open panel by ID
     */
    open(panelId) {
        const panel = document.getElementById(panelId);
        if (!panel) {
            console.warn(`Panel "${panelId}" not found`);
            return;
        }

        document.body.style.overflow = 'hidden';
        panel.classList.add('is-open');
        panel.dispatchEvent(new CustomEvent('panel:open', { detail: { panelId } }));
    }

    /**
     * Close panel by ID
     */
    close(panelId) {
        const panel = document.getElementById(panelId);
        if (!panel) return;

        panel.classList.remove('is-open');
        document.body.style.overflow = '';
        panel.dispatchEvent(new CustomEvent('panel:close', { detail: { panelId } }));
    }

    /**
     * Toggle panel by ID
     */
    toggle(panelId) {
        const panel = document.getElementById(panelId);
        if (!panel) return;

        if (panel.classList.contains('is-open')) {
            this.close(panelId);
        } else {
            this.open(panelId);
        }
    }

    /**
     * Close all open panels
     */
    closeAll() {
        document.querySelectorAll('.slide-panel.is-open').forEach(panel => {
            this.close(panel.id);
        });
    }
}

// Create global instance
window.slidePanel = new SlidePanelController();

// Helper functions
window.openPanel = (id) => window.slidePanel.open(id);
window.closePanel = (id) => window.slidePanel.close(id);
window.togglePanel = (id) => window.slidePanel.toggle(id);

/**
 * Touch-Friendly Nested Filter Support (Dual Pattern)
 * 
 * Pattern A (produk, bahan-baku, supplier): `.relative.group` + `div.absolute` + `invisible/visible`
 * Pattern B (pergerakan-stok): `.relative.group` + `.nested-submenu` + `hidden`
 * 
 * Desktop: hover works via CSS group-hover.
 * Mobile/touch: click toggles visibility.
 */
(function() {
    document.addEventListener('click', function(e) {
        const groupDiv = e.target.closest('.relative.group');

        if (groupDiv) {
            const button = groupDiv.querySelector('button');
            if (!button) return;
            if (!button.contains(e.target)) return;

            // Try Pattern B first: .nested-submenu with hidden class
            const nestedSubmenu = groupDiv.querySelector('.nested-submenu');
            if (nestedSubmenu) {
                e.preventDefault();
                e.stopPropagation();

                // Close other open nested-submenus
                document.querySelectorAll('.nested-submenu:not(.hidden)').forEach(el => {
                    if (el !== nestedSubmenu) el.classList.add('hidden');
                });

                // Toggle
                nestedSubmenu.classList.toggle('hidden');
                return;
            }

            // Try Pattern A: div.absolute with invisible/visible
            const absSubmenu = groupDiv.querySelector('div.absolute');
            if (absSubmenu) {
                e.preventDefault();
                e.stopPropagation();

                const isVisible = absSubmenu.classList.contains('visible');

                // Close other open absolute submenus
                document.querySelectorAll('.relative.group div.absolute.visible').forEach(el => {
                    if (el !== absSubmenu) {
                        el.classList.remove('visible', 'opacity-100');
                        el.classList.add('invisible', 'opacity-0');
                    }
                });

                // Toggle
                if (!isVisible) {
                    absSubmenu.classList.add('visible', 'opacity-100');
                    absSubmenu.classList.remove('invisible', 'opacity-0');
                } else {
                    absSubmenu.classList.remove('visible', 'opacity-100');
                    absSubmenu.classList.add('invisible', 'opacity-0');
                }
            }

            return;
        }

        // Click outside: close ALL open submenus (both patterns)
        document.querySelectorAll('.nested-submenu:not(.hidden)').forEach(el => {
            el.classList.add('hidden');
        });
        document.querySelectorAll('.relative.group div.absolute.visible').forEach(el => {
            el.classList.remove('visible', 'opacity-100');
            el.classList.add('invisible', 'opacity-0');
        });
    });
})();
