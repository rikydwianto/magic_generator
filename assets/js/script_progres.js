$(document).ready(function () {
  // Inisialisasi DataTables
  var table = $("#table_capaian").DataTable();

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

  var table1 = $("#cabang").DataTable();

  // Filter berdasarkan bulan
  $("#filtercabang").on("change", function () {
    table1.column(1).search(this.value).draw(); // Ubah angka 5 sesuai dengan indeks kolom bulan
  });
});
