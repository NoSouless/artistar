function atualizarToast(toast, title, body, isSuccess = true, ) {
    $('#toastTitle').text(title);
    $('#toastBody').text(body);
    //remove class bg-success
    if (isSuccess) {
        $('#'+toast).removeClass('bg-danger');
        $('#'+toast).addClass('bg-success');
    } else {
        $('#'+toast).removeClass('bg-success');
        $('#'+toast).addClass('bg-danger');
    }
    var myToast = new bootstrap.Toast(document.getElementById(toast));
    myToast.show();
}

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
class ToastManager {
    titleId = 'toastTitle';
    bodyId = 'toastBody';
    toastElement = null;
    delay = 3000;

    constructor(toastId, title = 'toastTitle', body = 'toastBody') {
        this.toastId = toastId;
        this.titleId = title;
        this.bodyId = body;
        this.toastElement = new bootstrap.Toast(document.getElementById(toastId));
        $('#'+toastId).attr('data-bs-delay', this.delay);
    }

    showSuccess(title, body) {
        $('#'+this.titleId).html(title);
        $('#'+this.bodyId).html(body);
        $('#'+this.toastId).removeClass('bg-danger');
        $('#'+this.toastId).addClass('bg-success');
        this.toastElement.show();
    }

    showError(title, body) {
        $('#'+this.titleId).html(title);
        $('#'+this.bodyId).html(body);
        $('#'+this.toastId).removeClass('bg-success');
        $('#'+this.toastId).addClass('bg-danger');
        this.toastElement.show();
    }
}

