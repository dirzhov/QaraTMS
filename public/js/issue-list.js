/*!
  * Bootstrap Autocomplete v0.2.0 (https://iqbalfn.github.io/bootstrap-autocomplete/)
  * Copyright 2019 Iqbal Fauzi
  * Licensed under MIT (https://github.com/iqbalfn/bootstrap-autocomplete/blob/master/LICENSE)
  */
(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports, require('jquery')) :
        typeof define === 'function' && define.amd ? define(['exports', 'jquery'], factory) :
            (global = global || self, factory(global['bootstrap-autocomplete'] = {}, global.jQuery));
}(this, (function (exports, $) { 'use strict';

    $ = $ && Object.prototype.hasOwnProperty.call($, 'default') ? $['default'] : $;

    function _defineProperties(target, props) {
        for (var i = 0; i < props.length; i++) {
            var descriptor = props[i];
            descriptor.enumerable = descriptor.enumerable || false;
            descriptor.configurable = true;
            if ("value" in descriptor) descriptor.writable = true;
            Object.defineProperty(target, descriptor.key, descriptor);
        }
    }

    function _createClass(Constructor, protoProps, staticProps) {
        if (protoProps) _defineProperties(Constructor.prototype, protoProps);
        if (staticProps) _defineProperties(Constructor, staticProps);
        return Constructor;
    }

    function _defineProperty(obj, key, value) {
        if (key in obj) {
            Object.defineProperty(obj, key, {
                value: value,
                enumerable: true,
                configurable: true,
                writable: true
            });
        } else {
            obj[key] = value;
        }

        return obj;
    }

    function _objectSpread(target) {
        for (var i = 1; i < arguments.length; i++) {
            var source = arguments[i] != null ? Object(arguments[i]) : {};
            var ownKeys = Object.keys(source);

            if (typeof Object.getOwnPropertySymbols === 'function') {
                ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
                    return Object.getOwnPropertyDescriptor(source, sym).enumerable;
                }));
            }

            ownKeys.forEach(function (key) {
                _defineProperty(target, key, source[key]);
            });
        }

        return target;
    }

    /**
     * --------------------------------------------------------------------------
     * Bootstrap (v4.3.1): util.js
     * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
     * --------------------------------------------------------------------------
     */
    /**
     * ------------------------------------------------------------------------
     * Private TransitionEnd Helpers
     * ------------------------------------------------------------------------
     */

    var TRANSITION_END = 'transitionend';
    var MAX_UID = 1000000;
    var MILLISECONDS_MULTIPLIER = 1000; // Shoutout AngusCroll (https://goo.gl/pxwQGp)

    function toType(obj) {
        return {}.toString.call(obj).match(/\s([a-z]+)/i)[1].toLowerCase();
    }

    function getSpecialTransitionEndEvent() {
        return {
            bindType: TRANSITION_END,
            delegateType: TRANSITION_END,
            handle: function handle(event) {
                if ($(event.target).is(this)) {
                    return event.handleObj.handler.apply(this, arguments); // eslint-disable-line prefer-rest-params
                }

                return undefined; // eslint-disable-line no-undefined
            }
        };
    }

    function transitionEndEmulator(duration) {
        var _this = this;

        var called = false;
        $(this).one(Util.TRANSITION_END, function () {
            called = true;
        });
        setTimeout(function () {
            if (!called) {
                Util.triggerTransitionEnd(_this);
            }
        }, duration);
        return this;
    }

    function setTransitionEndSupport() {
        $.fn.emulateTransitionEnd = transitionEndEmulator;
        $.event.special[Util.TRANSITION_END] = getSpecialTransitionEndEvent();
    }
    /**
     * --------------------------------------------------------------------------
     * Public Util Api
     * --------------------------------------------------------------------------
     */


    var Util = {
        TRANSITION_END: 'bsTransitionEnd',
        getUID: function getUID(prefix) {
            do {
                // eslint-disable-next-line no-bitwise
                prefix += ~~(Math.random() * MAX_UID); // "~~" acts like a faster Math.floor() here
            } while (document.getElementById(prefix));

            return prefix;
        },
        getSelectorFromElement: function getSelectorFromElement(element) {
            var selector = element.getAttribute('data-target');

            if (!selector || selector === '#') {
                var hrefAttr = element.getAttribute('href');
                selector = hrefAttr && hrefAttr !== '#' ? hrefAttr.trim() : '';
            }

            try {
                return document.querySelector(selector) ? selector : null;
            } catch (err) {
                return null;
            }
        },
        getTransitionDurationFromElement: function getTransitionDurationFromElement(element) {
            if (!element) {
                return 0;
            } // Get transition-duration of the element


            var transitionDuration = $(element).css('transition-duration');
            var transitionDelay = $(element).css('transition-delay');
            var floatTransitionDuration = parseFloat(transitionDuration);
            var floatTransitionDelay = parseFloat(transitionDelay); // Return 0 if element or transition duration is not found

            if (!floatTransitionDuration && !floatTransitionDelay) {
                return 0;
            } // If multiple durations are defined, take the first


            transitionDuration = transitionDuration.split(',')[0];
            transitionDelay = transitionDelay.split(',')[0];
            return (parseFloat(transitionDuration) + parseFloat(transitionDelay)) * MILLISECONDS_MULTIPLIER;
        },
        reflow: function reflow(element) {
            return element.offsetHeight;
        },
        triggerTransitionEnd: function triggerTransitionEnd(element) {
            $(element).trigger(TRANSITION_END);
        },
        // TODO: Remove in v5
        supportsTransitionEnd: function supportsTransitionEnd() {
            return Boolean(TRANSITION_END);
        },
        isElement: function isElement(obj) {
            return (obj[0] || obj).nodeType;
        },
        typeCheckConfig: function typeCheckConfig(componentName, config, configTypes) {
            for (var property in configTypes) {
                if (Object.prototype.hasOwnProperty.call(configTypes, property)) {
                    var expectedTypes = configTypes[property];
                    var value = config[property];
                    var valueType = value && Util.isElement(value) ? 'element' : toType(value);

                    if (!new RegExp(expectedTypes).test(valueType)) {
                        throw new Error(componentName.toUpperCase() + ": " + ("Option \"" + property + "\" provided type \"" + valueType + "\" ") + ("but expected type \"" + expectedTypes + "\"."));
                    }
                }
            }
        },
        findShadowRoot: function findShadowRoot(element) {
            if (!document.documentElement.attachShadow) {
                return null;
            } // Can find the shadow root otherwise it'll return the document


            if (typeof element.getRootNode === 'function') {
                var root = element.getRootNode();
                return root instanceof ShadowRoot ? root : null;
            }

            if (element instanceof ShadowRoot) {
                return element;
            } // when we don't find a shadow root


            if (!element.parentNode) {
                return null;
            }

            return Util.findShadowRoot(element.parentNode);
        }
    };
    setTransitionEndSupport();

    /**
     * ------------------------------------------------------------------------
     * Constants
     * ------------------------------------------------------------------------
     */

    var NAME = 'issuelist';
    var VERSION = '0.1.0';
    var DATA_KEY = 'bs.issuelist';
    var EVENT_KEY = "." + DATA_KEY;
    var DATA_API_KEY = '.data-api';
    var JQUERY_NO_CONFLICT = $.fn[NAME];
    var Default = {
        issues: null,
        savedIssueKeys: null,
        issueUrl: null,
        preProcess: null,
        onBeforeDelete: null,
        onPick: null,
        onItemRendered: null,
        onItemsRendered: null
    };
    var DefaultType = {
        issues: '(null|array)',
        savedIssueKeys: '(null|string)',
        issueUrl: '(null|string)',
        preProcess: '(null|function)',
        onBeforeFetch: '(null|function)',
        onAfterFetch: '(null|function)',
        onBeforeDelete: '(null|function)',
        onPick: '(null|function)',
        onItemRendered: '(null|function)',
        onItemsRendered: '(null|function)'
    };
    var Event = {
        DELETE_DATA_API: "delete" + EVENT_KEY + DATA_API_KEY,
        PICK: "pick" + EVENT_KEY,
        ITEM_RENDER: "itemrender" + EVENT_KEY
    };
    /**
     * ------------------------------------------------------------------------
     * Class Definition
     * ------------------------------------------------------------------------
     */

    var IssueList = /*#__PURE__*/function () {
        function IssueList(element, config) {
            this._config = this._getConfig(config);
            this._element = element;
            this._items = [];
            this._spinner = null;

            if (this._config.issues) this._items = this._config.issues;

            element.setAttribute('issuelist', 'off');

            this._makeSpinner();

            this._fetchPresetData();

            if (!this._config.prefetch) this._renderItems(this._items);
        } // Getters


        var _proto = IssueList.prototype;

        _proto.dispose = function dispose() {
            $.removeData(this._element, DATA_KEY);
            this._config = null;
            this._element = null;
            this._items = null;
            this._spinner = null;
        } // Private
        ;

        _proto._fetchPresetData = function _fetchPresetData(savedIssueKeys) {
            var _this2 = this;
            if (!this._config.prefetch) return;

            this.savedIssueKeys = savedIssueKeys

            var fetchUrl = '';
            if (this.savedIssueKeys)
                fetchUrl = this._config.prefetch.replace("%%ISSUES%%",
                    '"' + this.savedIssueKeys.replace(',', '","') + '"')
            else
                fetchUrl =  this._config.prefetch.replace("%%ISSUES%%", '');

            if (fetchUrl.indexOf('in ("")') < 0 && fetchUrl.indexOf('in ()') < 0) {
                this._showSpinner();
                if (this._config.onBeforeFetch) this._config.onBeforeFetch();

                $.ajax({
                    url: fetchUrl,
                    type: 'get',
                    headers: {
                        'Authorization': 'Bearer ' + jiraToken,
                        'Content-Type': 'application/json'
                    },
                    success: function (res) {
                        res = res.issues;

                        if (_this2._config.preProcess) res = _this2._config.preProcess(res);
                        res.forEach(function (i) {
                            if (_this2._items.some((e) => e.key == i.key)) return;

                            _this2._items.push(i);
                        });
                        _this2._renderItems(_this2._items);

                        if (_this2._config.onAfterFetch) _this2._config.onAfterFetch();
                        _this2._hideSpinner();
                    },
                    error: function (err) {
                        if (_this2._config.onAfterFetch) _this2._config.onAfterFetch();
                        _this2._hideSpinner();
                    }
                });
            }
        };

        _proto._makeSpinner = function _makeSpinner() {
            var tmpl = '<div class="text-center mask"><div class="spinner-border spinner-border-sm text-secondary" role="status"></div></div>';
            this._spinner = $(tmpl).appendTo(this._element).get(0);
            $(this._spinner).css({
                position: 'absolute',
                // top: this._element.offsetTop + (this._element.offsetHeight - this._spinner.offsetHeight) / 2 + 'px'
            });
            this._hideSpinner();
        };

        _proto._showSpinner = function _showSpinner() {
            this._spinner.style.display = 'inline-block';
        };

        _proto._hideSpinner = function _hideSpinner() {
            this._spinner.style.display = 'none';
        };

        _proto._renderItems = function _renderItems(items) {
            var _this4 = this;

            if (_this4._config.onItemsRendered) _this4._config.onItemsRendered(_this4._element);

            if (items && items.length)
                items.forEach(function (i) {
                    _this4._renderItem(i);
                });
        };
        
        _proto._renderItem = function _renderItem(dataItem) {
            var _this3 = this;
            var item = null;
            if (_this3._config.itemRenderer) {
                item = _this3._config.itemRenderer(_this3._element, dataItem);
            } else {
                var clsClosed = '';
                var summary = dataItem.summary || dataItem.fields.summary;
                var issueUrl = dataItem.summary || dataItem.fields.summary;
                var iconUrl = dataItem.iconUrl || dataItem.fields.issuetype.iconUrl;
                var status = dataItem.status || dataItem.fields.status.name;
                if (status == 'Closed') {
                    clsClosed = ' text-secondary';
                    summary = '<del>' + summary + '</del>';
                }
                item = $('<li class="list-group-item list-group-item-action cursor-pointer' + clsClosed
                    + '"><img alt height="16" width="16" src="' + iconUrl
                    + '"> <a href="' + _this3._config.issueUrl + dataItem.key + '" target="_blank">[' + dataItem.key
                    + ']</a> ' + summary + '<span class="badge bg-danger rounded-pill float-end ms-2 d-none">x</span></li>');

                item.on( "mouseenter mouseleave", function(event) {
                    $(this).find('.badge').toggleClass( "d-none" );
                    //event.preventDefault();
                });

                item.on( "click", ".badge", function() {
                    var parent = $(this).parent();
                    if (_this3._config.onBeforeDelete) _this3._config.onBeforeDelete(parent);

                    var key = parent.data().key;
                    _this3._items = _this3._items.filter(i => i.key != key);
                    parent.remove();
                });
            }
            item.data(dataItem);

            item.appendTo(_this3._element);

            if (_this3._config.onItemRendered) _this3._config.onItemRendered(_this3._element, item.get(0));

            var renderEvent = $.Event(Event.ITEM_RENDER, {
                item: item.get(0)
            });
            $(_this3._element).trigger(renderEvent);
        }

        _proto.refresh = function refresh() {
            this._element.innerHTML = '';
            this._renderItems(this._items);
        }

        _proto.clear = function clear() {
            var issues = $(this._element).children('li');

            issues.remove();
            this._config.savedIssueKeys = null;
            this._items = [];
        };

        _proto.deleteItem = function deleteItem(key) {
            var _this5 = this;

            var issues = $(this._element).children('li');
            var issue = issues.filter((i,e) => $(e).data().key == key);
            if (!issue.length) return;

            if (_this5._config.onBeforeDelete) _this5._config.onBeforeDelete(issue);

            var deleteEvent = $.Event(Event.DELETE_DATA_API, {
                item: issue
            });

            issue.remove();

            _this5._items = _this5._items.filter(i => {i.key != key});
            if (this._config.onDelete) this._config.onDelete(this._element, item);

            $(this._element).trigger(deleteEvent);
        };

        _proto.addItem = function addItem(item) {
            this._items.push(item);

            if (this._config.onPick) this._config.onPick(this._element, item);

            var pickEvent = $.Event(Event.PICK, {
                item: item
            });
            $(this._element).trigger(pickEvent);
        };

        _proto._getConfig = function _getConfig(config) {
            config = _objectSpread({}, Default, config);
            Util.typeCheckConfig(NAME, config, DefaultType);
            return config;
        };

        IssueList._jQueryInterface = function _jQueryInterface(config, relatedTarget) {
            return this.each(function () {
                var data = $(this).data(DATA_KEY);

                var _config = _objectSpread({}, Default, $(this).data(), typeof config === 'object' && config ? config : {});

                if (!data) {
                    data = new IssueList(this, _config);
                    $(this).data(DATA_KEY, data);
                }

                if (typeof config === 'string') {
                    if (typeof data[config] === 'undefined') throw new TypeError("No method named \"" + config + "\"");
                    data[config](relatedTarget);
                }
            });
        };

        _createClass(IssueList, null, [{
            key: "VERSION",
            get: function get() {
                return VERSION;
            }
        }, {
            key: "Default",
            get: function get() {
                return Default;
            }
        }]);

        return IssueList;
    }();
    /**
     * ------------------------------------------------------------------------
     * jQuery
     * ------------------------------------------------------------------------
     */


    $.fn[NAME] = IssueList._jQueryInterface;
    $.fn[NAME].Constructor = IssueList;

    $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return IssueList._jQueryInterface;
    };

    exports.IssueList = IssueList;

    Object.defineProperty(exports, '__esModule', { value: true });

})));