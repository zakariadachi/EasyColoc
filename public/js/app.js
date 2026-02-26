document.addEventListener("DOMContentLoaded", () => {
  const sidebarToggle = document.getElementById("sidebarToggle");
  const sidebar = document.getElementById("sidebar");
  const sidebarOverlay = document.getElementById("sidebarOverlay");

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener("click", () => {
      sidebar.classList.toggle("-translate-x-full");
      if (sidebarOverlay) sidebarOverlay.classList.toggle("hidden");
    });
  }

  if (sidebarOverlay) {
    sidebarOverlay.addEventListener("click", () => {
      sidebar.classList.add("-translate-x-full");
      sidebarOverlay.classList.add("hidden");
    });
  }

  const modalOpeners = document.querySelectorAll("[data-modal-target]");
  const modalClosers = document.querySelectorAll("[data-modal-close]");

  modalOpeners.forEach((btn) => {
    btn.addEventListener("click", () => {
      const targetId = btn.getAttribute("data-modal-target");
      const modal = document.getElementById(targetId);
      if (modal) {
        modal.classList.remove("hidden");
        modal.classList.add("flex");
        document.body.style.overflow = "hidden";
      }
    });
  });

  modalClosers.forEach((btn) => {
    btn.addEventListener("click", () => {
      const modal = btn.closest(".modal-container");
      if (modal) {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
        document.body.style.overflow = "auto";
      }
    });
  });

  document.addEventListener("click", (e) => {
    if (e.target.classList.contains("modal-container")) {
      e.target.classList.add("hidden");
      e.target.classList.remove("flex");
      document.body.style.overflow = "auto";
    }
  });
});
