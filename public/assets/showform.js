$(document).ready(function () {
  /* $.ajaxSetup({
    'beforeSend': function() {
        var showLoading = function() {
            Swal.fire({
                html: 'Mohon tunggu...',
                allowOutsideClick: false,
                showConfirmButton: false,
                imageUrl: ""
            });
        }
        showLoading();
    }
  }); */
  $("body").on("click", ".add-form", function () {
    $.ajaxSetup({
      "type": "post"
    });
    if ($(this).attr("data-url")) {
      $("#" + $(this).attr("data-target") + "").load($(this).attr("data-url"));
    }
  });

  $("body").on("click", ".btn-refresh", function () {
    location.reload();
  })

  $(".btn-deleteChecked").click(function (event) {
    event.preventDefault();
    var searchIDs = $("#" + $(this).attr("data-table") + " input:checkbox:checked").map(function () {
      return $(this).val();
    }).toArray();
    if (searchIDs.length == 0) {
      SweetAlert("", "Mohon cek list data yang akan dihapus", "error");
      return false;
    }
    var url = "" + $(this).attr("data-url") + "";
    confirmForm("Hapus Data?", function (confirmed) {
      if (confirmed) {
        $.post(url, {
          data: searchIDs
        }, (resp) => {
          swal({
            title: "Sukses!",
            text: resp.message,
            type: "success"
          }).then(function () {
            location.reload();
          });
        }, 'json');
      }
    })
  });

  $("body").on("click", ".cancel-form", function () {
    location.reload();
  });
})
function delete_row(row) {
  Swal.fire({
    title: 'Hapus Data?',
    text: "Data yang terhapus tidak dapat dikembalikan lagi",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes'
  }).then((result) => {
    if (result.value) {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': "" + $(row).attr("x-token") + ""
        }
      });
      $.ajax({
        'type': "delete",
        'url': "" + $(row).attr("data-url") + "",
        'dataType': 'json',
        'success': function (data) {
          if (data.success) {
            Swal.fire("Sukses!", data.message, "success").then(() => {
                location.reload();
            });
          } else {
            Swal.fire("Oopss...!!!", data.message, "error");
          }
        }
      });
    }
  })
  return false;
}
function confirmForm(txt,callback,body) {
  Swal.fire({ title: txt,text:body, type: "question", showCancelButton: !0,cancelButtonColor:"#d33",cancelButtonText:"No",confirmButtonText:"Yes" }).then(function (t) {
      callback(t && t.value == true);
  });
}

function set_edit(row) {
  let method = "put";
  if ($(row).attr("data-method")) {
    method = $(row).attr("data-method");
  }
  console.log(method);
  $.ajaxSetup({
    "type": method,
    "url": $(row).attr("ajax-url")
  });
  if ($(row).attr("data-url")) {
    $("#" + $(row).attr("data-target") + "").load($(row).attr("data-url"));
  }
}