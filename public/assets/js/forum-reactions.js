document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const postId = btn.dataset.post;

        const res = await fetch('/forum/post/like', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ post_id: postId })
        });

        const data = await res.json();
        btn.querySelector('span').innerText = data.likes;
        btn.classList.toggle('active', data.liked);
    });
});
