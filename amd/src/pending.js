
import ModalFactory from 'core/modal_factory';
import Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * Inicializa los listeners para mostrar detalles de respuestas AI.
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
                    title: 'Detalles del debate',
                    body: renderDiscussion(data),
                    large: true, // Hacer el modal más grande para mejor visualización
                }).done(modal => {
                    modal.show();
                });
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
               <h5>Respuesta AI propuesta</h5>
               ${data.airesponse}
             </div>`;
    return html;
}

/**
 * Renderiza un post individual con su nivel de indentación correspondiente
 *
 * @param {Object} post Datos del post
 * @returns {string} HTML del post
 */
function renderPost(post) {
    // Calcular la indentación basada en el nivel
    const indentationLevel = post.level;
    const marginLeft = indentationLevel * 30; // 30px por cada nivel

    // Diferentes estilos de borde para diferentes niveles
    let borderClass = 'border-left-primary';
    if (indentationLevel === 1) {
        borderClass = 'border-left-info';
    } else if (indentationLevel === 2) {
        borderClass = 'border-left-success';
    } else if (indentationLevel >= 3) {
        borderClass = 'border-left-warning';
    }

    // Indicador visual del nivel de respuesta
    let levelIndicator = '';
    if (indentationLevel > 0) {
        levelIndicator = `<span class="badge badge-secondary mb-2">
            ${'↳ '.repeat(indentationLevel)}Respuesta nivel ${indentationLevel}
        </span><br>`;
    }

    return `
        <div class="mb-3 p-3 border ${borderClass}" style="margin-left: ${marginLeft}px; border-left-width: 4px !important;">
            ${levelIndicator}
            <div class="d-flex justify-content-between align-items-start mb-2">
                <strong class="text-primary">${post.subject}</strong>
                <small class="text-muted">Nivel: ${indentationLevel}</small>
            </div>
            <div class="mb-2">
                <em class="text-info">${post.author}</em>
                <small class="text-muted ml-2">(${post.created})</small>
            </div>
            <div class="post-content">
                ${post.message}
            </div>
        </div>`;
}
