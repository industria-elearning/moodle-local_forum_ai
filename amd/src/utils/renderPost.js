/**
 * Renderiza un post individual con su nivel de indentación correspondiente
 *
 * @param {Object} post Datos del post
 * @returns {string} HTML del post
 */
export function renderPost(post) {
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
