import { get_string as getString } from 'core/str';

let replyTemplateCache = null;
let levelTemplateCache = null;

/**
 * Renders an individual post with its corresponding indentation level
 *
 * @param {Object} post Post data
 * @returns {Promise<string>} Post HTML (async because it loads translations if not already cached)
 */
export async function renderPost(post) {
    // Preload templates only once
    if (!replyTemplateCache || !levelTemplateCache) {
        [replyTemplateCache, levelTemplateCache] = await Promise.all([
            getString('replylevel', 'local_forum_ai'),
            getString('level', 'local_forum_ai'),
        ]);
    }

    const indentationLevel = post.level;
    const marginLeft = indentationLevel * 30;

    let borderClass = 'border-left-primary';
    if (indentationLevel === 1) {
        borderClass = 'border-left-info';
    } else if (indentationLevel === 2) {
        borderClass = 'border-left-success';
    } else if (indentationLevel >= 3) {
        borderClass = 'border-left-warning';
    }

    let levelIndicator = '';
    if (indentationLevel > 0) {
        const replyText = replyTemplateCache.replace('{$a}', indentationLevel);
        levelIndicator = `<span class="badge badge-secondary mb-2">
            ${'↳ '.repeat(indentationLevel)}${replyText}
        </span><br>`;
    }

    const levelText = levelTemplateCache.replace('{$a}', indentationLevel);

    return `
        <div class="mb-3 p-3 border ${borderClass}"
             style="margin-left: ${marginLeft}px; border-left-width: 4px !important;">
            ${levelIndicator}
            <div class="d-flex justify-content-between align-items-start mb-2">
                <strong class="text-primary">${post.subject}</strong>
                <small class="text-muted">${levelText}</small>
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
