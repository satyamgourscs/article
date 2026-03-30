/**
 * India location: states & cities via countriesnow.space, pincode via postalpincode.in
 * Requires: jQuery, Select2
 *
 * window.initIndiaLocationApiSelect2({
 *   stateSelect: '#state',
 *   citySelect: '#city',
 *   pincodeInput: '#pincode',
 *   stateDropdownParent: jQuery(...),
 *   cityDropdownParent: jQuery(...),
 *   initialState: '',
 *   initialCity: '',
 *   initialPincode: '',
 *   placeholderState: '',
 *   placeholderCity: ''
 * });
 */
(function ($) {
    'use strict';

    var STATES_URL = 'https://countriesnow.space/api/v0.1/countries/states';
    var CITIES_URL = 'https://countriesnow.space/api/v0.1/countries/state/cities';

    function fetchJson(url, options) {
        return fetch(url, options || {}).then(function (res) {
            if (!res.ok) {
                throw new Error('HTTP ' + res.status);
            }
            return res.json();
        });
    }

    function parseStatesPayload(data) {
        if (!data || !data.data) {
            return [];
        }
        var raw = data.data.states;
        if (!Array.isArray(raw)) {
            return [];
        }
        return raw
            .map(function (s) {
                if (typeof s === 'string') {
                    return s.trim();
                }
                return (s && (s.name || s.state_name) ? String(s.name || s.state_name).trim() : '') || '';
            })
            .filter(Boolean)
            .sort(function (a, b) {
                return a.localeCompare(b);
            });
    }

    function parseCitiesPayload(data) {
        if (!data || data.error) {
            return [];
        }
        var d = data.data;
        if (Array.isArray(d)) {
            return d
                .map(function (c) {
                    if (typeof c === 'string') {
                        return c.trim();
                    }
                    return (c && c.name) ? String(c.name).trim() : '';
                })
                .filter(Boolean);
        }
        if (d && Array.isArray(d.cities)) {
            return d.cities
                .map(function (c) {
                    return typeof c === 'string' ? c.trim() : (c && c.name ? String(c.name).trim() : '');
                })
                .filter(Boolean);
        }
        return [];
    }

    function findBestStateMatch(apiStates, pincodeState) {
        var p = (pincodeState || '').trim().toLowerCase();
        if (!p) {
            return '';
        }
        var i;
        for (i = 0; i < apiStates.length; i++) {
            if (apiStates[i].toLowerCase() === p) {
                return apiStates[i];
            }
        }
        for (i = 0; i < apiStates.length; i++) {
            var s = apiStates[i].toLowerCase();
            if (s.indexOf(p) !== -1 || p.indexOf(s) !== -1) {
                return apiStates[i];
            }
        }
        return '';
    }

    function findBestCityMatch(cities, hint) {
        var h = (hint || '').trim().toLowerCase();
        if (!h) {
            return '';
        }
        var i;
        for (i = 0; i < cities.length; i++) {
            if (cities[i].toLowerCase() === h) {
                return cities[i];
            }
        }
        for (i = 0; i < cities.length; i++) {
            var c = cities[i].toLowerCase();
            if (c.indexOf(h) !== -1 || h.indexOf(c) !== -1) {
                return cities[i];
            }
        }
        return '';
    }

    window.initIndiaLocationApiSelect2 = function (opts) {
        var $state = $(opts.stateSelect);
        var $city = $(opts.citySelect);
        var $pincode = opts.pincodeInput ? $(opts.pincodeInput) : null;
        var stateParent = opts.stateDropdownParent || $state.parent();
        var cityParent = opts.cityDropdownParent || $city.parent();
        var initialState = opts.initialState || '';
        var initialCity = opts.initialCity || '';
        var phState = opts.placeholderState || '';
        var phCity = opts.placeholderCity || '';

        var citiesCache = Object.create(null);
        var apiStatesList = [];

        function destroySel($el) {
            if ($el && $el.length && $el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }
        }

        function initStateSelect2() {
            destroySel($state);
            $state.select2({
                width: '100%',
                allowClear: true,
                placeholder: phState,
                dropdownParent: stateParent,
            });
        }

        function initCitySelect2() {
            destroySel($city);
            $city.select2({
                width: '100%',
                allowClear: true,
                placeholder: phCity,
                dropdownParent: cityParent,
            });
        }

        function loadStates() {
            return fetchJson(STATES_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ country: 'India' }),
            }).then(parseStatesPayload);
        }

        function loadCities(stateName) {
            if (!stateName) {
                return Promise.resolve([]);
            }
            if (citiesCache[stateName]) {
                return Promise.resolve(citiesCache[stateName]);
            }
            return fetchJson(CITIES_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ country: 'India', state: stateName }),
            })
                .then(parseCitiesPayload)
                .then(function (list) {
                    list.sort(function (a, b) {
                        return a.localeCompare(b);
                    });
                    citiesCache[stateName] = list;
                    return list;
                });
        }

        function populateCities(stateName, cityToSelect) {
            return loadCities(stateName).then(function (cities) {
                destroySel($city);
                $city.empty().append(new Option('', '', false, false));
                cities.forEach(function (c) {
                    $city.append(new Option(c, c, false, false));
                });
                var pick = cityToSelect || '';
                if (pick) {
                    var match = findBestCityMatch(cities, pick);
                    if (match) {
                        pick = match;
                    } else {
                        $city.append(new Option(pick, pick, false, false));
                    }
                }
                initCitySelect2();
                if (pick) {
                    $city.val(pick).trigger('change');
                }
            });
        }

        function fillFromPincode(rawPin) {
            var pin = String(rawPin || '').replace(/\D/g, '');
            if (pin.length !== 6) {
                return;
            }
            fetchJson('https://api.postalpincode.in/pincode/' + pin)
                .then(function (arr) {
                    if (!Array.isArray(arr) || !arr[0] || arr[0].Status !== 'Success') {
                        return;
                    }
                    var pos = arr[0].PostOffice;
                    if (!pos || !pos[0]) {
                        return;
                    }
                    var po = pos[0];
                    var stFromPin = (po.State || '').trim();
                    var cityHint = (po.District || po.Name || '').trim();
                    var matchState = findBestStateMatch(apiStatesList, stFromPin);
                    if (!matchState && stFromPin) {
                        matchState = stFromPin;
                        var exists = false;
                        $state.find('option').each(function () {
                            if (this.value === stFromPin) {
                                exists = true;
                                return false;
                            }
                        });
                        if (!exists) {
                            destroySel($state);
                            $state.append(new Option(stFromPin, stFromPin, false, false));
                            apiStatesList.push(stFromPin);
                            apiStatesList.sort(function (a, b) {
                                return a.localeCompare(b);
                            });
                            initStateSelect2();
                        }
                    }
                    if (!matchState) {
                        return;
                    }
                    $state.val(matchState);
                    return populateCities(matchState, cityHint);
                })
                .catch(function () {});
        }

        loadStates()
            .then(function (states) {
                apiStatesList = states;
                $state.empty().append(new Option('', '', false, false));
                states.forEach(function (s) {
                    $state.append(new Option(s, s, false, false));
                });
                if (initialState && states.indexOf(initialState) === -1) {
                    $state.append(new Option(initialState, initialState, false, false));
                }
                initStateSelect2();
                if (initialState) {
                    $state.val(initialState);
                    return populateCities(initialState, initialCity);
                }
                destroySel($city);
                $city.empty().append(new Option('', '', false, false));
                initCitySelect2();
                return Promise.resolve();
            })
            .then(function () {
                $state.off('change.indiaLoc').on('change.indiaLoc', function () {
                    var st = $state.val();
                    if (!st) {
                        destroySel($city);
                        $city.empty().append(new Option('', '', false, false));
                        initCitySelect2();
                        return;
                    }
                    populateCities(st, '');
                });

                if ($pincode && $pincode.length) {
                    $pincode.off('blur.indiaLoc').on('blur.indiaLoc', function () {
                        fillFromPincode($(this).val());
                    });
                }
            })
            .catch(function () {
                if (window.console && console.warn) {
                    console.warn('India states API failed');
                }
            });
    };
})(jQuery);
