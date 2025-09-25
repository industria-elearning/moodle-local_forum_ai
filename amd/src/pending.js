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
                    body: renderDiscussion(data),
                    large: true,
                }).done(modal => {
                    modal.show();

                    // Reasignar eventos para este modal
                    initAiEditHandlers(modal.getRoot(), data.token);
                });
            }).fail(Notification.exception);
        });
    });

    // Botones de aprobar/rechazar
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
 * @param {Object} data - Datos recibidos del servicio AJAX.
 * @param {string} data.course - Nombre del curso.
 * @param {string} data.forum - Nombre del foro.
 * @param {string} data.discussion - Título del debate.
 * @param {Array<Object>} data.posts - Lista de posts del debate.
 * @param {string} data.airesponse - Respuesta AI propuesta.
 * @param {string} data.token - Token único de aprobación asociado.
 * @returns {string} HTML a renderizar dentro del modal.
 */
function renderDiscussion(data) {
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
               <h5><i class="fa-solid fa-robot"></i> Respuesta AI propuesta
                   <button class="btn btn-sm btn-link edit-ai" data-token="${data.token}">
                     <i class="fa fa-pencil"></i>
                   </button>
               </h5>
               <div id="airesponse-content" data-token="${data.token}">${data.airesponse}</div>
             </div>`;
    return html;
}


/**
 * Inicializa los handlers para editar/guardar dentro de un modal específico.
 *
 * @param {object} root - El contenedor raíz del modal (jQuery-wrapped).
 * @param {string} token - El token único de aprobación asociado a la respuesta AI.
 */
function initAiEditHandlers(root, token) {
    // Editar mensaje
    root.on('click', '.edit-ai', e => {
        e.preventDefault();
        const content = root.find('#airesponse-content');
        const currentText = content.html().trim();

        content.html(`
            <textarea id="airesponse-edit" class="form-control" rows="5">${currentText}</textarea>
            <button class="btn btn-success btn-sm mt-2 save-ai" data-token="${token}">
              <i class="fa-solid fa-floppy-disk"></i> Guardar
            </button>
        `);
    });

    // Guardar cambios
    root.on('click', '.save-ai', e => {
        e.preventDefault();
        const newMessage = root.find('#airesponse-edit').val();

        Ajax.call([{
            methodname: 'local_forum_ai_update_response',
            args: { token: token, message: newMessage },
        }])[0].done(response => {
            root.find('#airesponse-content').html(response.message);

            // Opcional: refrescar la tabla completa
            location.reload();
        }).fail(Notification.exception);
    });
}
