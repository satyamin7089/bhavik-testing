/**
 * ASRS Forms Engine - Location Cascade (Country -> State -> City)
 *
 * Expects window.ASRSFormsConfig.locationDataUrl to point at a JSON file of the
 * shape: { "Country": { "State": ["City", ...], ... }, ... }
 *
 * Fields are declared in a form definition with type "location_cascade" and an
 * attribute pair: location_level (country|state|city|search) and location_group
 * (to link several fields that belong to the same location picker).
 */
(function () {
    'use strict';

    if (typeof window.ASRSFormsConfig === 'undefined') {
        return;
    }

    var DATA_URL = window.ASRSFormsConfig.locationDataUrl;
    if (!DATA_URL) {
        return;
    }

    var cache = {};

    function loadData(group, cb) {
        if (!cache[group]) {
            cache[group] = { loaded: false, loading: false, data: {}, waiters: [] };
        }
        var entry = cache[group];
        if (entry.loaded) {
            cb(entry.data);
            return;
        }
        entry.waiters.push(cb);
        if (entry.loading) {
            return;
        }
        entry.loading = true;
        fetch(DATA_URL)
            .then(function (r) { return r.json(); })
            .then(function (json) {
                entry.data = json || {};
                entry.loaded = true;
                entry.loading = false;
                for (var i = 0; i < entry.waiters.length; i++) {
                    entry.waiters[i](entry.data);
                }
                entry.waiters = [];
            })
            .catch(function () {
                entry.loading = false;
            });
    }

    function fillSelect(sel, items, placeholder) {
        sel.innerHTML = '';
        var ph = document.createElement('option');
        ph.value = '';
        ph.textContent = placeholder || 'Select';
        sel.appendChild(ph);
        for (var i = 0; i < items.length; i++) {
            var o = document.createElement('option');
            o.value = items[i];
            o.textContent = items[i];
            sel.appendChild(o);
        }
    }

    function groupSelects(formEl, group, level) {
        return formEl.querySelectorAll(
            '.asrs-field-type-location_cascade[data-location-group="' + group +
            '"][data-location-level="' + level + '"] select'
        );
    }

    function setSelectValue(formEl, group, level, value) {
        var sels = groupSelects(formEl, group, level);
        for (var i = 0; i < sels.length; i++) {
            sels[i].value = value;
            if (sels[i].dispatchEvent) {
                sels[i].dispatchEvent(new Event('change'));
            }
        }
    }

    function searchInputs(formEl, group) {
        return formEl.querySelectorAll(
            '.asrs-field-type-location_cascade[data-location-group="' + group +
            '"][data-location-level="search"] input'
        );
    }

    function initForm(formEl) {
        if (!formEl || formEl.getAttribute('data-asrs-location-init') === '1') {
            return;
        }
        formEl.setAttribute('data-asrs-location-init', '1');

        var nodes = formEl.querySelectorAll('.asrs-field-type-location_cascade');
        var groups = {};
        for (var n = 0; n < nodes.length; n++) {
            var g = nodes[n].getAttribute('data-location-group') || 'default';
            groups[g] = true;
        }

        Object.keys(groups).forEach(function (group) {
            loadData(group, function (data) {
                var countries = groupSelects(formEl, group, 'country');
                for (var i = 0; i < countries.length; i++) {
                    fillSelect(
                        countries[i],
                        Object.keys(data),
                        countries[i].getAttribute('data-placeholder') || 'Select Country'
                    );
                }

                // Country change -> populate States, clear Cities.
                for (var c = 0; c < countries.length; c++) {
                    countries[c].addEventListener('change', function () {
                        var country = this.value;
                        var states = data[country] || {};
                        var stateSels = groupSelects(formEl, group, 'state');
                        for (var s = 0; s < stateSels.length; s++) {
                            fillSelect(
                                stateSels[s],
                                Object.keys(states),
                                stateSels[s].getAttribute('data-placeholder') || 'Select State'
                            );
                        }
                        var citySels = groupSelects(formEl, group, 'city');
                        for (var ci = 0; ci < citySels.length; ci++) {
                            fillSelect(
                                citySels[ci],
                                [],
                                citySels[ci].getAttribute('data-placeholder') || 'Select City'
                            );
                        }
                    });
                }

                // State change -> populate Cities.
                var stateSels2 = groupSelects(formEl, group, 'state');
                for (var s2 = 0; s2 < stateSels2.length; s2++) {
                    stateSels2[s2].addEventListener('change', function () {
                        var countrySel = groupSelects(formEl, group, 'country');
                        var country = countrySel.length ? countrySel[0].value : '';
                        var state = this.value;
                        var cities = (data[country] && data[country][state]) ? data[country][state] : [];
                        var citySels = groupSelects(formEl, group, 'city');
                        for (var ci = 0; ci < citySels.length; ci++) {
                            fillSelect(
                                citySels[ci],
                                cities,
                                citySels[ci].getAttribute('data-placeholder') || 'Select City'
                            );
                        }
                    });
                }

                // Reverse: type a city -> auto-select State + Country.
                var searches = searchInputs(formEl, group);
                for (var q = 0; q < searches.length; q++) {
                    searches[q].addEventListener('input', function () {
                        var term = this.value.trim().toLowerCase();
                        if (!term) {
                            return;
                        }
                        for (var country in data) {
                            if (!data.hasOwnProperty(country)) { continue; }
                            for (var state in data[country]) {
                                if (!data[country].hasOwnProperty(state)) { continue; }
                                var cities = data[country][state] || [];
                                var found = null;
                                for (var k = 0; k < cities.length; k++) {
                                    if (cities[k].toLowerCase().indexOf(term) !== -1) {
                                        found = cities[k];
                                        break;
                                    }
                                }
                                if (found) {
                                    setSelectValue(formEl, group, 'country', country);
                                    setSelectValue(formEl, group, 'state', state);
                                    setSelectValue(formEl, group, 'city', found);
                                    this.value = found;
                                    return;
                                }
                            }
                        }
                    });
                }
            });
        });
    }

    function boot() {
        var forms = document.querySelectorAll('.asrs-form');
        for (var i = 0; i < forms.length; i++) {
            initForm(forms[i]);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
