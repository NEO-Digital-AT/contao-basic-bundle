/*
 * Back-end modules of the extensions (menu group "neo"): a click that starts work shows it at once, and a
 * button that sends its form keeps its place.
 *
 * - A submit button, a button link (a.tl_submit) or a global operation gets a spinner and aria-busy until the
 *   page answers.
 * - A submit button with data-neo-action (a scan, "Load suggestions", "Save and generate" …): after the page
 *   loaded again, the button is where it was on the screen, and the messages that came with the answer stand
 *   right below it (in the bar of the form buttons: after them) instead of at the top of the page.
 *
 * Contao replaces the page with Turbo; the script is loaded once and works by delegation on the document.
 * Other scripts of the extensions use window.NeoActions.busy(node, true|false) for actions in place.
 */
(function () {
    'use strict';

    if (window.NeoActions) {
        return;
    }

    var KEY = 'neo-action';
    var LINKS = 'a.tl_submit, #tl_buttons a, .tl_header_buttons a, .header_buttons a';

    function busy(node, on) {
        if (!node) {
            return;
        }

        var spinner = node.querySelector(':scope > .neo-busy');

        if (on) {
            node.setAttribute('aria-busy', 'true');

            // An input cannot hold the spinner; it keeps aria-busy and the cursor.
            if (!spinner && 'INPUT' !== node.tagName) {
                spinner = document.createElement('span');
                spinner.className = 'neo-busy';
                spinner.setAttribute('aria-hidden', 'true');
                node.insertBefore(spinner, node.firstChild);
            }
        } else {
            node.removeAttribute('aria-busy');

            if (spinner) {
                spinner.remove();
            }
        }
    }

    function clearAll() {
        var nodes = document.querySelectorAll('[aria-busy="true"]');

        for (var i = 0; i < nodes.length; i++) {
            if (nodes[i].querySelector(':scope > .neo-busy')) {
                busy(nodes[i], false);
            }
        }
    }

    // The messages at the top of the page, without those already moved to a button.
    function messages() {
        return Array.prototype.map.call(document.querySelectorAll('.tl_message:not(.neo-action__result) > *'), function (node) {
            return node.textContent.trim();
        });
    }

    window.NeoActions = { busy: busy };

    document.addEventListener('submit', function (event) {
        var button = event.submitter;

        if (!button || !button.matches('button, input[type="submit"]')) {
            return;
        }

        busy(button, true);

        if (button.hasAttribute('data-neo-action') && button.name) {
            try {
                window.sessionStorage.setItem(KEY, JSON.stringify({ name: button.name, top: button.getBoundingClientRect().top, known: messages() }));
            } catch (e) {
                // Without the session storage the page opens at the top, as Contao does.
            }
        }
    }, true);

    // Links that Contao follows with Turbo; a link that leaves Turbo (data-turbo="false") shows it on the click.
    document.addEventListener('turbo:click', function (event) {
        var link = event.target && event.target.closest ? event.target.closest(LINKS) : null;

        if (link) {
            busy(link, true);
        }
    });

    document.addEventListener('click', function (event) {
        var link = event.target && event.target.closest ? event.target.closest(LINKS) : null;

        if (!link || 'false' !== link.getAttribute('data-turbo') || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || '_blank' === link.target) {
            return;
        }

        busy(link, true);
    });

    function restore() {
        var raw = null;

        try {
            raw = window.sessionStorage.getItem(KEY);
            window.sessionStorage.removeItem(KEY);
        } catch (e) {
            return;
        }

        if (!raw) {
            return;
        }

        var data = JSON.parse(raw);
        var buttons = document.querySelectorAll('[data-neo-action]');
        var button = null;

        for (var i = 0; i < buttons.length; i++) {
            if (buttons[i].name === data.name) {
                button = buttons[i];
                break;
            }
        }

        if (!button) {
            return;
        }

        var fresh = [];
        var nodes = document.querySelectorAll('.tl_message:not(.neo-action__result) > *');

        for (var j = 0; j < nodes.length; j++) {
            if (-1 === data.known.indexOf(nodes[j].textContent.trim())) {
                fresh.push(nodes[j]);
            }
        }

        if (fresh.length) {
            var box = document.createElement('div');

            box.className = 'tl_message neo-action__result';
            box.setAttribute('role', 'status');

            fresh.forEach(function (node) {
                var parent = node.parentNode;

                box.appendChild(node);

                if (parent && !parent.children.length) {
                    parent.remove();
                }
            });

            var bar = button.closest('.tl_submit_container');

            if (bar) {
                bar.appendChild(box);
            } else {
                (button.closest('p, .widget') || button).insertAdjacentElement('afterend', box);
            }
        }

        // The button where it was on the screen (after Turbo's own scrolling).
        window.requestAnimationFrame(function () {
            window.requestAnimationFrame(function () {
                window.scrollBy(0, button.getBoundingClientRect().top - data.top);
            });
        });
    }

    document.addEventListener('turbo:load', restore);
    document.addEventListener('turbo:before-cache', clearAll);
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            clearAll();
        }
    });

    if (!window.Turbo) {
        if ('loading' === document.readyState) {
            document.addEventListener('DOMContentLoaded', restore);
        } else {
            restore();
        }
    }
})();
