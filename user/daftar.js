document.addEventListener("DOMContentLoaded", function () {
  // Mobile menu functionality
  const mobileMenuBtn = document.getElementById("mobileMenuBtn");
  const navLinks = document.getElementById("navLinks");
  const authButtons = document.getElementById("authButtons");

  mobileMenuBtn.addEventListener("click", function () {
    navLinks.classList.toggle("active");
    authButtons.classList.toggle("active");

    const icon = this.querySelector("i");
    if (navLinks.classList.contains("active")) {
      icon.classList.remove("fa-bars");
      icon.classList.add("fa-times");
    } else {
      icon.classList.remove("fa-times");
      icon.classList.add("fa-bars");
    }
  });

  // Form handling
  const form = document.getElementById("registerForm");
  const submitBtn = document.getElementById("submitBtn");
  const passwordInput = document.getElementById("password");
  const confirmPasswordInput = document.getElementById("confirm-password");

  // Real-time password confirmation validation
  function validatePasswordMatch() {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    const confirmGroup = confirmPasswordInput.closest(".form-group");

    // Remove existing messages
    const existingMsg = confirmGroup.querySelector(
      ".error-message, .success-message"
    );
    if (existingMsg) {
      existingMsg.remove();
    }

    if (confirmPassword && password !== confirmPassword) {
      confirmGroup.classList.add("error");
      confirmGroup.classList.remove("success");

      const errorMsg = document.createElement("div");
      errorMsg.className = "error-message";
      errorMsg.innerHTML =
        '<i class="fas fa-exclamation-circle"></i> Kata sandi tidak cocok';
      confirmGroup.appendChild(errorMsg);
    } else if (confirmPassword && password === confirmPassword) {
      confirmGroup.classList.remove("error");
      confirmGroup.classList.add("success");

      const successMsg = document.createElement("div");
      successMsg.className = "success-message";
      successMsg.innerHTML =
        '<i class="fas fa-check-circle"></i> Kata sandi cocok';
      confirmGroup.appendChild(successMsg);
    }
  }

  confirmPasswordInput.addEventListener("input", validatePasswordMatch);
  passwordInput.addEventListener("input", validatePasswordMatch);

  // Enhanced input validation
  const inputs = form.querySelectorAll("input[required]");
  inputs.forEach((input) => {
    input.addEventListener("blur", function () {
      const group = this.closest(".form-group");
      if (this.checkValidity()) {
        group.classList.remove("error");
        group.classList.add("success");
      } else {
        group.classList.remove("success");
        group.classList.add("error");
      }
    });

    input.addEventListener("input", function () {
      const group = this.closest(".form-group");
      if (this.checkValidity()) {
        group.classList.remove("error");
      }
    });
  });

  // Form submission handling
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirmPassword = document
      .getElementById("confirm-password")
      .value.trim();
    const nama = document.getElementById("nama").value.trim();
    const no_hp = document.getElementById("no_hp").value.trim();

    // Validation
    if (!nama || !email || !no_hp || !password || !confirmPassword) {
      alert("Semua field harus diisi!");
      return;
    }

    if (password !== confirmPassword) {
      alert("Password dan konfirmasi password tidak cocok!");
      return;
    }

    if (password.length < 6) {
      alert("Password minimal 6 karakter!");
      return;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      alert("Format email tidak valid!");
      return;
    }

    // Phone validation
    const phoneRegex = /^[0-9]{10,15}$/;
    if (!phoneRegex.test(no_hp)) {
      alert("Nomor HP harus berupa 10-15 digit angka!");
      return;
    }

    // Loading state
    submitBtn.classList.add("btn-loading");
    submitBtn.disabled = true;

    // Simulate API call
    setTimeout(() => {
      try {
        // Get existing accounts from memory (since localStorage is not available)
        let accounts = window.registeredAccounts || {};

        // Check if email already exists
        if (accounts[email]) {
          alert(
            "Email sudah terdaftar! Silakan gunakan email lain atau langsung masuk."
          );
          window.location.href = "masuk.html";
          return;
        }

        // Save new account
        accounts[email] = {
          nama: nama,
          no_hp: no_hp,
          password: password,
          registeredAt: new Date().toISOString(),
        };

        // Store in memory
        window.registeredAccounts = accounts;

        alert("Pendaftaran berhasil! Silakan masuk dengan akun Anda.");
        window.location.href = "masuk.html";
      } catch (error) {
        alert("Terjadi kesalahan saat mendaftar. Silakan coba lagi.");
        console.error("Registration error:", error);
      } finally {
        // Reset loading state
        submitBtn.classList.remove("btn-loading");
        submitBtn.disabled = false;
      }
    }, 1500);
  });
});
