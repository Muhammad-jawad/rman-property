(function ($) {
    'use strict';

    $(document).ready(function () {
        let currentPage = 1;

        $('#rman_load-more').on('click', function () {

            currentPage++; // Increment currentPage, as we want to load the next page

            $.ajax({

                type: 'POST',
                url: url.ajaxurl,
                dataType: 'json',
                data: {
                    action: 'rman_load_more',
                    no_of_posts: $(this).data('no-posts'),
                    paged: currentPage
                },

                beforeSend: function () {
                    // Setting a loading text before the AJAX request is completed
                    $(".property__button.load-more a").text("Loading...");
                },
                success: function (res) {
                    // Reset the text to the original after AJAX request is completed
                    $(".property__button.load-more a").text("Load more");

                    if (currentPage >= res.max) {
                        // Hide the load more button if there are no more pages
                        $('.property__button.load-more').hide();
                    }

                    // Append the response HTML to the container
                    $('.property__grid').append(res.html);
                }

            });

        });


        // Magnific popup js
        $('#ma_floorplans').on('click', function() {
            $.magnificPopup.open({
              items: {
                src: '#floorplan-gallery',
              },
              type: 'inline',
              gallery: {
                enabled: true
              }
            });
        });

        $('#ma_epc-action').on('click', function() {
            $.magnificPopup.open({
              items: {
                src: '#epc-gallery',
              },
              type: 'inline',
              gallery: {
                enabled: true
              }
            });
        });

        $('#ma_enquiryform').on('click', function() {
            $.magnificPopup.open({
              items: {
                src: '#ma-popup-form',
              },
              type: 'inline',
            });
        });

        //  Initialize Swiper for image galleries
        (function() {
            // Initialize main product carousel
            const carousel_product_thumbs = new Swiper('.carousel-properties-thumbs', {
                // Swiper options for thumbnail carousel
                loop: false,
                spaceBetween: 30,
                breakpoints: {
                    1: {
                        slidesPerView: 2,
                    },
                    540: {
                        slidesPerView: 3,
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                    1400: {
                        slidesPerView: 4,
                    },
                },
                watchSlidesProgress: true,
            });
            const carousel_product = new Swiper('.carousel-properties', {
                // Swiper options for main image carousel
                loop: false,
                navigation: {
                    nextEl: '.carousel-properties .swiper-nav-next',
                    prevEl: '.carousel-properties .swiper-nav-prev',
                },
                spaceBetween: 30,
                slidesPerView: 1,
                thumbs: {
                    swiper: carousel_product_thumbs,
                }
            });
        })();



        const printButton = document.getElementById('printButton');
        const mainContent = document.getElementById('fl-main-content');
        
        printButton.addEventListener('click', () => {
            const printWindow = window.open('', '_blank');
            const printDocument = printWindow.document;
        
            // Add linked stylesheets
            const stylesheets = Array.from(document.querySelectorAll('link[rel="stylesheet"]')).map(link => link.outerHTML).join('');
        
            // Add linked scripts
            const scripts = Array.from(document.querySelectorAll('script[src]')).map(script => script.outerHTML).join('');
        
            // Write the print window content
            printDocument.open();
            printDocument.write('<html><head><title>Print Property</title>');
            printDocument.write(stylesheets);
            printDocument.write('<style>');
            printDocument.write('.swiper-container.carousel-properties {max-height: 650px;}');
            printDocument.write('.swiper-container.carousel-properties-thumbs {max-height: 250px; }');
            printDocument.write('</style>');
            printDocument.write('</head><body>');
            printDocument.write(mainContent.outerHTML);
            printDocument.write(scripts);
            printDocument.write('</body></html>');
            printDocument.close();
            
            
            // Add a delay before printing
            setTimeout(() => {
                printWindow.print();
            }, 1000); // Change the delay time (in milliseconds) as needed
        });
          
    });

})(jQuery);



document.addEventListener('DOMContentLoaded', function() {
    const wishListButton = document.getElementById('ma_wishlist-button');
    const wishlistContainer = document.querySelector('.ma-wishlist-container');
    const wishlistIcon = document.querySelector('.wishlist-icon');
    const wishlistLists = document.querySelector('.wishlist-lists');
    const wishlistCount = document.querySelector('.wishlist-count');
    const pageTitle = document.title;


    let savedWishlist = JSON.parse(localStorage.getItem('ma-wishlist')) || [];
    let isWishlistVisible = false;

    showAndHideLocalStorage();

    // Initial display update
    updateWishlistDisplay();


    wishlistIcon.addEventListener('click', function() {
        isWishlistVisible = !isWishlistVisible;
        if (isWishlistVisible) {
            wishlistLists.classList.add('visible');
        } else {
            wishlistLists.classList.remove('visible');
        }
    });

    wishListButton.addEventListener('click', function() {
        const productName = 'Property Name'; // Change this as needed
        const pageLink = window.location.href;
        const pageTitle = document.title;

        if (!isTitleInWishlist(pageTitle)) {
            saveToLocalStorage(productName, pageLink, pageTitle);
            showAndHideLocalStorage();
            updateWishlistDisplay();
        }
    });

    function isTitleInWishlist(title) {
        return savedWishlist.some(item => item.title === title);
    }

    function saveToLocalStorage(productName, pageLink, pageTitle) {
        const wishlistItem = { title: pageTitle, link: pageLink };
        savedWishlist.push(wishlistItem);
        localStorage.setItem('ma-wishlist', JSON.stringify(savedWishlist));
    }

    function showAndHideLocalStorage() {
       
        if (savedWishlist.length > 0) {
            wishlistContainer.style.display = 'block';
            wishlistCount.textContent = savedWishlist.length;
            
        } else {
            wishlistContainer.style.display = 'none';
            wishlistCount.textContent = '';
        }
        if(wishListButton)
        {
            const icon = wishListButton.querySelector('i');
            if (isTitleInWishlist(pageTitle)) {
                icon.classList.remove("fa-regular");
                icon.classList.add("fa");
            }
            else {
                icon.classList.add("fa-regular");
                icon.classList.remove("fa");
            }
        }
    }

    function isTitleInWishlist(title) {
        return savedWishlist.some(item => item.title === title);
    }

    function updateWishlistDisplay() {
        wishlistLists.innerHTML = ''; // Clear the existing content

        savedWishlist.forEach((item, index) => {
            const listItem = document.createElement('div');
            listItem.classList.add('wishlist-item');
            listItem.innerHTML = `
                <a href="${item.link}"><span>${item.title}</span></a>
                <button class="delete-button" data-index="${index}">X</button>
            `;
            wishlistLists.appendChild(listItem);
        });

        const deleteButtons = document.querySelectorAll('.delete-button');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                savedWishlist.splice(index, 1);
                localStorage.setItem('ma-wishlist', JSON.stringify(savedWishlist));
                showAndHideLocalStorage();
                updateWishlistDisplay();
            });
        });
    }

    
});


document.addEventListener('DOMContentLoaded', function() {
    let shareButtons = document.querySelectorAll('.social-share-buttons button');

    const toggleButton = document.getElementById('ma_social-share');
    const shareButtonsParent = document.querySelector('.social-share-buttons');

    shareButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            let platform = this.getAttribute('data-share');
            let url = this.getAttribute('data-url');
            let title = this.getAttribute('data-title');
            
            switch (platform) {
                case 'facebook':
                    window.open('https://www.facebook.com/sharer/sharer.php?u=' + url, 'Facebook Share', 'width=600,height=400');
                    break;
                case 'twitter':
                    window.open('https://twitter.com/intent/tweet?url=' + url + '&text=' + title, 'Twitter Share', 'width=600,height=400');
                    break;
                case 'linkedin':
                    window.open('https://www.linkedin.com/shareArticle?url=' + url + '&title=' + title, 'LinkedIn Share', 'width=600,height=400');
                    break;
                case 'pinterest':
                    window.open('https://pinterest.com/pin/create/button/?url=' + url + '&description=' + title, 'Pinterest Share', 'width=600,height=400');
                    break;
                case 'whatsapp':
                    window.open('https://api.whatsapp.com/send?text=' + title + ' ' + url, 'WhatsApp Share', 'width=600,height=400');
                    break;
                case 'gmail':
                    window.location.href = 'mailto:?subject=' + title + '&body=' + url;
                    break;
                // Add cases for other platforms here if required
            }
        });
    });

    


    toggleButton.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default action of the link
        shareButtonsParent.classList.toggle('visible');
    });
});