const asal = localStorage.getItem("asal");
const tujuan = localStorage.getItem("tujuan");

if (asal && tujuan) {
  document.getElementById("hasil").innerHTML = (
    <p>
      Menampilkan rute dari <strong>${asal}</strong> ke{" "}
      <strong>${tujuan}</strong>
    </p>
  );
} else {
  document.getElementById("hasil").innerHTML = <p>Data tidak tersedia.</p>;
}
