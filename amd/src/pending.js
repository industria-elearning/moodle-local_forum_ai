import ModalFactory from 'core/modal_factory';
import Ajax from 'core/ajax';
import Notification from 'core/notification';
import { renderPost } from './utils/renderPost';
import { get_string as getString } from 'core/str';

/**
 * Initializes the listeners to display AI response details.
 */
export const init = () => {
    // View details button
    document.querySelectorAll('.view-details').forEach(btn => {
        btn.addEventListener('click', e => {
            const token = e.currentTarget.dataset.token;

            Ajax.call([{
                methodname: 'local_forum_ai_get_details',
                args: { token: token },
            }])[0].done(async data => {
                // Preload strings
                const [
                    modalTitle,
                    discussionLabel,
                    noPosts,
                    aiResponseProposed,
                    saveLabel,
                    saveApproveLabel,
                    rejectLabel
                ] = await Promise.all([
                    getString('modal_title_pending', 'local_forum_ai'),
                    getString('discussion_label', 'local_forum_ai', data.discussion),
                    getString('no_posts', 'local_forum_ai'),
                    getString('ai_response_proposed', 'local_forum_ai'),
                    getString('save', 'local_forum_ai'),
                    getString('saveapprove', 'local_forum_ai'),
                    getString('reject', 'local_forum_ai'),
                ]);

                ModalFactory.create({
                    type: ModalFactory.types.DEFAULT,
                    title: modalTitle,
                    body: renderDiscussion(data, true, {
                        discussionLabel,
                        noPosts,
                        aiResponseProposed,
                        saveLabel,
                        saveApproveLabel,
                        rejectLabel
                    }),
                    large: true,
                }).done(modal => {
                    modal.show();
                    initAiEditHandlers(modal.getRoot(), data.token);
                });
            }).fail(Notification.exception);
        });
    });

    // Approve/reject buttons from the table
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault();
            const token = btn.dataset.token;
            const action = btn.dataset.action;

            Ajax.call([{
                methodname: 'local_forum_ai_approve_response',
                args: { token: token, action: action },
            }])[0].done(() => {
                const row = btn.closest('tr');
                if (row) {
                    row.remove();
                }
            }).fail(Notification.exception);
        });
    });
};

/**
 * Builds the HTML for the details modal.
 *
 * @param {Object} data Data received from the AJAX service.
 * @param {boolean} editMode Whether it should start directly in edit mode.
 * @param {Object} strings Set of translated strings
 * @param {string} strings.discussionLabel Text for the discussion title
 * @param {string} strings.noPosts Text for when there are no posts
 * @param {string} strings.aiResponseProposed Text for the proposed AI response
 * @param {string} strings.saveLabel Text for the Save button
 * @param {string} strings.saveApproveLabel Text for the Save and Approve button
 * @param {string} strings.rejectLabel Text for the Reject button
 * @returns {Promise<string>} Generated HTML
 */
async function renderDiscussion(data, editMode = false, strings) {
    let html = `<h4>${data.course} / ${data.forum}</h4>
                <h5>${strings.discussionLabel}</h5>`;

    if (data.posts.length === 0) {
        html += `<p class="text-warning">${strings.noPosts}</p>`;
    } else {
        for (const post of data.posts) {
            html += await renderPost(post);
        }
    }

    html += `<div class="alert alert-primary mt-4">
               <h5><i class="fa-solid fa-robot"></i> ${strings.aiResponseProposed}</h5>
               <div id="airesponse-content" data-token="${data.token}">`;

    if (editMode) {
        html += `
            <textarea id="airesponse-edit" class="form-control" rows="5">${data.airesponse}</textarea>
            <div class="mt-2">
                <button class="btn btn-success btn-sm save-ai" data-token="${data.token}">
                    <i class="fa-solid fa-floppy-disk"></i> ${strings.saveLabel}
                </button>
                <button class="btn btn-primary btn-sm save-approve-ai" data-token="${data.token}">
                    <i class="fa-solid fa-check"></i> ${strings.saveApproveLabel}
                </button>
                <button class="btn btn-danger btn-sm reject-ai" data-token="${data.token}">
                    <i class="fa-solid fa-times"></i> ${strings.rejectLabel}
                </button>
            </div>
        `;
    } else {
        html += data.airesponse;
    }

    html += `</div></div>`;
    return html;
}

/**
 * Initializes the handlers to edit/save inside a specific modal.
 *
 * @param {object} root The root container of the modal.
 * @param {string} token Unique approval token associated.
 */
function initAiEditHandlers(root, token) {
    root.on('click', '.save-ai', e => {
        e.preventDefault();
        const newMessage = root.find('#airesponse-edit').val();

        Ajax.call([{
            methodname: 'local_forum_ai_update_response',
            args: { token: token, message: newMessage },
        }])[0].done(response => {
            root.find('#airesponse-content').html(response.message);
            location.reload();
        }).fail(Notification.exception);
    });

    root.on('click', '.save-approve-ai', e => {
        e.preventDefault();
        const newMessage = root.find('#airesponse-edit').val();

        Ajax.call([{
            methodname: 'local_forum_ai_update_response',
            args: { token: token, message: newMessage },
        }])[0].done(() => {
            Ajax.call([{
                methodname: 'local_forum_ai_approve_response',
                args: { token: token, action: 'approve' },
            }])[0].done(() => {
                location.reload();
            }).fail(Notification.exception);
        }).fail(Notification.exception);
    });

    root.on('click', '.reject-ai', e => {
        e.preventDefault();

        Ajax.call([{
            methodname: 'local_forum_ai_approve_response',
            args: { token: token, action: 'reject' },
        }])[0].done(() => {
            location.reload();
        }).fail(Notification.exception);
    });
}
