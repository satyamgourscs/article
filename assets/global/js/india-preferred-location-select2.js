/**
 * Select2 helpers for Indian state → city dropdowns.
 * Requires: jQuery, Select2, window.INDIA_STATE_CITY_MAP (object: state name => city names).
 */
(function ($) {
    'use strict';

    function sortedStates(map) {
        return Object.keys(map || {}).sort(function (a, b) {
            return a.localeCompare(b);
        });
    }

    function sortedCities(map, state) {
        var list = (map[state] || []).slice();
        list.sort(function (a, b) {
            return a.localeCompare(b);
        });
        return list;
    }

    function ensureOption($select, value) {
        if (!value) {
            return;
        }
        var exists = false;
        $select.find('option').each(function () {
            if (this.value === value) {
                exists = true;
                return false;
            }
        });
        if (!exists) {
            $select.append(new Option(value, value, true, true));
        }
    }

    /**
     * @param {object} opts
     * @param {string} opts.stateSelect - jQuery selector for state <select>
     * @param {string} opts.citySelect - jQuery selector for city <select>
     * @param {jQuery} [opts.stateDropdownParent]
     * @param {jQuery} [opts.cityDropdownParent]
     * @param {string} [opts.initialState]
     * @param {string} [opts.initialCity]
     * @param {string} [opts.placeholderState]
     * @param {string} [opts.placeholderCity]
     */
    window.initIndiaPreferredLocationSelect2 = function (opts) {
        var map = window.INDIA_STATE_CITY_MAP || {};
        var $state = $(opts.stateSelect);
        var $city = $(opts.citySelect);
        var stateInitial = opts.initialState || '';
        var pendingCity = opts.initialCity || '';
        var phState = opts.placeholderState || '';
        var phCity = opts.placeholderCity || '';

        var $stateParent = opts.stateDropdownParent || $state.parent();
        var $cityParent = opts.cityDropdownParent || $city.parent();

        $state.empty().append(new Option('', '', false, false));
        sortedStates(map).forEach(function (name) {
            $state.append(new Option(name, name, false, false));
        });
        if (stateInitial && !map[stateInitial]) {
            $state.append(new Option(stateInitial, stateInitial, false, false));
        }

        var base = { width: '100%', allowClear: true, dropdownAutoWidth: false, minimumResultsForSearch: 0 };

        function onStateChange() {
            if ($city.hasClass('select2-hidden-accessible')) {
                $city.select2('destroy');
            }
            var st = $state.val() || '';
            $city.empty().append(new Option('', '', false, false));
            var cityToSet = '';
            if (st) {
                sortedCities(map, st).forEach(function (c) {
                    $city.append(new Option(c, c, false, false));
                });
                if (pendingCity && st === stateInitial) {
                    ensureOption($city, pendingCity);
                    cityToSet = pendingCity;
                    pendingCity = '';
                }
            }
            $city.val(cityToSet || '');
            $city.select2($.extend({}, base, {
                placeholder: phCity || '',
                dropdownParent: $cityParent
            }));
        }

        $state.off('change', onStateChange).on('change', onStateChange);

        $state.select2($.extend({}, base, {
            placeholder: phState || '',
            dropdownParent: $stateParent
        }));

        $state.val(stateInitial || '').trigger('change');
    };
})(jQuery);
