
import ModalFactory from 'core/modal_factory';
import Ajax from 'core/ajax';
import Notification from 'core/notification';
import { renderPost } from './utils/renderPost';

/**
 * Inicializa los listeners para mostrar detalles de respuestas AI.
 */
export const init = () => {
    // Botón de ver detalles (ya lo tenías)
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
                // Quitar la fila de la tabla
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
 * @param {Object} data Datos recibidos del servicio AJAX
 * @returns {string} HTML
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
               <h5><i class="fa-solid fa-robot"></i>Respuesta AI propuesta 
                   <button class="btn btn-sm btn-link edit-ai" data-token="${data.token}">
                     <i class="fa fa-pencil"></i>
                   </button>
               </h5>
               <div id="airesponse-content" data-token="${data.token}">${data.airesponse}</div>
             </div>`;
    return html;
}

// Delegamos evento para edición
document.addEventListener('click', e => {
    if (e.target.closest('.edit-ai')) {
        const btn = e.target.closest('.edit-ai');
        const token = btn.dataset.token;
        const container = document.getElementById('airesponse-content');
        const currentText = container.innerHTML;

        // Reemplazar contenido por textarea y botón guardar
        container.innerHTML = `
            <textarea id="airesponse-edit" class="form-control" rows="5">${currentText}</textarea>
            <button class="btn btn-success btn-sm mt-2 save-ai" data-token="${token}">
              <i class="fa-solid fa-floppy-disk"></i>
            </button>
        `;
    }

    if (e.target.closest('.save-ai')) {
        const btn = e.target.closest('.save-ai');
        const token = btn.dataset.token;
        const newMessage = document.getElementById('airesponse-edit').value;

        Ajax.call([{
            methodname: 'local_forum_ai_update_response',
            args: { token: token, message: newMessage },
        }])[0].done(response => {
            const container = document.getElementById('airesponse-content');
            container.innerHTML = response.message;
            // Opcional: recargar página para ver en tabla
            location.reload();
        }).fail(Notification.exception);
    }
});

