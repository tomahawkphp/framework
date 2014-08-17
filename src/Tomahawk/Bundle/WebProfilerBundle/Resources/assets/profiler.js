(function(jQuery){
    var axe = {
        // Sandbox a jQuery instance for the profiler.
        jq: jQuery.noConflict(true)
    };

    axe.jq.extend(axe, {

        // BOUND ELEMENTS
        // -------------------------------------------------------------
        // Binding these elements early, stops jQuery from "querying"
        // the DOM every time they are used.

        el: {
            main: axe.jq('.axe-profiler'),
            close: axe.jq('#axe-profiler-close'),
            zoom: axe.jq('#axe-profiler-zoom'),
            hide: axe.jq('#axe-profiler-hide'),
            show: axe.jq('#axe-profiler-show'),
            tab_pane: axe.jq('.axe-profiler-tab-pane'),
            hidden_tab_pane: axe.jq('.axe-profiler-tab-pane:visible'),
            tab: axe.jq('.axe-profiler-tab'),
            tabs: axe.jq('.axe-profiler-tabs'),
            tab_links: axe.jq('.axe-profiler-tabs a'),
            window: axe.jq('.axe-profiler-window'),
            closed_tabs: axe.jq('#axe-profiler-closed-tabs'),
            open_tabs: axe.jq('#axe-profiler-open-tabs'),
            content_area: axe.jq('.axe-profiler-content-area')
        },

        // CLASS ATTRIBUTES
        // -------------------------------------------------------------
        // Useful variable for Axe Profiler.

        // is axe in full screen mode
        is_zoomed: false,

        // initial height of content area
        small_height: axe.jq('.axe-profiler-content-area').height(),

        // the name of the active tab css
        active_tab: 'axe-profiler-active-tab',

        // the data attribute of the tab link
        tab_data: 'data-axe-profiler-tab',

        // size of axe when compact
        mini_button_width: '2.6em',

        // is the top window open?
        window_open: false,

        // current active pane
        active_pane: '',

        // START()
        // -------------------------------------------------------------
        // Sets up all the binds for Anbu!

        start: function() {

            // hide initial elements
            axe.el.close.css('visibility', 'visible').hide();
            axe.el.zoom.css('visibility', 'visible').hide();
            axe.el.tab_pane.css('visibility', 'visible').hide();

            // bind all click events
            axe.el.close.click(function(event) {
                axe.close_window();
                event.preventDefault();
            });
            axe.el.hide.click(function(event) {
                axe.hide();
                event.preventDefault();
            });
            axe.el.show.click(function(event) {
                axe.show();
                event.preventDefault();
            });
            axe.el.zoom.click(function(event) {
                axe.zoom();
                event.preventDefault();
            });
            axe.el.tab.click(function(event) {
                axe.clicked_tab(axe.jq(this));
                event.preventDefault();
            });

        },

        // CLICKED_TAB()
        // -------------------------------------------------------------
        // A tab has been clicked, decide what to do.

        clicked_tab: function(tab) {

            // if the tab is closed
            if (axe.window_open && axe.active_pane == tab.attr(axe.tab_data)) {
                axe.close_window();
            } else {
                axe.open_window(tab);
            }

        },

        // OPEN_WINDOW()
        // -------------------------------------------------------------
        // Animate open the top window to the appropriate tab.

        open_window: function(tab) {

            // can't directly assign this line, but it works
            axe.jq('.axe-profiler-tab-pane:visible').fadeOut(200);
            axe.jq('.' + tab.attr(axe.tab_data)).delay(220).fadeIn(300);
            axe.el.tab_links.removeClass(axe.active_tab);
            tab.addClass(axe.active_tab);
            axe.el.window.slideDown(300);
            axe.el.close.fadeIn(300);
            axe.el.zoom.fadeIn(300);
            axe.active_pane = tab.attr(axe.tab_data);
            axe.window_open = true;

        },

        // CLOSE_WINDOW()
        // -------------------------------------------------------------
        // Animate closed the top window hiding all tabs.

        close_window: function() {

            axe.el.tab_pane.fadeOut(100);
            axe.el.window.slideUp(300);
            axe.el.close.fadeOut(300);
            axe.el.zoom.fadeOut(300);
            axe.el.tab_links.removeClass(axe.active_tab);
            axe.active_pane = '';
            axe.window_open = false;

        },

        // SHOW()
        // -------------------------------------------------------------
        // Show the Anbu toolbar when it has been compacted.

        show: function() {

            axe.el.closed_tabs.fadeOut(600, function () {
                axe.el.main.removeClass('axe-profiler-hidden');
                axe.el.open_tabs.fadeIn(200);
            });
            axe.el.main.animate({width: '100%'}, 700);

        },

        // HIDE()
        // -------------------------------------------------------------
        // Hide the axe toolbar, show a tiny re-open button.

        hide: function() {

            axe.close_window();

            setTimeout(function() {
                axe.el.window.slideUp(400, function () {
                    axe.close_window();
                    axe.el.main.addClass('axe-profiler-hidden');
                    axe.el.open_tabs.fadeOut(200, function () {
                        axe.el.closed_tabs.fadeIn(200);
                    });
                    axe.el.main.animate({width: axe.mini_button_width}, 700);
                });
            }, 100);

        },

        // TOGGLEZOOM()
        // -------------------------------------------------------------
        // Toggle the zoomed mode of the top window.

        zoom: function() {
            var height;
            if (axe.is_zoomed) {
                height = axe.small_height;
                axe.is_zoomed = false;
            } else {
                // the 6px is padding on the top of the window
                height = (axe.jq(window).height() - axe.el.tabs.height() - 6) + 'px';
                axe.is_zoomed = true;
            }

            axe.el.content_area.animate({height: height}, 700);

        }

    });

    // launch axe on jquery dom ready
    axe.jq(axe.start);

})($);