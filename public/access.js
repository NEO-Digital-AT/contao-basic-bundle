/*
 * "Access keys and licences": runs "Check now" and "Test connection" in place.
 *
 * Each such button is a link with the request token in its address, so that it
 * works without JavaScript (the page opens again at the block afterwards). This
 * script turns the links into buttons and sends a POST with the request token
 * of the form instead; the answer replaces the result of the block, without
 * leaving the page and without losing anything typed. While the request runs,
 * the button is disabled, says what is happening with a spinner and the result
 * area is busy; the new result is announced (role="status"). The result stands
 * below the button (tests) or right next to it (licence rows).
 *
 * A link to a collapsed section (#pal_…) opens it. Enter in a field saves the
 * form instead of pressing "Confirm" of the terms.
 *
 * Contao's back end replaces the page with Turbo; the script is loaded once and
 * works by delegation on the document.
 */
(function () {
    'use strict';

    if (window.NeoAccessActions) {
        return;
    }

    window.NeoAccessActions = true;

    var SELECTOR = '[data-neo-access-action]';

    // The links become buttons: disabled while busy, Space and Enter like any button.
    function upgrade(root) {
        var links = (root || document).querySelectorAll('a' + SELECTOR);

        for (var i = 0; i < links.length; i++) {
            var link = links[i];
            var button = document.createElement('button');

            button.type = 'button';
            button.className = link.className;
            button.textContent = link.textContent;

            for (var j = 0; j < link.attributes.length; j++) {
                var attribute = link.attributes[j];

                if (attribute.name.indexOf('data-') === 0) {
                    button.setAttribute(attribute.name, attribute.value);
                }
            }

            button.setAttribute('data-neo-access-url', link.href);
            link.replaceWith(button);
        }
    }

    function token(trigger) {
        var form = trigger.closest('form');
        var field = form ? form.querySelector('input[name="REQUEST_TOKEN"]') : null;

        return field ? field.value : '';
    }

    function failure(text) {
        var box = document.createElement('div');
        var line = document.createElement('p');

        box.className = 'tl_message';
        line.className = 'tl_error';
        line.textContent = text;
        box.appendChild(line);

        return box.outerHTML;
    }

    function setBusy(trigger, region, busy) {
        if (busy) {
            trigger.setAttribute('data-neo-access-label', trigger.textContent);
            trigger.textContent = trigger.getAttribute('data-neo-access-busy') || trigger.textContent;
        } else if (trigger.hasAttribute('data-neo-access-label')) {
            trigger.textContent = trigger.getAttribute('data-neo-access-label');
            trigger.removeAttribute('data-neo-access-label');
        }

        if ('BUTTON' === trigger.tagName) {
            trigger.disabled = busy;
        } else {
            trigger.setAttribute('aria-disabled', busy ? 'true' : 'false');
        }

        trigger.setAttribute('aria-busy', busy ? 'true' : 'false');

        // The spinner of the extensions' back end (actions.js, loaded with every module of the group).
        if (window.NeoActions) {
            window.NeoActions.busy(trigger, busy);
        }

        if (region) {
            region.setAttribute('aria-busy', busy ? 'true' : 'false');
        }
    }

    function run(trigger) {
        if (trigger.disabled || 'true' === trigger.getAttribute('aria-busy')) {
            return;
        }

        var block = document.getElementById(trigger.getAttribute('data-neo-access-target'));
        var region = block ? block.querySelector('[data-neo-access-result]') : null;
        var url = trigger.getAttribute('data-neo-access-url') || trigger.getAttribute('href');
        var body = new FormData();

        body.append('REQUEST_TOKEN', token(trigger));
        setBusy(trigger, region, true);

        fetch(url, { method: 'POST', body: body, credentials: 'same-origin', headers: { Accept: 'application/json' } })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (!data || 'string' !== typeof data.html) {
                    throw new Error('unexpected answer');
                }

                if (region) {
                    region.innerHTML = data.html;
                }
            })
            .catch(function () {
                if (region) {
                    region.innerHTML = failure(trigger.getAttribute('data-neo-access-failed') || '');
                }
            })
            .then(function () {
                setBusy(trigger, region, false);
            });
    }

    document.addEventListener('click', function (event) {
        var trigger = event.target && event.target.closest ? event.target.closest(SELECTOR) : null;

        if (!trigger || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        // Before Turbo, which would follow the link otherwise.
        event.preventDefault();
        event.stopPropagation();
        run(trigger);
    }, true);

    // Enter in a field saves the form. Without this, the browser submits with the
    // first submit button of the form, which is "Confirm" of the terms when they
    // are open, and the page answers with "tick the box" instead of saving.
    document.addEventListener('keydown', function (event) {
        var field = event.target;

        if ('Enter' !== event.key || event.isComposing || !field.matches || !field.matches('input:not([type="checkbox"]):not([type="radio"]):not([type="submit"]):not([type="button"])')) {
            return;
        }

        var form = field.form;
        var save = form && form.querySelector('button[name="neo_terms_accept"]') ? form.querySelector('button[type="submit"][name="save"]') : null;

        if (save) {
            event.preventDefault();
            save.click();
        }
    });

    // A link to a section (#pal_…, e.g. from the hint of an extension) opens it when it is collapsed.
    function openTarget() {
        var id = decodeURIComponent(window.location.hash.slice(1));
        var fieldset = 0 === id.indexOf('pal_') ? document.getElementById(id) : null;
        var toggle = fieldset && fieldset.classList.contains('collapsed') ? fieldset.querySelector('legend button') : null;

        if (toggle) {
            toggle.click();

            // Before Contao's controller is connected the click does nothing.
            if (fieldset.classList.contains('collapsed')) {
                fieldset.classList.remove('collapsed');
                toggle.setAttribute('aria-expanded', 'true');
            }

            fieldset.scrollIntoView();
        }
    }

    function ready() {
        upgrade();
        openTarget();
    }

    document.addEventListener('DOMContentLoaded', ready);
    document.addEventListener('turbo:load', ready);
    document.addEventListener('turbo:render', function () { upgrade(); });
    window.addEventListener('hashchange', openTarget);

    if ('loading' !== document.readyState) {
        ready();
    }
})();
