import Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * Controlador JS para la página de revisión de respuesta AI.
 */
export const init = () => {
    const editBtn = document.getElementById("edit-btn");
    const viewDiv = document.getElementById("airesponse-view");
    const editForm = document.getElementById("airesponse-edit");
    const cancelBtn = document.getElementById("cancel-edit");
    const textarea = editForm ? editForm.querySelector("textarea[name='message']") : null;
    const saveBtn = editForm ? editForm.querySelector("button[type='submit']") : null;

    const token = editForm ? editForm.dataset.token : null;

    // --- Alternar edición ---
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

    // --- Guardar cambios por AJAX ---
    if (saveBtn) {
        saveBtn.addEventListener("click", e => {
            e.preventDefault();
            const newMessage = textarea.value;

            Ajax.call([{
                methodname: "local_forum_ai_update_response",
                args: { token: token, message: newMessage },
            }])[0].done(response => {
                if (response.status === "ok") {
                    // Reemplazar contenido en vista normal
                    viewDiv.querySelector(".card-text").innerHTML = response.message;

                    Notification.addNotification({
                        message: "Respuesta actualizada correctamente.",
                        type: "success"
                    });

                    // Volver a vista normal
                    editForm.style.display = "none";
                    viewDiv.style.display = "block";
                } else {
                    Notification.addNotification({
                        message: "No se pudo actualizar la respuesta.",
                        type: "error"
                    });
                }
            }).fail(Notification.exception);
        });
    }

    // --- Aprobar / Rechazar ---
    document.querySelectorAll(".action-btn").forEach(btn => {
        btn.addEventListener("click", e => {
            e.preventDefault();

            const action = btn.dataset.action; // approve | reject
            const token = btn.dataset.token;

            Ajax.call([{
                methodname: "local_forum_ai_approve_response",
                args: { token: token, action: action },
            }])[0].done(response => {
                if (response.success) {
                    Notification.addNotification({
                        message: action === "approve"
                            ? "Respuesta AI aprobada y publicada con éxito."
                            : "Respuesta AI rechazada.",
                        type: "success"
                    });

                    setTimeout(() => {
                        window.location.href = M.cfg.wwwroot + "/mod/forum/discuss.php?d=" + btn.dataset.discussionid;
                    }, 1500);
                } else {
                    Notification.addNotification({
                        message: "No se pudo procesar la acción.",
                        type: "error"
                    });
                }
            }).fail(Notification.exception);
        });
    });
};
