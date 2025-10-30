import ModalFactory from 'core/modal_factory';
import Ajax from 'core/ajax';
import Notification from 'core/notification';
import { get_string as getString } from 'core/str';
import { renderPost } from './utils/renderPost';

/**
 * Initializes the listeners to display AI response history.
 */
export const init = () => {
    document.querySelectorAll('.view-details').forEach(btn => {
        btn.addEventListener('click', e => {
            const token = e.currentTarget.dataset.token;

            Ajax.call([{
                methodname: 'local_forum_ai_get_details',
                args: { token: token },
            }])[0].done(async data => {
                const [
                    modalTitle,
                    discussionLabel,
                    noPosts,
                    aiResponse,
                    aiResponseApproved,
                    aiResponseRejected
                ] = await Promise.all([
                    getString('modal_title', 'local_forum_ai'),
                    getString('discussion_label', 'local_forum_ai', data.discussion),
                    getString('no_posts', 'local_forum_ai'),
                    getString('ai_response', 'local_forum_ai'),
                    getString('ai_response_approved', 'local_forum_ai'),
                    getString('ai_response_rejected', 'local_forum_ai'),
                ]);

                ModalFactory.create({
                    type: ModalFactory.types.DEFAULT,
                    title: modalTitle,
                    body: renderDiscussion(data, {
                        discussionLabel,
                        noPosts,
                        aiResponse,
                        aiResponseApproved,
                        aiResponseRejected
                    }),
                    large: true,
                }).done(modal => {
                    modal.show();
                });
            }).fail(Notification.exception);
        });
    });
};

/**
 * Builds the HTML for the history modal.
 *
 * @param {Object} data Data received from the AJAX service
 * @param {Object} strings Translated strings
 * @returns {Promise<string>} HTML
 */
async function renderDiscussion(data, strings) {
    let html = `<h4>${data.course} / ${data.forum}</h4>
                <h5>${strings.discussionLabel}</h5>`;

    if (data.posts.length === 0) {
        html += `<p class="text-warning">${strings.noPosts}</p>`;
    } else {
        for (const post of data.posts) {
            html += await renderPost(post);
        }
    }

    let statusClass = 'bg-secondary';
    let statusLabel = `<i class="fa fa-robot mr-2"></i> ${strings.aiResponse}`;

    if (data.status === 'approved') {
        statusClass = 'bg-success text-white';
        statusLabel = `<i class="fa fa-check mr-2"></i> ${strings.aiResponseApproved}`;
    } else if (data.status === 'rejected') {
        statusClass = 'bg-danger text-white';
        statusLabel = `<i class="fa fa-times mr-2"></i> ${strings.aiResponseRejected}`;
    }

    html += `<div class="alert mt-4 ${statusClass}">
                <h5>${statusLabel}</h5>
                ${data.airesponse}
             </div>`;

    return html;
}
