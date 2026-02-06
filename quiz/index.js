var url = window.location.href;

var urlParams = new URLSearchParams(url);

function hitungSelisihWaktu(tanggalAwal, tanggalAkhir = new Date()) {
  // Mendapatkan waktu dari parameter
  const waktuAwal = new Date(tanggalAwal);
  const waktuAkhir = new Date(tanggalAkhir);

  // Menghitung selisih waktu dalam milidetik
  const selisihMilidetik = waktuAkhir - waktuAwal;

  // Menghitung selisih waktu dalam jam dan hari
  const selisihJam = selisihMilidetik / (1000 * 60 * 60);
  const selisihHari = Math.floor(selisihJam / 24);

  return {
    hari: selisihHari,
    jam: selisihJam % 24,
    waktu: tanggalAwal,
  };
}

if (urlParams.has("post-test")) {
  var postTestValue = urlParams.get("post-test");
} else {
  var dataLocalStorage = localStorage.getItem("unique_id");

  if (dataLocalStorage != "") {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", url_api + "info_kuis.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        var jsonResponse = JSON.parse(xhr.responseText);
        waktu = hitungSelisihWaktu(jsonResponse.created);
        if (waktu.hari >= 1) {
        } else {
          let unik2 = jsonResponse?.unique_id_2;
          if (unik2 !== undefined && unik2 !== null && unik2 !== "") {
            window.location.href =
              url_quiz + "index.php?id=" + id_kuis + "&post-test&unik=" + unik2;
          } else {
            // console.log("Tidak ada data");
          }
        }
      }
    };

    xhr.send("data=" + dataLocalStorage + "&id_kuis=" + id_kuis);
  }
}
