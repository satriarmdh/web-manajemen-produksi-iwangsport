document.querySelectorAll('[data-finish-toggle]').forEach((toggle) => {
            const detailId = toggle.dataset.finishToggle;
            const inputs = document.querySelectorAll(`[data-finish-input="${detailId}"]`);

            const syncFinishInputs = () => {
                inputs.forEach((input) => {
                    input.value = toggle.checked ? '1' : '0';
                });
            };

            toggle.addEventListener('change', syncFinishInputs);
            syncFinishInputs();
        });