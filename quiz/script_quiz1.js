function getSoal() {
  $(".loader").show();
  $("#questionContainer").hide();
  $.ajax({
    url: url_api + "soal.php?data-soal", // Ganti dengan URL yang sesuai
    method: "POST",
    data: {
      id_kuis: id_kuis,
      id_jawab: id_jawab,
    },
    dataType: "json",
    success: function (response) {
      // Proses respons dari server
      if (response.result) {
        let data = response.result;
        if (data.total_soal <= data.soal_dijawab) {
          window.location.href = url + "lihat_hasil.php";
        }
        localStorage.setItem("waktu", data.data_kuis.waktu);
        // console.log(localStorage);
        setTimeout(function () {
          hitung_soal(response.result);
          displayQuestion(response.result);
          $(".loader").hide();
          $("#questionContainer").show();
        }, 100);
        //   tombol(response.result);
      } else {
        console.error("Gagal mendapatkan soal dari server");
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error(
        "Gagal melakukan permintaan Ajax:",
        textStatus,
        errorThrown
      );
    },
  });
}

// Panggil fungsi getSoal saat halaman dimuat
getSoal();

function hitung_soal(data) {
  let soal = $("#hitung_soal");
  let soal_ke = parseInt(data.soal_dijawab) + 1;
  let total_soal = parseInt(data.total_soal);
  soal.html("Soal ke-" + soal_ke + " dari " + data.total_soal);

  let btn = $("#nextBtn");
  let cekHasil = $("#cekHasil");
  //   cekHasil.hide();
  //   console.log(`dari ${soal_ke}`, "total soal : ", total_soal);
  if (soal_ke == total_soal) {
    btn.hide();
    cekHasil.show();
  } else {
    btn.show();
    cekHasil.hide();
  }
}
var currentQuestionIndex = 0;
function displayQuestion(data) {
  var kuis = data.data_kuis;
  var acak = kuis.acak;
  var soal = data.data_soal;

  var currentQuestion = soal[currentQuestionIndex];
  var pilihan = currentQuestion.pilihan;
  var questionContainer = $("#questionContainer");
  var pilihanHTML = document.getElementById("questionContainer");
  var pilihanObjek = JSON.parse(pilihan);

  questionContainer.html("");
  // Tampilkan soal dan pilihan
  if (currentQuestion.url_gambar != null) {
    cekGambar(currentQuestion.id_soal, "soal");
  }
  questionContainer.append("<h3>" + currentQuestion.soal + "</h3>");
  questionContainer.append(`<input type="hidden" name="id_soal" id="" value="${currentQuestion.id_soal}">
  <input type="hidden" name="id_kuis" id="" value="${currentQuestion.id_kuis}">
`);

  var kunciPilihan = Object.keys(pilihanObjek);
  questionContainer.append("<h5>PILIHAN</h5>");
  questionContainer.append("<ul class='list-group'>");
  function random() {
    return 0.5 - Math.random();
  }
  if (acak == "ya") {
    kunciPilihan.sort(random);
  }
  // Mengrandom array kunciPilihan menggunakan sort dan fungsi pembanding random

  kunciPilihan.forEach(function (kunci) {
    let pilihan =
      acak != "ya" ? pilihanObjek[kunci].id.toUpperCase() + ". " : "";

    pilihanHTML.innerHTML += `
      <li class="list-group-item" onclick='ceklis("${pilihanObjek[kunci].id}")'>
          <input class="form-check-input me-1" onclick='ceklis("${pilihanObjek[kunci].id}")' type="radio" name="pilihan" value="${pilihanObjek[kunci].id}" id="pilihan${pilihanObjek[kunci].id}">
          <label class="form-check-label" onclick='ceklis("${pilihanObjek[kunci].id}")' for="pilihan${pilihanObjek[kunci].id}" name='pilihan'>${pilihan} ${pilihanObjek[kunci].teks}</label>
      </li>
        `;
  });
  questionContainer.append("</ul>");
  //   console.log(currentQuestion.id_soal);
}

function validateForm() {
  var selectedOption = document.querySelector('input[name="pilihan"]:checked');
  if (!selectedOption) {
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Silakan pilih salah satu opsi!",
    });
  } else {
    // Uncomment the line below if you want to submit the form
    confirmAndSubmit();
  }
}

function validateForm1() {
  var selectedOption = document.querySelector('input[name="pilihan"]:checked');
  if (!selectedOption) {
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Silakan pilih salah satu opsi!",
    });
  } else {
    // Uncomment the line below if you want to submit the form
    Swal.fire({
      title: "Yakin dengan jawaban ini?",
      text: "Ini adalah soal terakhir, setelah mengklik Ya, tunggu sampai loading selesai.",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ya, kirim jawaban",
      cancelButtonText: "Batal",
    }).then((result) => {
      // Jika pengguna mengonfirmasi, submit formulir
      if (result.isConfirmed) {
        // Swal.fire("berhasil");
        jawabAndCek();
      } else {
        Swal.fire({
          title: "Informasi",
          text: "Silahkan Pikirkan kembali jawaban nya sebelum dikirim.",
          icon: "warning",
        });
      }
    });
  }
}
function ceklis(angka) {
  var checkbox = document.getElementById("pilihan" + angka);
  // Toggle status centang checkbox
  checkbox.checked = !checkbox.checked;
}
function confirmAndSubmit() {
  // Munculkan konfirmasi
  Swal.fire({
    title: "Yakin dengan jawaban ini?",
    text: "Anda tidak akan dapat melihat jawaban sebelumnya setelah mengirim.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Ya, kirim jawaban",
    cancelButtonText: "Batal",
  }).then((result) => {
    // Jika pengguna mengonfirmasi, submit formulir
    if (result.isConfirmed) {
      //   Swal.fire("berhasil");
      input_jawaban();
    } else {
      Swal.fire({
        title: "Informasi",
        text: "Silahkan Pikirkan kembali jawaban nya sebelum dikirim.",
        icon: "warning",
      });
    }
  });
}

function kirimData(id_jawab, id_kuis, id_soal, pilihan) {
  $.ajax({
    type: "POST",
    url: url_api + "soal.php?input-soal",
    data: {
      id_jawab: id_jawab,
      id_kuis: id_kuis,
      id_soal: id_soal,
      pilihan: pilihan,
    },
    dataType: "json",
    success: function (response) {
      //   console.log(response);
      // Tambahkan logika atau tindakan lainnya setelah berhasil mengirim data
    },
    error: function (error) {
      console.error("Gagal mengirim data:", error);
      // Tambahkan logika atau tindakan lainnya setelah gagal mengirim data
    },
  });
}
function input_jawaban() {
  let id_soal = $('input[name="id_soal"]').val();
  let pilihan = $('input[name="pilihan"]:checked').val();
  kirimData(id_jawab, id_kuis, id_soal, pilihan);
  getSoal();
}

function updateKuis(id_kuis, id_jawab) {
  $.ajax({
    type: "POST",
    url: url_api + "soal.php?update-kuis",
    data: {
      id_kuis: id_kuis, // Ganti dengan ID kuis yang sesuai
      id_jawab: id_jawab, // Ganti dengan ID jawab yang sesuai
    },
    dataType: "json",
    success: function (response) {
      // Tanggapan dari server
    },
    error: function (jqXHR, textStatus, errorThrown) {
      // Tanggapan jika terjadi kesalahan dalam melakukan AJAX
      console.error("Error: " + textStatus, errorThrown);
    },
  });
}

function jawabAndCek() {
  let id_soal = $('input[name="id_soal"]').val();
  let pilihan = $('input[name="pilihan"]:checked').val();
  kirimData(id_jawab, id_kuis, id_soal, pilihan);
  $("#questionContainer").hide();
  $(".loader").show();
  updateKuis(id_kuis, id_jawab);
  setTimeout(() => {
    window.location.href = url + "lihat_hasil.php";
  }, 2000);
}
$(document).ready(function () {
  // Set the countdown duration in minutes
  var countdownDuration = parseInt(localStorage.getItem("waktu")); // Change this to your desired countdown duration in minutes

  // Check if there is a countdown value in localStorage
  var storedCountdown = localStorage.getItem("countdown");
  var endTime;

  if (storedCountdown) {
    endTime = parseInt(storedCountdown, 10);
  } else {
    // Calculate the end time based on the current time and countdown duration
    endTime = new Date().getTime() + countdownDuration * 60 * 1000;
    // Save the end time to localStorage
    localStorage.setItem("countdown", endTime);
  }

  var x = setInterval(function () {
    // Get the current time
    var now = new Date().getTime();

    // Calculate the remaining time in milliseconds
    var distance = endTime - now;

    // Calculate minutes and seconds
    var minutes = Math.floor(distance / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    // Display the countdown
    $("#countdown").html(minutes + "m " + seconds + "d ");

    // If the countdown is over, display a message
    if (distance < 0) {
      clearInterval(x);
      $("#countdown").html("waktu habis!");
      $("#gambar").hide();
      Swal.fire("STOP! Waktu Habis tunggu sampai proses selesai!");
      // console.log(distance);
      waktuHabis();
      updateKuis(id_kuis, id_jawab);
      setTimeout(() => {
        window.location.href = url + "lihat_hasil.php";
      }, 3000);
    }
  }, 1000); // Update every 1 second
});
function waktuHabis() {
  // Swal.fire({ title: "WAKTU HABIS", icon: "danger" });
  $("#questionContainer").html(
    `<h1 class='text-center'>WAKTU HABIS <br> 
    Soal yang belum terjawab akan otomatis disalahkan semua! <br/>
     Tunggu sampai diarahkan ke halaman berikut nya! <br/>
     <span class='loader1'></span></h1>`
  );
  $("#nextBtn").hide();
  $.ajax({
    type: "POST",
    url: url_api + "soal.php?belum-terjawab",
    data: {
      id_kuis: id_kuis, // Ganti dengan ID kuis yang sesuai
      id_jawab: id_jawab, // Ganti dengan ID jawab yang sesuai
    },
    dataType: "json",
    success: function (response) {
      // Tanggapan dari server
      response.data.forEach((index) => {
        let id_soal = index.id_soal;
        kirimData(id_jawab, id_kuis, id_soal, "TIDAKJAWAB");
        console.log(id_soal);
      });
    },
    error: function (jqXHR, textStatus, errorThrown) {
      // Tanggapan jika terjadi kesalahan dalam melakukan AJAX
      console.error("Error: " + textStatus, errorThrown);
    },
  });
}
function hapusWaktu() {
  localStorage.clear();
}
// console.log(localStorage);

function cekGambar(id_soal, ket) {
  // Pemanggilan AJAX menggunakan metode GET
  var url_gambar = "";
  $("#gambar").html("");
  $.ajax({
    type: "GET",
    url: url_api + "gambar_soal.php", //?id=" + id_soal + "&ket=" + ket,
    data: { id: id_soal, ket: ket },
    dataType: "json",

    success: function (response) {
      // Menangani respons JSON
      if (response.hasil.url_gambar != "") {
        var gambarHTML = $("#gambar");

        gambarHTML.html(`<h6>Perhatikan gambar dibawah</h6>
        <img src='${response.hasil.url_gambar}' class='img-fluid' />
        `);
      }
    },
    error: function (xhr, status, error) {
      // Menangani kesalahan
      console.log(error);
    },
  });
}
window.addEventListener("beforeunload", function (event) {
  updateKuis(id_kuis, id_jawab);
});
