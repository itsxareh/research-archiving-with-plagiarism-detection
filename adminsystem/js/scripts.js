(function($) {
    "use strict";


    $(function () {
        for (var nk = window.location, o = $(".nano-content li a").filter(function () {
            return this.href == nk;
        })
            .addClass("active")
            .parent()
            .addClass("active"); ;) {
            if (!o.is("li")) break;
            o = o.parent()
                .addClass("d-block")
                .parent()
                .addClass("active");
        }
    });


    /* 
    ------------------------------------------------
    Sidebar open close animated humberger icon
    ------------------------------------------------*/

    $(".hamburger").on('click', function() {
        $(this).toggleClass("is-active");
    });





    /* TO DO LIST 
    --------------------*/
    $(".tdl-new").on('keypress', function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) {
            var v = $(this).val();
            var s = v.replace(/ +?/g, '');
            if (s == "") {
                return false;
            } else {
                $(".tdl-content ul").append("<li><label><input type='checkbox'><i></i><span>" + v + "</span><a href='#' class='ti-close'></a></label></li>");
                $(this).val("");
            }
        }
    });


    $(".tdl-content a").on("click", function() {
        var _li = $(this).parent().parent("li");
        _li.addClass("remove").stop().delay(100).slideUp("fast", function() {
            _li.remove();
        });
        return false;
    });

    // for dynamically created a tags
    $(".tdl-content").on('click', "a", function() {
        var _li = $(this).parent().parent("li");
        _li.addClass("remove").stop().delay(100).slideUp("fast", function() {
            _li.remove();
        });
        return false;
    });


    let currentOpenDropdown = null;

    const closeAllDropdowns = () => {
        document.querySelectorAll(".dropdown-action").forEach((dropdown) => {
            dropdown.classList.remove('active');
        });
        currentOpenDropdown = null;
    };

    document.addEventListener("click", function(event) {
        if (!event.target.closest('.action-button') && !event.target.closest('.dropdown-action')) {
            closeAllDropdowns();
            return;
        }

        const actionButton = event.target.closest('.action-button');
        if (actionButton) {
            event.stopPropagation();

            const studentId = actionButton.id.split("_")[1];
            const dropdown = document.getElementById(`dropdown_${studentId}`);

            if (dropdown) {
                if (currentOpenDropdown === dropdown) {
                    dropdown.classList.remove('active');
                    currentOpenDropdown = null;
                } else {
                    closeAllDropdowns();
                    dropdown.classList.add('active');
                    currentOpenDropdown = dropdown;
                }
            }
        }
    });
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAllDropdowns();
        }
    });
    


})(jQuery);