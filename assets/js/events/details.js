async function lazyLoadImages() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    const lazyBackgrounds = document.querySelectorAll('.slide-background[data-bg]');

    if ('IntersectionObserver' in window) {
        const lazyObserver = new IntersectionObserver(async function(entries, observer) {
            entries.forEach(async function(entry) {
                if (entry.isIntersecting) {
                    const lazyElement = entry.target;
                    if (lazyElement.tagName === 'IMG') {
                        lazyElement.src = lazyElement.getAttribute('data-src');
                        lazyElement.removeAttribute('data-src');
                    } else if (lazyElement.classList.contains('slide-background')) {
                        lazyElement.style.backgroundImage = `url('${lazyElement.getAttribute('data-bg')}')`;
                        lazyElement.removeAttribute('data-bg');
                    }
                    observer.unobserve(lazyElement);
                }
            });
        });

        lazyImages.forEach(async function(lazyImage) {
            lazyObserver.observe(lazyImage);
        });

        lazyBackgrounds.forEach(async function(lazyBackground) {
            lazyObserver.observe(lazyBackground);
        });
    } else {
        // Fallback para navegadores que não suportam IntersectionObserver
        lazyImages.forEach(async function(lazyImage) {
            lazyImage.src = lazyImage.getAttribute('data-src');
            lazyImage.removeAttribute('data-src');
        });

        lazyBackgrounds.forEach(async function(lazyBackground) {
            lazyBackground.style.backgroundImage = `url('${lazyBackground.getAttribute('data-bg')}')`;
            lazyBackground.removeAttribute('data-bg');
        });
    }
}

async function favoriteEvent() {
    $('#btn-favorite').on('click', function() {
        let icon = $(this);
        let child = $('#btn-favorite-icon');
        $.ajax({
            url: '/events/subscribe',
            type: 'POST',
            data: {
                eventId: eventId,
                status: child.attr('data-icon') == 'mdi:heart' ? true : false
            },
            success: async function(response) {
                response = JSON.parse(response);
                if (response.code == 200) {
                    icon.addClass('animate-grow-shrink');
                    if (response.data.subscribed) { 
                        if (child.attr('data-icon') == 'mdi:heart-outline') {
                            child.attr('data-icon', 'mdi:heart');
                            const eventTabs = $('#eventTabs');
                            eventTabs.css('display', 'flex');
                            // Trigger animation
                            setTimeout(() => {
                                eventTabs.removeClass('hide-with-animation').addClass('show-with-animation');
                            }, 10);
                        }
                    } else {
                        if (child.attr('data-icon') == 'mdi:heart') {
                            child.attr('data-icon', 'mdi:heart-outline');
                            const eventTabs = $('#eventTabs');
                            eventTabs.removeClass('show-with-animation').addClass('hide-with-animation');
                            // Voltar para a primeira aba
                            $('#home-tab').click();
                            // Hide after animation completes
                            setTimeout(() => {
                                eventTabs.css('display', 'none');
                            }, 400);
                        }
                    }
                    icon.on('animationend', function() {
                        icon.removeClass('animate-grow-shrink');
                    });
                } else {
                    if (response.code == 401) {
                        window.location.href = response.data.redirect;
                    }
                }
            },
            error: function(error) {
                console.log('An error occurred');
            }
        });

    });
}

function shareButtons() {
    $('#copyUrl').on('click', function() {
        navigator.clipboard.writeText(url).then(async function() {
            const toast = new bootstrap.Toast(document.getElementById('copyToast'));
            toast.show();
        }, function(err) {
            console.error('Erro ao copiar a URL: ', err);
        });
    }); 
}

function loadShareSlide() {
    // Aguarda o slick estar disponível
    if (typeof $ === 'undefined' || typeof $.fn.slick === 'undefined') {
        setTimeout(loadShareSlide, 100);
        return;
    }
    
    $('.share-carousel').slick({
        slidesToShow: 4,
        slidesToScroll: 4,
        infinite: false,
        arrows: true,
        prevArrow: '<button type="button" class="slick-prev">Previous</button>',
        nextArrow: '<button type="button" class="slick-next">Next</button>',
        responsive: [
            {
              breakpoint: 768,
              settings: {
                slidesToShow: 3,
                slidesToScroll: 1,
              },
            },
            {
              breakpoint: 576,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 1,
              },
            },
        ],
    });
}

document.addEventListener('DOMContentLoaded', async function() {
    lazyLoadImages();
    favoriteEvent();
    new QRCode(document.getElementById("qrcode"), {
        text: url,
        width: 200,
        height: 200
    });
    $('#btn-share').on('click', function() {
        $('#shareModal').modal('show');
    });
    $('#shareModal').on('shown.bs.modal', function () {
        loadShareSlide();
        shareButtons();
    });
});


document.querySelectorAll('.modal').forEach(async function(modal) {
    modal.addEventListener('shown.bs.modal', function() {
        document.querySelectorAll('.your-class').forEach(async function(element) {
            $(element).slick('setPosition');
        });
        document.querySelectorAll('.wrap-modal-slider').forEach(async function(element) {
            element.classList.add('open');
        });
    });
});

const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
document.addEventListener('click', function(event) {
    popoverTriggerList.forEach(function(popoverTriggerEl) {
        const popover = bootstrap.Popover.getInstance(popoverTriggerEl);
        if (popover) {
            if (!popoverTriggerEl.contains(event.target) && !event.target.closest('.popover')) {
                popover.hide();
            }
        }
    });
});

$('[data-dateId]').on('click', function() {
    var dateId = $(this).data('dateid');
    var date = datesWithObservations[dateId];
    $('#dateObservationModalLabel').text(date['evento_data_dia']);
    $('#dateObservationContent').html(date['evento_data_observacao'].replace(/\n/g, '<br>'));
    $('#dateStartHour').text(date['evento_data_hora_inicial']);
    if (date['evento_data_hora_final'])
        $('#dateEndHour').text(' - ' + date['evento_data_hora_final']);
    $('#dateObservationModal').modal('show');
});

$('[data-priceId]').on('click', function() {
    var priceId = $(this).data('priceid');
    var price = pricesWithObservations[priceId];
    $('#priceObservationModalLabel').text(price['evento_taxa_titulo']);
    $('#priceObservationContent').html(price['evento_taxa_observacao'].replace(/\n/g, '<br>'));
    $('#priceValue').text('R$ '+price['evento_taxa_valor']);
    $('#priceObservationModal').modal('show');
});

$('.slide-item').on('click', async function() {
    $('#imageModal').find('img').attr('src', $(this).attr('src'));
    $('#imageModal').modal('show');
});

$('#inputUserTags').select2({
    placeholder: translator.tagsPlaceholder,
    allowClear: true,
    tags: true,
    // dropdownParent: $('#newModal .modal-body'),
    width: '100%',
    language: {
        noResults: function() {
            return translator.tagsLanguage;
        }
    }
});

$('#form-userSubscription').on('submit', function(e) {
    e.preventDefault();
    const formData = $(this).serializeArray();
    formData.push({ name: 'eventId', value: eventId });
    $.ajax({
        url: '/events/update-subscription',
        type: 'POST',
        data: formData,
        success: async function(response) {
            response = JSON.parse(response);
            if (response.code == 200) {
                $('#toastTitle').text(translator.saveSuccessTitle);
                $('#toastBody').text(response.message);
                $('#myToast').removeClass('bg-danger')
                $('#myToast').addClass('bg-success');
            } else if (response.code == 401) {
                window.location.href = response.data.redirect;
            } else {
                $('#toastTitle').text(translator.errorTitle);
                $('#toastBody').text(response.message);
                $('#myToast').removeClass('bg-success')
                $('#myToast').addClass('bg-danger');
            }
            var myToast = new bootstrap.Toast(document.getElementById('myToast'));
            myToast.show();
        },
        error: function(error) {
            console.log('An error occurred');
        }
    });
});



