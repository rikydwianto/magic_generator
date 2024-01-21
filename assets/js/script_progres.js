$(document).ready(function () {
  // Inisialisasi DataTables
  var table = $("#table_capaian").DataTable();
  var table_biasa = $("#table").DataTable();

  // Filter berdasarkan bulan
  $("#filterBulan").on("change", function () {
    table.column(5).search(this.value).draw(); // Ubah angka 5 sesuai dengan indeks kolom bulan
  });

  // // Filter berdasarkan tahun
  $("#filterTahun").on("change", function () {
    table.column(6).search(this.value).draw(); // Ubah angka 6 sesuai dengan indeks kolom tahun
  });

  // // Filter berdasarkan minggu
  $("#filterMinggu").on("change", function () {
    table.column(4).search(this.value).draw(); // Ubah angka 7 sesuai dengan indeks kolom minggu
  });
});
