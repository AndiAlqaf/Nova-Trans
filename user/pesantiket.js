// Script untuk interaksi halaman
document.addEventListener("DOMContentLoaded", function () {
  // Toggle FAQ
  const faqQuestions = document.querySelectorAll(".faq-question");

  faqQuestions.forEach((question) => {
    question.addEventListener("click", function () {
      const faqItem = this.parentElement;

      // Tutup FAQ lain yang sedang terbuka
      document.querySelectorAll(".faq-item.open").forEach((openItem) => {
        if (openItem !== faqItem) {
          openItem.classList.remove("open");
          const toggle = openItem.querySelector(".faq-toggle");
          toggle.innerHTML = '<i class="fas fa-plus"></i>';
        }
      });

      // Toggle FAQ yang diklik
      faqItem.classList.toggle("open");
      const toggle = faqItem.querySelector(".faq-toggle");

      if (faqItem.classList.contains("open")) {
        toggle.innerHTML = '<i class="fas fa-times"></i>';
      } else {
        toggle.innerHTML = '<i class="fas fa-plus"></i>';
      }
    });
  });

  // Animasi dropdown untuk section promo
  const promoDropdown = document.querySelector(".section-header .btn-dropdown");
  const promoCards = document.querySelector(".promo-cards");

  if (promoDropdown && promoCards) {
    promoDropdown.addEventListener("click", function () {
      promoCards.classList.toggle("hidden");

      if (promoCards.classList.contains("hidden")) {
        this.innerHTML = '<i class="fas fa-chevron-down"></i>';
      } else {
        this.innerHTML = '<i class="fas fa-chevron-up"></i>';
      }
    });
  }

  // Toggle tab menu
  const tabButtons = document.querySelectorAll(".tab-btn");

  tabButtons.forEach((button) => {
    button.addEventListener("click", function () {
      // Hapus kelas active dari semua tab
      document.querySelectorAll(".tab-btn").forEach((btn) => {
        btn.classList.remove("active");
      });

      // Tambahkan kelas active pada tab yang diklik
      this.classList.add("active");
    });
  });

  // Validasi form pencarian dan redirect ke keberangkatan.php
  const searchBtn = document.querySelector(".search-btn");

  if (searchBtn) {
    searchBtn.addEventListener("click", function (e) {
      e.preventDefault();

      // Dapatkan semua input yang diperlukan
      const kotaAsal = document.querySelector(
        'input[name="kota_asal"], select[name="kota_asal"]'
      );
      const kotaTujuan = document.querySelector(
        'input[name="kota_tujuan"], select[name="kota_tujuan"]'
      );
      const tanggalBerangkat = document.querySelector(
        'input[name="tanggal_berangkat"]'
      );
      const tanggalPulang = document.querySelector(
        'input[name="tanggal_pulang"]'
      );
      const jumlahPenumpang = document.querySelector(
        'input[name="jumlah_penumpang"]'
      );

      let isValid = true;
      let errorMessage = "";

      // Validasi kota asal
      if (
        !kotaAsal ||
        kotaAsal.value.trim() === "" ||
        kotaAsal.value === "Pilih lokasi"
      ) {
        if (kotaAsal) kotaAsal.style.borderColor = "red";
        isValid = false;
        errorMessage += "Pilih kota asal. ";
      } else {
        if (kotaAsal) kotaAsal.style.borderColor = "";
      }

      // Validasi kota tujuan
      if (
        !kotaTujuan ||
        kotaTujuan.value.trim() === "" ||
        kotaTujuan.value === "Pilih lokasi"
      ) {
        if (kotaTujuan) kotaTujuan.style.borderColor = "red";
        isValid = false;
        errorMessage += "Pilih kota tujuan. ";
      } else {
        if (kotaTujuan) kotaTujuan.style.borderColor = "";
      }

      // Validasi tanggal berangkat
      if (!tanggalBerangkat || tanggalBerangkat.value.trim() === "") {
        if (tanggalBerangkat) tanggalBerangkat.style.borderColor = "red";
        isValid = false;
        errorMessage += "Pilih tanggal berangkat. ";
      } else {
        if (tanggalBerangkat) tanggalBerangkat.style.borderColor = "";
      }

      // Validasi jumlah penumpang
      if (
        !jumlahPenumpang ||
        jumlahPenumpang.value.trim() === "" ||
        parseInt(jumlahPenumpang.value) < 1
      ) {
        if (jumlahPenumpang) jumlahPenumpang.style.borderColor = "red";
        isValid = false;
        errorMessage += "Pilih jumlah penumpang. ";
      } else {
        if (jumlahPenumpang) jumlahPenumpang.style.borderColor = "";
      }

      // Jika semua validasi berhasil, redirect ke keberangkatan.php
      if (isValid) {
        // Buat URL dengan parameter
        const params = new URLSearchParams();
        params.append("kota_asal", kotaAsal.value);
        params.append("kota_tujuan", kotaTujuan.value);
        params.append("tanggal_berangkat", tanggalBerangkat.value);
        params.append("jumlah_penumpang", jumlahPenumpang.value);

        // Tambahkan tanggal pulang jika ada
        if (tanggalPulang && tanggalPulang.value.trim() !== "") {
          params.append("tanggal_pulang", tanggalPulang.value);
        }

        // Redirect ke halaman keberangkatan.php dengan parameter
        window.location.href = "keberangkatan.php?" + params.toString();
      } else {
        alert("Mohon lengkapi form pencarian: " + errorMessage);
      }
    });
  }

  // Validasi form langganan
  const subscribeForm = document.querySelector(".subscribe-form");

  if (subscribeForm) {
    subscribeForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const emailInput = this.querySelector('input[type="email"]');
      const email = emailInput.value.trim();

      // Validasi email sederhana
      if (email === "" || !email.includes("@")) {
        emailInput.style.borderColor = "red";
        alert("Mohon masukkan alamat email yang valid.");
      } else {
        emailInput.style.borderColor = "";
        alert("Terima kasih telah berlangganan!");
        emailInput.value = "";
      }
    });
  }

  // Animasi hover untuk step booking
  const steps = document.querySelectorAll(".step");

  steps.forEach((step) => {
    step.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-10px)";
    });

    step.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0)";
    });
  });

  // Efek parallax untuk bagian hero
  window.addEventListener("scroll", function () {
    const hero = document.querySelector(".hero");
    const scrollPosition = window.scrollY;

    if (hero) {
      hero.style.backgroundPositionY = scrollPosition * 0.5 + "px";
    }
  });
});
