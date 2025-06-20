// JavaScript untuk Halaman Pilih Kursi

// Tunggu sampai DOM siap
document.addEventListener("DOMContentLoaded", function () {
  const selectedSeats = new Set();
  const maxSeats = 4;
  const serviceFee = 5000;

  const seats = document.querySelectorAll(".seat.available");
  const selectedSeatsList = document.getElementById("selected-seats-list");
  const emptyMessage = document.getElementById("empty-message");
  const summary = document.getElementById("summary");
  const seatCount = document.getElementById("seat-count");
  const subtotal = document.getElementById("subtotal");
  const total = document.getElementById("total");
  const notification = document.getElementById("notification");
  const notificationText = document.getElementById("notification-text");
  const continueButton = document.querySelector(
    ".action-buttons .btn-outline:last-child"
  );

  updateUI();

  seats.forEach((seat) => {
    seat.addEventListener("click", function () {
      const seatId = this.dataset.seat;
      const seatPrice = parseInt(this.dataset.price);

      if (selectedSeats.has(seatId)) {
        selectedSeats.delete(seatId);
        this.classList.remove("selected");
        this.classList.add("available");
        showNotification(`Kursi ${seatId} dibatalkan`, "warning");
      } else {
        if (selectedSeats.size >= maxSeats) {
          showNotification(`Maksimal ${maxSeats} kursi per transaksi`, "error");
          return;
        }

        selectedSeats.add(seatId);
        this.classList.remove("available");
        this.classList.add("selected");
        addSeatSelectionEffect(this);
        showNotification(`Kursi ${seatId} dipilih`, "success");
      }
      updateUI();
    });
  });

  function updateUI() {
    updateSelectedSeatsList();
    updateSummary();
    updateContinueButton();
  }

  function updateSelectedSeatsList() {
    selectedSeatsList.innerHTML = "";

    if (selectedSeats.size === 0) {
      emptyMessage.style.display = "block";
      summary.style.display = "none";
    } else {
      emptyMessage.style.display = "none";
      summary.style.display = "block";

      selectedSeats.forEach((seatId) => {
        const seatElement = document.querySelector(`[data-seat="${seatId}"]`);
        const seatPrice = parseInt(seatElement.dataset.price);
        selectedSeatsList.appendChild(createSeatItem(seatId, seatPrice));
      });
    }
  }

  function createSeatItem(seatId, seatPrice) {
    const seatItem = document.createElement("div");
    seatItem.className = "selected-seat-item";

    seatItem.innerHTML = `
            <div class="seat-info">
                <div class="seat-number">${seatId}</div>
                <div class="seat-details">
                    <div>Kursi ${seatId}</div>
                    <div class="seat-price">Rp ${formatPrice(seatPrice)}</div>
                </div>
            </div>
            <button class="remove-seat" data-seat-id="${seatId}">
                <i class="fas fa-times"></i>
            </button>
        `;

    seatItem.querySelector(".remove-seat").addEventListener("click", () => {
      removeSeat(seatId);
    });

    return seatItem;
  }

  function removeSeat(seatId) {
    selectedSeats.delete(seatId);
    const seatElement = document.querySelector(`[data-seat="${seatId}"]`);
    seatElement.classList.remove("selected");
    seatElement.classList.add("available");
    showNotification(`Kursi ${seatId} dihapus`, "warning");
    updateUI();
  }

  function updateSummary() {
    const count = selectedSeats.size;
    let subtotalAmount = calculateSubtotal();
    const totalAmount = subtotalAmount + serviceFee;

    seatCount.textContent = count;
    subtotal.textContent = `Rp ${formatPrice(subtotalAmount)}`;
    total.textContent = `Rp ${formatPrice(totalAmount)}`;
  }

  function updateContinueButton() {
    if (selectedSeats.size > 0) {
      continueButton.style.opacity = "1";
      continueButton.style.pointerEvents = "auto";
      continueButton.classList.remove("btn-outline");
      continueButton.classList.add("btn-primary");
    } else {
      continueButton.style.opacity = "0.5";
      continueButton.style.pointerEvents = "none";
      continueButton.classList.remove("btn-primary");
      continueButton.classList.add("btn-outline");
    }
  }

  continueButton.addEventListener("click", function (e) {
    if (selectedSeats.size === 0) {
      e.preventDefault();
      showNotification("Silakan pilih kursi terlebih dahulu", "error");
      return;
    }

    const params = new URLSearchParams();
    params.set("seats", Array.from(selectedSeats).join(","));
    params.set("total", calculateSubtotal() + serviceFee);

    this.href = `pembayaran.html?${params.toString()}`;
  });

  function calculateSubtotal() {
    let subtotalAmount = 0;
    selectedSeats.forEach((seatId) => {
      const seatElement = document.querySelector(`[data-seat="${seatId}"]`);
      subtotalAmount += parseInt(seatElement.dataset.price);
    });
    return subtotalAmount;
  }

  function formatPrice(price) {
    return new Intl.NumberFormat("id-ID").format(price);
  }

  function showNotification(message, type = "success") {
    notificationText.textContent = message;
    notification.className = "notification";

    const icon = notification.querySelector("i");
    if (type === "success") {
      notification.style.background =
        "linear-gradient(135deg, #28a745, #20c997)";
      icon.className = "fas fa-check-circle";
    } else if (type === "warning") {
      notification.style.background =
        "linear-gradient(135deg, #ffc107, #fd7e14)";
      icon.className = "fas fa-exclamation-triangle";
    } else {
      notification.style.background =
        "linear-gradient(135deg, #dc3545, #e83e8c)";
      icon.className = "fas fa-times-circle";
    }

    notification.classList.add("show");
    setTimeout(() => notification.classList.remove("show"), 3000);
  }

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && selectedSeats.size > 0) {
      selectedSeats.forEach((seatId) => {
        const seatElement = document.querySelector(`[data-seat="${seatId}"]`);
        seatElement.classList.remove("selected");
        seatElement.classList.add("available");
      });
      selectedSeats.clear();
      updateUI();
      showNotification("Semua pilihan kursi dibatalkan", "warning");
    }
  });

  const mobileMenuBtn = document.querySelector(".mobile-menu-btn");
  const navLinks = document.querySelector(".nav-links");
  const authButtons = document.querySelector(".auth-buttons");

  if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener("click", function () {
      navLinks.style.display =
        navLinks.style.display === "flex" ? "none" : "flex";
      authButtons.style.display =
        authButtons.style.display === "flex" ? "none" : "flex";
    });
  }

  seats.forEach((seat) => {
    seat.addEventListener("mouseenter", function () {
      if (!this.classList.contains("selected"))
        this.style.transform = "scale(1.05)";
    });
    seat.addEventListener("mouseleave", function () {
      if (!this.classList.contains("selected"))
        this.style.transform = "scale(1)";
    });
  });

  if (window.innerWidth <= 768) {
    const seatContainer = document.querySelector(".seats-map");
    let isScrolling = false;
    seatContainer.addEventListener("scroll", function () {
      if (!isScrolling) {
        window.requestAnimationFrame(() => (isScrolling = false));
        isScrolling = true;
      }
    });
  }

  setInterval(() => {
    if (selectedSeats.size > 0) {
      console.log("Auto-saving selection:", {
        seats: Array.from(selectedSeats),
        timestamp: new Date().toISOString(),
      });
    }
  }, 30000);

  window.addEventListener("beforeunload", function (e) {
    if (selectedSeats.size > 0) {
      e.preventDefault();
      e.returnValue =
        "Anda memiliki kursi yang dipilih. Yakin ingin meninggalkan halaman?";
    }
  });

  function addSeatSelectionEffect(seat) {
    seat.style.animation = "seatPulse 0.3s ease-in-out";
    setTimeout(() => (seat.style.animation = ""), 300);
  }

  const style = document.createElement("style");
  style.textContent = `
        @keyframes seatPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1.1); }
        }
    `;
  document.head.appendChild(style);
});
