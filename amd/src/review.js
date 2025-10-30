import Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * JS controller for the AI response review page.
 */
export const init = () => {
    const editBtn = document.getElementById("edit-btn");
    const viewDiv = document.getElementById("airesponse-view");
    const editForm = document.getElementById("airesponse-edit");
    const cancelBtn = document.getElementById("cancel-edit");
    const textarea = editForm ? editForm.querySelector("textarea[name='message']") : null;
    const saveBtn = editForm ? editForm.querySelector("button[type='submit']") : null;

    const token = editForm ? editForm.dataset.token : null;

    // --- Toggle editing ---
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

    // --- Save changes via AJAX ---
    if (saveBtn) {
        saveBtn.addEventListener("click", e => {
            e.preventDefault();
            const newMessage = textarea.value;

            Ajax.call([{
                methodname: "local_forum_ai_update_response",
                args: { token: token, message: newMessage },
            }])[0].done(response => {
                if (response.status === "ok") {
                    // Replace content in normal view
                    viewDiv.querySelector(".card-text").innerHTML = response.message;

                    Notification.addNotification({
                        message: "Response successfully updated.",
                        type: "success"
                    });

                    // Return to normal view
                    editForm.style.display = "none";
                    viewDiv.style.display = "block";
                } else {
                    Notification.addNotification({
                        message: "The response could not be updated.",
                        type: "error"
                    });
                }
            }).fail(Notification.exception);
        });
    }

    // --- Approve / Reject ---
    document.querySelectorAll(".action-btn").forEach(btn => {
        btn.addEventListener("click", e => {
            e.preventDefault();

            const action = btn.dataset.action;
            const token = btn.dataset.token;

            Ajax.call([{
                methodname: "local_forum_ai_approve_response",
                args: { token: token, action: action },
            }])[0].done(response => {
                if (response.success) {
                    Notification.addNotification({
                        message: action === "approve"
                            ? "AI response approved and successfully published."
                            : "AI response rejected.",
                        type: "success"
                    });

                    setTimeout(() => {
                        window.location.href = M.cfg.wwwroot + "/mod/forum/discuss.php?d=" + btn.dataset.discussionid;
                    }, 1500);
                } else {
                    Notification.addNotification({
                        message: "The action could not be processed.",
                        type: "error"
                    });
                }
            }).fail(Notification.exception);
        });
    });
};
