module.exports = {
    init: function () {
        $(document).on('ready', function () {
            /* IE11 CustomEvent polyfill */
            if (typeof window.CustomEvent === 'function') return false;
            function CustomEvent(event, params) {
                params = params || { bubbles: false, cancelable: false, detail: undefined };
                var evt = document.createEvent('CustomEvent');
                evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
                return evt;
            }
            CustomEvent.prototype = window.Event.prototype;
            window.CustomEvent = CustomEvent;

            /* Array.prototypes.includes polyfill */
            if (!String.prototype.includes) {
                String.prototype.includes = function () {
                    'use strict';
                    return String.prototype.indexOf.apply(this, arguments) !== -1;
                };
            }
        });
    },
};
