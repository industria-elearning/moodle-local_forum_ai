/**
 * Controlador JS para la página de revisión de respuesta AI.
 */
export const init = () => {
    const editBtn = document.getElementById("edit-btn");
    const viewDiv = document.getElementById("airesponse-view");
    const editForm = document.getElementById("airesponse-edit");
    const cancelBtn = document.getElementById("cancel-edit");

    if (editBtn) {
        editBtn.addEventListener("click", () => {
            viewDiv.style.display = "none";
            editForm.style.display = "block";
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener("click", () => {
            editForm.style.display = "none";
            viewDiv.style.display = "block";
        });
    }
};
