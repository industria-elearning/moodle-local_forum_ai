import ModalFactory from 'core/modal_factory';
import Ajax from 'core/ajax';
import Notification from 'core/notification';
import { renderPost } from './utils/renderPost';

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
            }])[0].done(data => {
                ModalFactory.create({
                    type: ModalFactory.types.DEFAULT,
                    title: 'Detalles del debate',
                    body: renderDiscussion(data, true),
                    large: true,
                }).done(modal => {
                    modal.show();

                    // Inicializar handlers
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
 * @returns {string} HTML
 */
function renderDiscussion(data, editMode = false) {
    let html = `<h4>${data.course} / ${data.forum}</h4>
                <h5>Debate: ${data.discussion}</h5>`;

    if (data.posts.length === 0) {
        html += '<p class="text-warning">No se encontraron posts en este debate.</p>';
    } else {
        data.posts.forEach((post) => {
            html += renderPost(post);
        });
    }

    html += `<div class="alert alert-primary mt-4">
               <h5><i class="fa-solid fa-robot"></i> Respuesta AI propuesta</h5>
               <div id="airesponse-content" data-token="${data.token}">`;

    if (editMode) {
        // Mostrar directamente en modo edición
        html += `
            <textarea id="airesponse-edit" class="form-control" rows="5">${data.airesponse}</textarea>
            <div class="mt-2">
                <button class="btn btn-success btn-sm save-ai" data-token="${data.token}">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar
                </button>
                <button class="btn btn-primary btn-sm save-approve-ai" data-token="${data.token}">
                    <i class="fa-solid fa-check"></i> Guardar y Aprobar
                </button>
                <button class="btn btn-danger btn-sm reject-ai" data-token="${data.token}">
                    <i class="fa-solid fa-times"></i> Rechazar
                </button>
            </div>
        `;
    } else {
        // Vista normal
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
    // Guardar cambios
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

    // Guardar y aprobar
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

    // Rechazar
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
