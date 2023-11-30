function cropImage(obj, file, imageView, cb) {

   var image = document.getElementById('cropperimage');
   var modal = $('#modal');
   var cropper;
 
   var done = function(url) {
      obj.val('');
      image.src = url;
      modal.modal('show');
   };
   var reader;

   if (URL) {
         done(URL.createObjectURL(file));
   } else if (FileReader) {
         reader = new FileReader();
         reader.onload = function(e) {
            done(reader.result);
         };
         reader.readAsDataURL(file);
   }

   $('button[data-dismiss="modal"]').on('click', function() {

      $(this).closest('div[role="dialog"]').modal('hide');

      $('.modal').on("hidden.bs.modal", function (e) { 
         if ($('.modal:visible').length) { 
             $('body').addClass('modal-open');
         }
      });

   });
 
   modal.off().on('shown.bs.modal', function() { 
      cropper = new Cropper(image, {
         dragMode: 'move'
      });
   })
   
   modal.on('hidden.bs.modal', function() {
      cropper.destroy();
      cropper = null;
   });
 
   $('#crop').off().on('click', function() { 
      var loc = window.location; 
      var pathName = loc.toString().split("/"); 
      var canvas;

      modal.modal('hide');

      $('.modal').on("hidden.bs.modal", function (e) { 
         if ($('.modal:visible').length) { 
             $('body').addClass('modal-open');
         }
      });

      if (cropper) {
         //console.log(cropper.getCroppedCanvas({ width: 800, height: 800 }));
         if(pathName[4]=='promocode'){

            if(cropper.getCroppedCanvas({ width: 400, height: 200 }) != null) {
               canvas = cropper.getCroppedCanvas({
                  width: 400,
                  height: 200,
               });
               imageView.attr('src', canvas.toDataURL());
               canvas.toBlob(function(blob) {
                  cb(blob);
               });
            }

         }else{
            if(cropper.getCroppedCanvas({ width: 800, height: 800 }) != null) {
               canvas = cropper.getCroppedCanvas({
                  width: 800,
                  height: 800,
               });
         
               imageView.attr('src', canvas.toDataURL());
               canvas.toBlob(function(blob) {
                  cb(blob);
               });
            }
         }
      }
   });
}