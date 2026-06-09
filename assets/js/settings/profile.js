$(document).ready(function() {
    function initImageDropArea(areaSelector, inputSelector, textSelector) {
        const $area = $(areaSelector);
        const $input = $(inputSelector);
        const $text = $(textSelector);

        if (!$area.length || !$input.length) {
            return;
        }

        $area.on('click', function(e) {
            e.stopPropagation();
            $input.trigger('click');
        });

        $area.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $area.addClass('dragover');
        });

        $area.on('dragleave dragend', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $area.removeClass('dragover');
        });

        $area.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $area.removeClass('dragover');
            if (e.originalEvent.dataTransfer.files.length) {
                $input[0].files = e.originalEvent.dataTransfer.files;
                showPreview($input[0].files[0]);
            }
        });

        $input.on('change', function() {
            if (this.files && this.files[0]) {
                showPreview(this.files[0]);
            }
        });

        function showPreview(file) {
            const fileSize = file.size / 1024 / 1024;
            if (fileSize > 5) {
                alert($input.attr('data-size-error'));
                $input.val('');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                $area.find('img').remove();
                $area.append('<img src="' + event.target.result + '" alt="Preview">');
                if ($text.length) {
                    $text.hide();
                }
            };
            reader.readAsDataURL(file);
        }
    }

    initImageDropArea('#profile-photo-drop-area', '#profile-photo-input', '#profile-photo-drop-text');

    $(document).on('submit', '#settings-profile-form', function(event) {
        event.preventDefault();

        var form = this;
        var formData = new FormData(form);

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.code == 200) {
                $('#toastTitle').text($('#settings-profile-form').attr('data-success'));
                $('#toastBody').text(response.message);
                $('#myToast').removeClass('bg-danger').addClass('bg-success');
                var myToast = new bootstrap.Toast(document.getElementById('myToast'));
                myToast.show();
            } else {
                $('#toastTitle').text($('#settings-profile-form').attr('data-error'));
                $('#toastBody').text(response.message || 'Erro ao salvar as alterações.');
                $('#myToast').removeClass('bg-success').addClass('bg-danger');
                var myToast = new bootstrap.Toast(document.getElementById('myToast'));
                myToast.show();
            }
        }).fail(function() {
            $('#toastTitle').text($('#settings-profile-form').attr('data-error'));
            $('#toastBody').text('Erro ao salvar as alterações.');
            $('#myToast').removeClass('bg-success').addClass('bg-danger');
            var myToast = new bootstrap.Toast(document.getElementById('myToast'));
            myToast.show();
        });
    });
});