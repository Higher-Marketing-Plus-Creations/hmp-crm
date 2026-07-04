(function () {
    const script = document.currentScript;
    let container = null;

    if (script && script.getAttribute('data-container')) {
        container = document.querySelector(script.getAttribute('data-container'));
    }

    if (!container) {
        container = document.createElement('div');
        container.setAttribute('class', 'hmp-post-widget');
        (document.body || document.documentElement).appendChild(container);
    }

    const websiteId = script && script.getAttribute('data-website-id') ? script.getAttribute('data-website-id') : '{{ $websiteId ?? '' }}';
    const endpoint = script && script.getAttribute('data-endpoint') ? script.getAttribute('data-endpoint') : '/api/posts/widget?website_id=' + encodeURIComponent(websiteId);

    if (!websiteId) {
        return;
    }

    fetch(endpoint)
        .then(function (response) { return response.json(); })
        .then(function (payload) {
            const posts = Array.isArray(payload.posts) ? payload.posts : [];
            if (!posts.length) {
                container.innerHTML = '<p>No posts available yet.</p>';
                return;
            }

            container.innerHTML = posts.map(function (post) {
                const image = post.feature_image ? '<img src="' + post.feature_image + '" alt="' + (post.title || '') + '" style="max-width:100%;height:auto;border-radius:8px;">' : '';
                return '<article style="margin-bottom:16px;padding:16px;border:1px solid #e5e7eb;border-radius:12px;background:#fff;">' + image + '<h3 style="margin:8px 0 6px;font-size:18px;">' + (post.title || '') + '</h3><p style="color:#6b7280;">' + (post.category || '') + '</p><div>' + (post.content || '') + '</div></article>';
            }).join('');
        });
})();
