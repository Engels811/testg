setInterval(async () => {
    const res = await fetch('/forum/online-users');
    const online = await res.json();

    document.querySelectorAll('.forum-user-status').forEach(el => {
        const uid = el.dataset.user;
        el.classList.toggle('online', online.includes(uid));
        el.classList.toggle('offline', !online.includes(uid));
    });
}, 30000);
