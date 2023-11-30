$(function() {

  showSwal = function (data) {
    const { type, text, confirmButtonText, link } = data;
    'use strict'
      if (type === 'basic') {
        swal.fire({
          text: text ?? 'Any fool can use a computer',
          confirmButtonText: 'Close',
          confirmButtonClass: 'btn btn-danger',
        })
      } else if (type === 'title-and-text') {
        Swal.fire(
          'The Internet?',
          'That thing is still around?',
          'question'
        )
      } else if (type === 'title-icon-text-footer') {
        Swal.fire({
          type: 'error',
          title: 'Oops...',
          text: 'Something went wrong!',
          footer: '<a href>Why do I have this issue?</a>'
        })
      } else if (type === 'custom-html') {
        Swal.fire({
          title,
          icon: 'info',
          html,
          showCloseButton: true,
          showCancelButton: true,
          focusConfirm: false,
          confirmButtonText: '<i class="fa fa-thumbs-up"></i> Great!',
          confirmButtonAriaLabel: 'Thumbs up, great!',
          cancelButtonText: '<i data-feather="thumbs-up"></i>',
          cancelButtonAriaLabel: 'Thumbs down',
        });
        // feather.replace();
      } else if (type === 'custom-position') {
        Swal.fire({
          position: 'top-end',
          icon: 'success',
          title: 'Your work has been saved',
          showConfirmButton: false,
          timer: 1500
        })
      } else if (type === 'passing-parameter-execute-cancel') {
        // evt.preventdefault();
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger mr-2'
          },
          buttonsStyling: false,
        });
        swalWithBootstrapButtons.fire({
          title: 'Are you sure?',
          text: text ?? "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonClass: 'me-2',
          confirmButtonText: confirmButtonText ?? 'Yes, delete it!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            if (link) window.location = link;
            // let target = $(`#${evt.id}`);
            // var id = target.attr('id');
            // var url = target.data('url');
            // let token = $('meta[name="_token"]').attr('content')
            // var data = {
            //   id: id,
            //   _token: token
            // };
            // $.ajax({
            //   url,
            //   type: 'delete',
            //   data: data,
            //   success: function (data) {
            //     if (data.status == '200' && data.error == false) {
            //       console.log(data);
            //       $(`#${id}`).closest('tr').remove();
            //       swalWithBootstrapButtons.fire(
            //         'Deleted!',
            //         data?.message || "Successfully Deleted",
            //         'success'
            //       )
            //     }
            //   },
            //   error: function (data) {
            //     console.log(data);
            //     if ((data.status == '400' || data.status == '500') && data?.responseJSON?.error == true) {
            //       swalWithBootstrapButtons.fire(
            //         'Service Not Delete!',
            //         data?.responseJSON?.message || "Something went wrong!",
            //         'error'
            //       )
            //     }
            //   }
            // });
          } else if (
            // Read more about handling dismissals
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire(
              'Cancelled',
              'Your imaginary file is safe :)',
              'error'
            )
          }
        });
      } else if (type === 'message-with-auto-close') {
        let timerInterval
        Swal.fire({
          title: 'Auto close alert!',
          html: 'I will close in <b></b> milliseconds.',
          timer: 2000,
          timerProgressBar: true,
          didOpen: () => {
            Swal.showLoading()
            timerInterval = setInterval(() => {
              const content = Swal.getHtmlContainer()
              if (content) {
                const b = content.querySelector('b')
                if (b) {
                  b.textContent = Swal.getTimerLeft()
                }
              }
            }, 100)
          },
          willClose: () => {
            clearInterval(timerInterval)
          }
        }).then((result) => {
          /* Read more about handling dismissals below */
          if (result.dismiss === Swal.DismissReason.timer) {
            console.log('I was closed by the timer')
          }
        })
      } else if (type === 'message-with-custom-image') {
        Swal.fire({
          title: 'Sweet!',
          text: 'Modal with a custom image.',
          // imageUrl: 'https://unsplash.it/400/200',
          imageUrl: '/assets/images/others/placeholder.jpg',
          imageWidth: 400,
          imageHeight: 200,
          imageAlt: 'Custom image',
        })
      } else if (type === 'mixin') {
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        });
        
        Toast.fire({
          icon: 'success',
          title: 'Signed in successfully'
        })
      }
    }
  });