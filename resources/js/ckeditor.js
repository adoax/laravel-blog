require('../fileuploader/dist/jquery.fileuploader.min');

window.ClassicEditor = require('@ckeditor/ckeditor5-build-classic');


ClassicEditor
    .create(document.querySelector('#editor'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', "insertTable", '|', 'imageUpload',],
        language: 'fr',
        cloudServices: {
            tokenUrl: 'https://64331.cke-cs.com/token/dev/9GaBukhQ2jnW1rbWp5by4KW2knkf4VZlX8Nr8duh2DRieWXEs29eG4uSmyrq',
            uploadUrl: 'https://64331.cke-cs.com/easyimage/upload/'
        },
        image: {
            // You need to configure the image toolbar, too, so it uses the new style buttons.
            toolbar: ['imageTextAlternative', '|', 'imageStyle:alignLeft', 'imageStyle:full', 'imageStyle:alignRight',],
            resizeUnit: 'px',
            styles: [
                // This option is equal to a situation where no style is applied.
                'full',

                // This represents an image aligned to the left.
                'alignLeft',

                // This represents an image aligned to the right.
                'alignRight'
            ]
        }
    })
    .then(editor => {
    })
    .catch(error => {
        console.error(error);
    });


$(document).ready(function () {
    $('input.files').fileuploader({
        theme: 'default',
        limit: 1,
        dialogs: {
            // alert dialog
            alert: function(text) {
                return alert(text);
            },

            // confirm dialog
            confirm: function(text, callback) {
                confirm(text) ? callback() : null;
            }
        }
    });
});
