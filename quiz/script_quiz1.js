var isCountdownPaused = false;

function ajaxRequest(url, method, data) {
  return new Promise(function (resolve, reject) {
    $.ajax({
      url: url,
      method: method,
      data: data,
      dataType: "json",
      success: resolve,
      error: reject,
    });
  });
}
async function getSoal() {
  try {
    $(".loader").show();
    $("#questionContainer").hide();

    const response = await ajaxRequest(url_api + "soal.php?data-soal", "POST", {
      id_kuis: id_kuis,
      id_jawab: id_jawab,
    });

    // Proses respons dari server
    if (response.result) {
      let data = response.result;
      if (data.total_soal == data.soal_dijawab) {
        // window.location.href = url + "lihat_hasil.php?selesai";
      }

      localStorage.setItem("waktu", data.data_kuis.waktu);

      // Menunggu selama 1 detik menggunakan setTimeout
      await new Promise((resolve) => setTimeout(resolve, 1000));

      hitung_soal(response.result);
      displayQuestion(response.result);
      $(".loader").hide();
      $("#questionContainer").show();
    } else {
      // console.error("Gagal mendapatkan soal dari server");
    }
  } catch (error) {
    // console.error("Gagal melakukan permintaan Ajax:", error);
  }
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
    if (result.isConfirmed) {
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
  pauseCountdown();
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
      resumeCountdown();

      getSoal();
    },
    error: function (error) {
      console.error("Gagal mengirim data:", error);
    },
  });
}
function input_jawaban() {
  let id_soal = $('input[name="id_soal"]').val();
  let pilihan = $('input[name="pilihan"]:checked').val();
  kirimData(id_jawab, id_kuis, id_soal, pilihan);
}

function updateKuis(id_kuis, id_jawab) {
  $.ajax({
    type: "POST",
    url: url_api + "soal.php?update-kuis",
    data: {
      id_kuis: id_kuis,
      id_jawab: id_jawab,
    },
    dataType: "json",
    success: function (response) {},
    error: function (jqXHR, textStatus, errorThrown) {
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
  setTimeout(function () {
    window.location.href = url + "lihat_hasil.php";
  }, 2000);
}
var countdownDuration = parseInt(localStorage.getItem("waktu"));
var storedCountdown = localStorage.getItem("countdown");
var endTime;
var endTime;

if (storedCountdown) {
  endTime = parseInt(storedCountdown, 10);
} else {
  endTime = new Date().getTime() + countdownDuration * 60 * 1000;
  localStorage.setItem("countdown", endTime);
}

var isCountdownPaused = false;

// Fungsi untuk mengurangi waktu
function updateCountdown() {
  if (!isCountdownPaused) {
    var now = new Date().getTime();
    var distance = endTime - now;
    var minutes = Math.floor(distance / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    $("#countdown").html(minutes + "m " + seconds + "d ");

    if (distance < 0) {
      clearInterval(countdownInterval);
      $("#countdown").html("Waktu habis!");
      $("#gambar").hide();
      Swal.fire("STOP! Waktu Habis tunggu sampai proses selesai!");
      waktuHabis();
      updateKuis(id_kuis, id_jawab);

      setTimeout(function () {
        window.location.href = url + "lihat_hasil.php";
      }, 3000);
    }
  }
}

// Memulai perhitungan waktu
var countdownInterval = setInterval(updateCountdown, 1000);

function pauseCountdown() {
  isCountdownPaused = true;
}

function resumeCountdown() {
  isCountdownPaused = false;
}

function waktuHabis() {
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
      id_kuis: id_kuis,
      id_jawab: id_jawab,
    },
    dataType: "json",
    success: function (response) {
      response.data.forEach((index) => {
        let id_soal = index.id_soal;
        kirimData(id_jawab, id_kuis, id_soal, "TIDAKJAWAB");
      });
    },
    error: function (jqXHR, textStatus, errorThrown) {
      // Tanggapan jika terjadi kesalahan dalam melakukan AJAX
      console.error("Error: " + textStatus, errorThrown);
    },
  });
}

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
      if (response.hasil.url_gambar != "") {
        var gambarHTML = $("#gambar");

        gambarHTML.html(`<h6>Perhatikan gambar dibawah</h6>
        <img src='${response.hasil.url_gambar}' class='img-fluid' />
        `);
      }
    },
    error: function (xhr, status, error) {
      console.log(error);
    },
  });
}
window.addEventListener("beforeunload", function (event) {
  updateKuis(id_kuis, id_jawab);
});
