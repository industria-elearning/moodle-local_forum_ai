import ModalFactory from 'core/modal_factory';
import Ajax from 'core/ajax';
import Notification from 'core/notification';
import { renderPost } from './utils/renderPost';

/**
 * Inicializa los listeners para mostrar historial de respuestas AI.
 */
export const init = () => {
    document.querySelectorAll('.view-details').forEach(btn => {
        btn.addEventListener('click', e => {
            const token = e.currentTarget.dataset.token;

            Ajax.call([{
                methodname: 'local_forum_ai_get_details',
                args: { token: token },
            }])[0].done(data => {
                ModalFactory.create({
                    type: ModalFactory.types.DEFAULT,
                    title: 'Detalles del historial de debate',
                    body: renderDiscussion(data),
                    large: true,
                }).done(modal => {
                    modal.show();
                });
            }).fail(Notification.exception);
        });
    });
};

/**
 * Construye el HTML para el modal de historial.
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
        data.posts.forEach(post => {
            html += renderPost(post);
        });
    }

    // 🎨 Fondo según estado
    let statusClass = 'bg-secondary';
    let statusLabel = '<i class="fa fa-robot mr-2"></i> Respuesta AI';
    if (data.status === 'approved') {
        statusClass = 'bg-success text-white';
        statusLabel = '<i class="fa fa-check mr-2"></i> Respuesta AI (Aprobada)';
    } else if (data.status === 'rejected') {
        statusClass = 'bg-danger text-white';
        statusLabel = '<i class="fa fa-times mr-2"></i> Respuesta AI (Rechazada)';
    }

    html += `<div class="alert mt-4 ${statusClass}">
                <h5>${statusLabel}</h5>
                ${data.airesponse}
             </div>`;

    return html;
}
