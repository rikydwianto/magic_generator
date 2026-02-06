function formatAngka(input) {
  let value = input.value.replace(/\D/g, "");

  value = new Intl.NumberFormat("id-ID").format(value);

  input.value = value;
}

document.addEventListener("DOMContentLoaded", function () {
  var form = document.querySelector("form");
  var anggotaMasukInput = form.querySelector("[name=anggota_masuk]");
  var anggotaKeluarInput = form.querySelector("[name=anggota_keluar]");
  var nettAnggotaInput = form.querySelector("[name=nett_anggota]");
  var hasil = this.getElementById("hasil_agt");
  function hitungNettAnggota() {
    var anggotaMasuk =
      parseInt(anggotaMasukInput.value.replace(/\./g, "")) || 0;
    var anggotaKeluar =
      parseInt(anggotaKeluarInput.value.replace(/\./g, "")) || 0;

    var nettAnggota = anggotaMasuk - anggotaKeluar;
    if (nettAnggota > 0) {
      hasil.classList.add("text-success");
      hasil.classList.remove("text-danger");
    } else {
      hasil.classList.add("text-danger");
      hasil.classList.remove("text-success");
    }
    hasil.textContent = nettAnggota;
    nettAnggotaInput.value = nettAnggota;
  }

  anggotaMasukInput.addEventListener("input", hitungNettAnggota);
  anggotaKeluarInput.addEventListener("input", hitungNettAnggota);
});
document.addEventListener("DOMContentLoaded", function () {
  var form = document.querySelector("form");
  var naikParInput = form.querySelector("[name=naik_par]");
  var turunParInput = form.querySelector("[name=turun_par]");
  var nettParInput = form.querySelector("[name=nett_par]");
  var hasil = this.getElementById("hasil");

  function hitungNettPar() {
    var naikPar = parseInt(naikParInput.value.replace(/\./g, "")) || 0;
    var turunPar = parseInt(turunParInput.value.replace(/\./g, "")) || 0;

    var nettPar = naikPar - turunPar;

    var value = new Intl.NumberFormat("id-ID").format(nettPar);

    nettParInput.value = value;
    if (nettPar < 0) {
      hasil.classList.add("text-success");
      hasil.classList.remove("text-danger");
    } else {
      hasil.classList.add("text-danger");
      hasil.classList.remove("text-success");
    }

    hasil.textContent = value;
  }

  naikParInput.addEventListener("input", hitungNettPar);
  turunParInput.addEventListener("input", hitungNettPar);
});

function validateInput() {
  var regex = /^[1-9]\d*00$/; // Validasi apakah angka merupakan kelipatan seratus

  var naikParValue = document
    .getElementById("naikParInput")
    .value.replace(/\D/g, "");
  var turunParValue = document
    .getElementById("turunParInput")
    .value.replace(/\D/g, "");
  var submitBtn = document.getElementById("tm_simpan");
  var konfirm = document.getElementById("tm_konfirmasi");
  var val = document.getElementById("keterangan");
  var hasil = naikParValue % 100;
  var hasil_1 = turunParValue % 100;
  // alert(hasil);
  if ((hasil > 0 && hasil <= 99) || (hasil_1 > 0 && hasil_1 <= 99)) {
    // konfirm.disabled = false;
    submitBtn.disabled = true;
    // konfirm.disabled = true;
    val.innerHTML =
      "Format OS PAR TIDAK SESUAI! <br> Note : * dimaksud adalah balance bukan total anggotanya contoh 1.000.000";
  } else {
    submitBtn.disabled = false;
    val.innerHTML = "";
  }
  // if (parseInt(naikParValue) === 0 && parseInt(turunParValue) === 0) {
  //   submitBtn.disabled = false;
  //   val.innerHTML = "";
  // }
}
validateInput();
$(document).ready(function () {
  $("#tabelCapaianStaff").DataTable();
});
$(document).ready(function () {
  // Menanggapi acara modal yang ditampilkan
  var $signaturePad = $("#signature");
  var btn = $("#submitBtn");

  // Menanggapi acara modal yang ditampilkan
  $("#exampleModal").on("shown.bs.modal", function () {
    // Periksa apakah JSignature telah diinisialisasi sebelumnya
    if (!$signaturePad.data("jSignature")) {
      // Inisialisasi JSignature jika belum diinisialisasi
      $("#signature canvas").remove();

      $signaturePad.jSignature();
    } else {
      // Reset tanda tangan jika sudah diinisialisasi sebelumnya
      // $signaturePad.jSignature("reset");
    }
  });

  $signaturePad.on("change", function () {
    // Mengecek apakah ada tanda tangan
    var isSignatureEmpty =
      $signaturePad.jSignature("getData", "native").length === 0;
    var submitBtn = document.getElementById("submitBtn");
    var imageData = $signaturePad.jSignature("getData", "image");

    // Mengatur status disabled berdasarkan kondisi tanda tangan
    submitBtn.disabled = isSignatureEmpty;

    // Lakukan sesuatu berdasarkan hasil pengecekan
    if (isSignatureEmpty) {
      // console.log("Pengguna belum menggambar atau menandatangani.");
    } else {
      // console.log("Pengguna telah menggambar atau menandatangani.");
    }
  });
});

function saveSignature() {
  var signatureData = $("#signature").jSignature("getData", "image");
  // Kirim atau simpan data tanda tangan di sini
}

function resetSignature() {
  // Mereset tanda tangan
  $("#signature").jSignature("reset");
}
