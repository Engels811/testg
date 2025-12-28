document.addEventListener("DOMContentLoaded", () => {

    const list = document.getElementById("hardwareList");
    const saveBtn = document.getElementById("saveHwOrder");
    if (!list || !saveBtn) return;

    let dragged = null;

    list.querySelectorAll("li").forEach(li => {

        li.addEventListener("dragstart", () => {
            dragged = li;
            li.classList.add("dragging");
        });

        li.addEventListener("dragend", () => {
            dragged = null;
            li.classList.remove("dragging");
        });

        li.addEventListener("dragover", (e) => {
            e.preventDefault();
        });

        li.addEventListener("drop", (e) => {
            e.preventDefault();
            if (!dragged || dragged === li) return;

            const rect = li.getBoundingClientRect();
            const after = (e.clientY - rect.top) > rect.height / 2;

            list.insertBefore(dragged, after ? li.nextSibling : li);
        });
    });

    saveBtn.addEventListener("click", () => {
        const order = [...list.children].map((li, idx) => ({
            id: li.dataset.id,
            sort: idx + 1
        }));

        fetch("/admin/hardware/save-order", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ order })
        })
        .then(res => {
            if (!res.ok) throw new Error("save failed");
            return res.text();
        })
        .then(() => location.reload())
        .catch(() => alert("Reihenfolge konnte nicht gespeichert werden."));
    });

});
