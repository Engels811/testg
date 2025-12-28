document.addEventListener("DOMContentLoaded", () => {

    const form = document.getElementById("hardwareForm");
    const list = document.getElementById("hardwareList");

    const icon = document.getElementById("hw-icon");
    const title = document.getElementById("hw-title");
    const name = document.getElementById("hw-name");
    const details = document.getElementById("hw-details");
    const category = document.getElementById("hw-category");
    const id = document.getElementById("hw-id");

    /* =====================================================
       AUTO ICON – CATEGORY BASED
    ===================================================== */

    const categoryIcons = {
        pc: "🖥️",

        // CORE
        gpu: "🎮",
        cpu: "⚡",
        ram: "💾",
        storage: "🗄️",
        psu: "🔌",

        // COOLING
        cooling: "❄️",
        watercooling: "💧",
        aio: "🌊",
        aircooling: "🌬️",

        // PERIPHERALS
        monitors: "🖥️",
        audio: "🎙️",
        microphone: "🎤",
        headset: "🎧",

        // CAMERA & LIGHT
        camera_lighting: "📷",
        camera: "📸",
        lighting: "💡",

        // STREAMING
        capture: "📡",
        streamdeck: "🎛️",

        // EXTRAS
        chair: "🪑",
        desk: "🪵",
        case: "🧱",
        extras: "✨"
    };

    /* =====================================================
       SMART ICON DETECTION (TITLE / NAME)
    ===================================================== */

    function detectIconByText(text) {
        text = text.toLowerCase();

        if (text.includes("gpu") || text.includes("rtx") || text.includes("radeon")) return "🎮";
        if (text.includes("cpu") || text.includes("ryzen") || text.includes("intel")) return "⚡";
        if (text.includes("ram") || text.includes("ddr")) return "💾";
        if (text.includes("ssd") || text.includes("nvme") || text.includes("hdd")) return "🗄️";

        if (text.includes("aio") || text.includes("wasserkühl")) return "💧";
        if (text.includes("lüfter") || text.includes("air")) return "🌬️";

        if (text.includes("netzteil") || text.includes("psu")) return "🔌";

        if (text.includes("monitor")) return "🖥️";
        if (text.includes("mikro") || text.includes("microphone")) return "🎤";
        if (text.includes("headset")) return "🎧";
        if (text.includes("kamera")) return "📷";
        if (text.includes("licht") || text.includes("light")) return "💡";

        if (text.includes("stuhl") || text.includes("chair")) return "🪑";
        if (text.includes("gehäuse") || text.includes("case")) return "🧱";

        return null;
    }

    /* =====================================================
       EVENTS
    ===================================================== */

    // Kategorie geändert
    category.addEventListener("change", () => {
        if (!icon.value) {
            icon.value = categoryIcons[category.value] || "";
        }
    });

    // Titel / Name geändert (smarter)
    [title, name].forEach(field => {
        field.addEventListener("blur", () => {
            if (!icon.value) {
                const detected =
                    detectIconByText(title.value) ||
                    detectIconByText(name.value);

                if (detected) {
                    icon.value = detected;
                }
            }
        });
    });

    /* =====================================================
       EDIT MODE (CLICK LIST)
    ===================================================== */

    list.querySelectorAll("li").forEach(li => {
        li.addEventListener("click", () => {

            id.value = li.dataset.id;
            icon.value = li.dataset.icon;
            title.value = li.dataset.title;
            name.value = li.dataset.name;
            details.value = li.dataset.details;
            category.value = li.dataset.category;

            form.scrollIntoView({
                behavior: "smooth",
                block: "start"
            });
        });
    });

});
