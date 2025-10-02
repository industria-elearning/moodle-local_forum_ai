import ModalFactory from 'core/modal_factory';
import Ajax from 'core/ajax';
import Notification from 'core/notification';
import { renderPost } from './utils/renderPost';
import { get_string as getString } from 'core/str';

/**
 * Inicializa los listeners para mostrar detalles de respuestas AI.
 */
export const init = () => {
    // Botón de ver detalles
    document.querySelectorAll('.view-details').forEach(btn => {
        btn.addEventListener('click', e => {
            const token = e.currentTarget.dataset.token;

            Ajax.call([{
                methodname: 'local_forum_ai_get_details',
                args: { token: token },
            }])[0].done(async data => {
                // Precargar cadenas
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

    // Botones de aprobar/rechazar desde la tabla
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
 * Construye el HTML para el modal de detalles.
 *
 * @param {Object} data Datos recibidos del servicio AJAX.
 * @param {boolean} editMode Si debe iniciar directamente en modo edición.
 * @param {Object} strings Conjunto de cadenas traducidas
 * @param {string} strings.discussionLabel Texto para el título del debate
 * @param {string} strings.noPosts Texto para cuando no hay posts
 * @param {string} strings.aiResponseProposed Texto para la respuesta AI propuesta
 * @param {string} strings.saveLabel Texto para el botón Guardar
 * @param {string} strings.saveApproveLabel Texto para el botón Guardar y Aprobar
 * @param {string} strings.rejectLabel Texto para el botón Rechazar
 * @returns {Promise<string>} HTML generado
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
 * Inicializa los handlers para editar/guardar dentro de un modal específico.
 *
 * @param {object} root El contenedor raíz del modal.
 * @param {string} token Token único de aprobación asociado.
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
