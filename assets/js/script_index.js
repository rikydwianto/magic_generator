$(document).ready(function () {
  $("#example").DataTable({
    dom: "Bfrtip",
    buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
  });
  $("#example2").DataTable({
    dom: "Bfrtip",
    buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdfHtml5"],
  });
});

$(document).ready(function () {
  var table = $("#soalTable").DataTable();

  $("#kategoriFilter, #subkategoriFilter").change(function () {
    var kategori = $("#kategoriFilter").val();
    var subkategori = $("#subkategoriFilter").val();

    table.column(4).search(kategori).draw();
    table.column(5).search(subkategori).draw();
  });
});
$(document).ready(function () {
  $(".tambah-pilihan").click(function () {
    var hurufTerakhir = $("#pilihan-container .input-group").length + 1;
    var huruf = String.fromCharCode(64 + hurufTerakhir);

    var html =
      '<div class="input-group mb-2">' +
      '<div class="input-group-prepend">' +
      '<span class="input-group-text">' +
      huruf +
      "</span>" +
      "</div>" +
      '<input type="text" class="form-control" name="pilihan[]" placeholder="Teks Pilihan" required>' +
      '<div class="input-group-append">' +
      '<button class="btn btn-danger hapus-pilihan" type="button">-</button>' +
      "</div>" +
      "</div>";
    $("#pilihan-container").append(html);
  });

  $("#pilihan-container").on("click", ".hapus-pilihan", function () {
    $(this).closest(".input-group").remove();

    $("#pilihan-container .input-group").each(function (index) {
      var huruf = String.fromCharCode(65 + index);
      $(this).find(".input-group-prepend .input-group-text").text(huruf);
    });
  });

  var quill = new Quill("#editor", {
    theme: "snow",
    modules: {
      toolbar: [
        ["bold", "italic", "underline", "strike"],
        ["image", "link"],
        [
          {
            list: "ordered",
          },
          {
            list: "bullet",
          },
        ],
        ["clean"],
      ],
    },
  });
  quill.root.style.fontSize = "1.5rem";

  document.getElementById("myForm").addEventListener("submit", function () {
    document.getElementById("soal").value = quill.root.innerHTML;
  });
});
let popupIsOpen = false;

function openNewTab(id, id_kuis) {
  window.open(
    "popup_jawaban.php?id=" + id + "&id_kuis=" + id_kuis,
    "_blank",
    "width=800,height=600"
  );
}

function ambilGambar(id, ket) {
  $(".loader").show();
  $(".isi").html(`Wait . . .`);
  $.ajax({
    type: "GET",
    url: url_api + "gambar_soal.php?id=" + id + "&ket=" + ket,
    dataType: "json",
    success: function (response) {
      if (response.hasil) {
        let hasil = response.hasil;
        $("#staticBackdropLabel").html("Gambar soal : " + hasil.soal);

        setTimeout(function () {
          $(".isi").html(`
        <img src="${hasil.url_gambar}" class='img img-fluid align-item-center'>
        `);
          $(".loader").hide();
        }, 2000);
      } else {
        $(".isi").html("Data tidak ditemukan.");
      }
    },
    error: function (error) {
      console.error("Error:", error.statusText);
    },
  });
}
function klikGambar(id, ket) {
  ambilGambar(id, ket);

  $("#gambarModal").modal("show");
}
