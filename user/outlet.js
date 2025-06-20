// Filter outlet berdasarkan kota
document.getElementById("kota").addEventListener("change", function () {
  const selectedKota = this.value;
  const outletCards = document.querySelectorAll(".outlet-card");

  outletCards.forEach((card) => {
    if (selectedKota === "all" || card.dataset.kota === selectedKota) {
      card.style.display = "flex";
    } else {
      card.style.display = "none";
    }
  });
});
