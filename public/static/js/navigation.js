$(document).ready(function () {
    var noAutoCollapsing = false;

    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };

    if (isMobile.any()) {
        $('body').addClass('mobile-device');
    }

    /**************************************/
    /* run this function if window resize */
    /**************************************/

    var widthLess1024 = function(){
        if ($(window).width() < 1024) {
            //make sidebar collapsed
            $('#sidebar, #navbar').addClass('collapsed');
            $('#navigation').find('.dropdown.open').removeClass('open');
            $('#navigation').find('.dropdown-menu.animated').removeClass('animated');

            //move content if navigation is collapsed
            if ($('#sidebar').hasClass('collapsed')) {
                $('#router-view').animate({left: "0px",paddingLeft: "55px"},150);
            } else {
                $('#router-view').animate({paddingLeft: "55px"},150);
            };
        }

        else {
            //make navigation not collapsed
            $('#sidebar, #navbar').removeClass('collapsed');

            //move content if navigation is not collapsed
            if ($('#sidebar').hasClass('collapsed')) {
                $('#router-view').animate({left: "210px",paddingLeft: "265px"},150);
            } else {
                $('#router-view').animate({paddingLeft: "265px"},150);
            };
        }

    };

    var widthLess768 = function(){
        if ($(window).width() < 768) {
            //paste top navbar objects to sidebar
            if($('.collapsed-content .search').length === 1) {
                $('#main-search').appendTo('.collapsed-content .search');
            }
            if($('.collapsed-content li.user').length === 0) {
                $( ".collapsed-content li.search" ).after($( "#current-user" ));
            }
        }

        else {
            //show content of top navbar
            $('#current-user').show();

            //remove top navbar objects from sidebar
            if($('.collapsed-content .search').length === 2) {
                $( ".nav.refresh" ).after($( "#main-search" ));
            }
            if($('.collapsed-content li.user').length === 1) {
                $( ".quick-actions >li:last-child" ).before($( "#current-user" ));
            }
        }
    };


    if (isMobile.any()) {
        $('body').addClass('mobile-device');
    }

    /************************************************/
    /* ADD ANIMATION TO TOP MENU & SUBMENU DROPDOWN */
    /************************************************/

    $('.quick-actions .dropdown').on('show.bs.dropdown', function(e){
        $(this).find('.dropdown-menu').addClass('animated fadeInDown');
        $(this).find('#user-inbox').addClass('animated bounceIn');
    });

    $('#navigation .dropdown').on('show.bs.dropdown', function(e){
        $(this).find('.dropdown-menu').addClass('animated fadeInLeft');
    });


    /****************************/
    /* SIDEBAR PARTS COLLAPSING */
    /****************************/

    $('#sidebar .sidebar-toggle').on('click', function(){
        var target = $(this).data('toggle');

        $(target).toggleClass('collapsed');
    });

    /*********************************/
    /* INITIALIZE SIDEBAR NICESCROLL */
    /*********************************/

    if (!isMobile.any()) {
/*
        $("#sidebar").niceScroll({
            cursorcolor: '#000000',
            zindex: 999999,
            bouncescroll: true,
            cursoropacitymax: 0.4,
            cursorborder: '',
            cursorborderradius: 0,
            cursorwidth: '7px',
            railalign: 'left',
            railoffset: {top:45,left:0}
        });
*/
    }

    /*****************************/
    /* INITIALIZE MAIN NICESCROLL*/
    /*****************************/


    if (!isMobile.any()) {

        var initContentScroll = function () {
/*
            $("#router-view").niceScroll({
                cursorcolor: '#000000',
                zindex: 999999,
                bouncescroll: true,
                cursoropacitymax: 0.4,
                cursorborder: '',
                cursorborderradius: 7,
                cursorwidth: '7px',
                background: 'rgba(0,0,0,.1)',
                autohidemode: false,
                railpadding: {top: 0, right: 2, left: 2, bottom: 0}
            });
*/
        };

        initContentScroll();
    } else {
        $('#router-view').css({overflow: 'auto'})
    }

    $('#mmenu').on(
        "opened.mm",
        function()
        {
            $("#router-view").getNiceScroll().hide();
        }
    );

    $('#mmenu').on(
        "closed.mm",
        function()
        {
            $("#router-view").getNiceScroll().show();
        }
    );

    $('.modal')
        .on('show.bs.modal', function(){
            $('body, #router-view').css({overflow: 'hidden'});
            $("#router-view").getNiceScroll().remove();
        })
        .on('hide.bs.modal', function(){
            $('body, #router-view').css({overflow: ''});
            initContentScroll();
        });

    /************************************/
    /* SIDEBAR MENU DROPDOWNS FUNCTIONS */
    /************************************/

    $('#navigation .dropdown.open').data('closable', false);

    $('#navigation .menu >.dropdown').on({
        "shown.bs.dropdown": function() {
            $(this).data('closable', false);
            // resize scrollbar
            $("#sidebar").getNiceScroll().resize();
        },
        "click": function(e) {

            $(this).data('closable', true);

            if (!$(this).hasClass('open')) {
                $('li.dropdown.open').removeClass('open');
            }

            if ($('#sidebar').hasClass('collapsed')) {
                // Avoid having the menu to close when clicking
                e.stopPropagation();
            }

            // resize scrollbar
            $("#sidebar").getNiceScroll().resize();

        },
        "hide.bs.dropdown": function() {
            return $(this).data('closable');
            // resize scrollbar
            $("#sidebar").getNiceScroll().resize();
        }
    });

    /*******************************/
    /* SIDEBAR COLLAPSING FUNCTION */
    /*******************************/

    $('.sidebar-collapse a').on('click', function(){
        noAutoCollapsing = true;

        // Add or remove class collapsed
        $('#sidebar, #navbar').toggleClass('collapsed');

        $('#navigation').find('.dropdown.open').removeClass('open');
        $('#navigation').find('.dropdown-menu.animated').removeClass('animated');
        $('#sidebar > li.collapsed').removeClass('collapsed');

        if (!isMobile.any()) {
            if ($('#sidebar').hasClass('collapsed')) {
                if ($(window).width() < 1024) {
                    //if width is less than 1024px move content to left 0px
                    $('#router-view').animate({left: "0px"},150)
                }
                else {
                    //if width is not less than 1024px give padding 55px to content
                    $('#router-view').animate({paddingLeft: "55px"},150)
                }

            } else {

                if ($(window).width() < 1024) {
                    //if width is less than 1024px move content to left 210px
                    $('#router-view').animate({left: "210px"},150)
                }
                else {
                    //if width is not less than 1024px give padding 265px to content
                    $('#router-view').animate({paddingLeft: "265px"},150)
                }
            }
        } else {
            if ($('#sidebar').hasClass('collapsed')) {
                $('#router-view').css({paddingLeft: "55px", display: 'block'})
            } else {
                $('#router-view').css({paddingLeft: "265px", display: 'none'})
            }
        }

    });

    /**************************/
    /* SIDEBAR CLASS TOGGLING */
    /**************************/

    $('#navigation .menu li').hover(function(){
        $(this).addClass('hovered');
        $("#sidebar").addClass('open');
    }, function(e){
        $(e.target).parent().removeClass('hovered');
        $(this).removeClass('hovered');
        $("#sidebar").removeClass('open');
    });

    /**************************************/
    /* run this function after page ready */
    /**************************************/

    widthLess1024();
    widthLess768();

    /***************************************/
    /* run this functions if window resize */
    /***************************************/

    $(window).resize(function() {
        widthLess1024();
        widthLess768();
    });

    /**************/
    /* ANIMATIONS */
    /**************/

    //animate numbers with class .animate-number with data-value attribute
    $(".animate-number").each(function() {
        var value = $(this).data('value');
        var duration = $(this).data('animation-duration');

        $(this).animateNumbers(value, true, duration, "linear");
    });

    //animate progress bars
    $('.animate-progress-bar').each(function(){
        var progress =  $(this).data('percentage');

        $(this).css('width', progress);
    })
});