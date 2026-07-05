(function () {

    const script = document.currentScript;
    let container = null;

    // Custom container
    if (script && script.getAttribute('data-container')) {
        container = document.querySelector(script.getAttribute('data-container'));
    }

    // Default container
    if (!container) {
        container = document.createElement('div');
        container.className = 'hmp-post-widget';
        (document.body || document.documentElement).appendChild(container);
    }

    // API Key (Blade se aayegi)
    const apiKey = "{{ $apiKey ?? '' }}";

    if (!apiKey) {
        container.innerHTML = '<p>API Key is missing.</p>';
        return;
    }

    // API Endpoint
    const endpoint = "{{ url('/api/posts/widget') }}?api_key=" + encodeURIComponent(apiKey);

    fetch(endpoint)
        .then(function (response) {

            if (!response.ok) {
                throw new Error('Failed to load posts.');
            }

            return response.json();
        })
        .then(function (payload) {

            const posts = Array.isArray(payload.posts) ? payload.posts : [];

            if (!posts.length) {
                container.innerHTML = '<p>No posts available.</p>';
                return;
            }

            let html = '';

            posts.forEach(function (post) {

                html += `
                    <article style="
                        margin-bottom:20px;
                        padding:20px;
                        border:1px solid #e5e7eb;
                        border-radius:12px;
                        background:#ffffff;
                        box-shadow:0 2px 10px rgba(0,0,0,.05);
                    ">
                `;

                if (post.feature_image) {
                    html += `
                        <img
                            src="${post.feature_image}"
                            alt="${post.title}"
                            style="
                                width:25px;
                                height:25px;
                                border-radius:8px;
                                margin-bottom:12px;
                            ">
                    `;
                }

                html += `
                        <h3 style="
                            margin:0 0 10px;
                            font-size:22px;
                            font-weight:bold;
                        ">
                            ${post.title ?? ''}
                        </h3>

                        <p style="
                            color:#6b7280;
                            margin-bottom:12px;
                            font-size:14px;
                        ">
                            ${post.category ?? ''}
                        </p>

                        <div>
                            ${post.content ?? ''}
                        </div>

                    </article>
                `;

            });

            container.innerHTML = html;

        })
        .catch(function (error) {

            console.error(error);

            container.innerHTML = `
                <p style="color:red;">
                    Unable to load posts.
                </p>
            `;

        });

})();