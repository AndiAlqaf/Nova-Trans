document.querySelector(".booking-form").addEventListener("submit", (e) => {
  e.preventDefault();
  const formData = {
    departure: document.getElementById("departure").value,
    destination: document.getElementById("destination").value,
    passengers: document.getElementById("passengers").value,
    departDate: document.getElementById("departDate").value,
    returnDate: returnDate.disabled ? null : returnDate.value,
  };

  if (!formData.departure || !formData.destination || !formData.departDate) {
    alert("Mohon lengkapi data keberangkatan, tujuan, dan tanggal pergi");
    return;
  }

  console.log("Form submitted:", formData);

  // Pengalihan setelah validasi berhasil
  window.location.href = "keberangkatan.php";
});
