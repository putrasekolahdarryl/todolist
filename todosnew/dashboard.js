document.addEventListener("DOMContentLoaded", () => {
  // === Fungsi Modular ===
  const getTaskId = (el) => el.getAttribute("data-id");

  const fetchAndReload = (url, errorMessage) => {
    fetch(url)
      .then((res) => {
        if (res.ok) {
          location.reload();
        } else {
          alert(errorMessage);
        }
      })
      .catch(() => alert("Terjadi kesalahan koneksi."));
  };

  // === Event: Edit Task ===
  document.querySelectorAll(".edit-btn").forEach((button) => {
    button.addEventListener("click", () => {
      const id = getTaskId(button);
      if (id) window.location.href = `edit.php?id=${id}`;
    });
  });

  // === Event: Complete Task ===
  document.querySelectorAll(".complete-btn").forEach((button) => {
    button.addEventListener("click", () => {
      const id = getTaskId(button);
      if (id) fetchAndReload(`complete_task.php?id=${id}`, "Gagal menyelesaikan tugas.");
    });
  });

  // === Event: Delete Task ===
  document.querySelectorAll(".delete-btn").forEach((button) => {
    button.addEventListener("click", () => {
      const id = getTaskId(button);
      if (id && confirm("Hapus tugas ini?")) {
        fetchAndReload(`delete_task.php?id=${id}`, "Gagal menghapus tugas.");
      }
    });
  });

  // === Countdown Timer ===
  const updateCountdowns = () => {
    const now = Date.now();

    document.querySelectorAll(".countdown").forEach((el) => {
      const deadlineAttr = el.getAttribute("data-deadline");
      if (!deadlineAttr) {
        el.textContent = "⚠️ Tidak ada deadline";
        return;
      }

      const deadline = new Date(deadlineAttr).getTime();
      if (isNaN(deadline)) {
        el.textContent = "❌ Tanggal tidak valid";
        el.classList.add("expired");
        return;
      }

      const remaining = deadline - now;

      if (remaining <= 0) {
        el.textContent = "⏰ Waktu habis!";
        el.classList.add("expired");
      } else {
        const days = Math.floor(remaining / (1000 * 60 * 60 * 24));
        const hours = Math.floor((remaining / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((remaining / (1000 * 60)) % 60);
        const seconds = Math.floor((remaining / 1000) % 60);

        const parts = [];
        if (days > 0) parts.push(`${days}d`);
        parts.push(`${hours}h`, `${minutes}m`, `${seconds}s`);
        el.textContent = parts.join(" ");
      }
    });
  };

  updateCountdowns(); // initial
  setInterval(updateCountdowns, 1000); // update tiap detik
});
