$(document).ready(function () {
  $("#submitBtn").click(function () {
    // Validasi formulir sebelum konfirmasi SweetAlert
    if ($("#karyawanForm")[0].checkValidity()) {
      Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin mengirimkan formulir?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
        reverseButtons: true,
        customClass: {
          confirmButton: "btn btn-success",
          cancelButton: "btn btn-danger ml-2",
        },
      }).then((result) => {
        if (result.isConfirmed) {
          // Jika konfirmasi "Ya", kirim formulir
          $("#karyawanForm").submit();
        }
      });
    } else {
      // Tampilkan SweetAlert jika ada field yang kosong
      Swal.fire({
        title: "Peringatan",
        text: "Harap isi semua field.",
        icon: "warning",
        confirmButtonText: "OK",
        customClass: {
          confirmButton: "btn btn-warning",
        },
      });
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("startQuizBtn")
    .addEventListener("click", function () {
      Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin memulai kuis? Setelah memulai, soal tidak dapat diulang.",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
        reverseButtons: true,
        customClass: {
          confirmButton: "btn btn-success",
          cancelButton: "btn btn-danger ml-2",
        },
      }).then((result) => {
        if (result.isConfirmed) {
          // Jika konfirmasi "Ya", kirim formulir

          localStorage.removeItem("countdown");
          localStorage.removeItem("waktu");

          document.getElementById("startQuizForm").submit();
        }
      });
    });
});
