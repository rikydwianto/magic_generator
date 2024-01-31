var url = window.location.href;

var urlParams = new URLSearchParams(url);

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
        let unik2 = jsonResponse[0]?.unique_id_2;
        if (unik2 !== undefined && unik2 !== null && unik2 !== "") {
          window.location.href =
            url_quiz + "index.php?id=" + id_kuis + "&post-test&unik=" + unik2;
        } else {
          // console.log("Tidak ada data");
        }
      }
    };

    xhr.send(
      "data=" + encodeURIComponent(dataLocalStorage) + "&id_kuis=" + id_kuis
    );
  }
}
