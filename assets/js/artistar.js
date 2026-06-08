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