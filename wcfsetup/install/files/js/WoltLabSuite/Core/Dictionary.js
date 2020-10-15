var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    Object.defineProperty(o, k2, { enumerable: true, get: function() { return m[k]; } });
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || function (mod) {
    if (mod && mod.__esModule) return mod;
    var result = {};
    if (mod != null) for (var k in mod) if (k !== "default" && Object.prototype.hasOwnProperty.call(mod, k)) __createBinding(result, mod, k);
    __setModuleDefault(result, mod);
    return result;
};
define(["require", "exports", "./Core"], function (require, exports, Core) {
    "use strict";
    Core = __importStar(Core);
    var _hasMap = window.hasOwnProperty('Map') && typeof window.Map === 'function';
    /**
     * @constructor
     */
    function Dictionary() {
        this._dictionary = (_hasMap) ? new Map() : {};
    }
    Dictionary.prototype = {
        /**
         * Sets a new key with given value, will overwrite an existing key.
         *
         * @param  {(number|string)}  key  key
         * @param  {?}            value  value
         */
        set: function (key, value) {
            if (typeof key === 'number')
                key = key.toString();
            if (typeof key !== 'string') {
                throw new TypeError('Only strings can be used as keys, rejected \'' + key + '\' (' + typeof key + ').');
            }
            if (_hasMap)
                this._dictionary.set(key, value);
            else
                this._dictionary[key] = value;
        },
        /**
         * Removes a key from the dictionary.
         *
         * @param  {(number|string)}  key  key
         */
        'delete': function (key) {
            if (typeof key === 'number')
                key = key.toString();
            if (_hasMap)
                this._dictionary['delete'](key);
            else
                this._dictionary[key] = undefined;
        },
        /**
         * Returns true if dictionary contains a value for given key and is not undefined.
         *
         * @param  {(number|string)}  key  key
         * @return  {boolean}  true if key exists and value is not undefined
         */
        has: function (key) {
            if (typeof key === 'number')
                key = key.toString();
            if (_hasMap)
                return this._dictionary.has(key);
            else {
                return (this._dictionary.hasOwnProperty(key) && typeof this._dictionary[key] !== 'undefined');
            }
        },
        /**
         * Retrieves a value by key, returns undefined if there is no match.
         *
         * @param  {(number|string)}  key  key
         * @return  {*}
         */
        get: function (key) {
            if (typeof key === 'number')
                key = key.toString();
            if (this.has(key)) {
                if (_hasMap)
                    return this._dictionary.get(key);
                else
                    return this._dictionary[key];
            }
            return undefined;
        },
        /**
         * Iterates over the dictionary keys and values, callback function should expect the
         * value as first parameter and the key name second.
         *
         * @param  {function<*, string>}  callback  callback for each iteration
         */
        forEach: function (callback) {
            if (typeof callback !== 'function') {
                throw new TypeError('forEach() expects a callback as first parameter.');
            }
            if (_hasMap) {
                this._dictionary.forEach(callback);
            }
            else {
                var keys = Object.keys(this._dictionary);
                for (var i = 0, length = keys.length; i < length; i++) {
                    callback(this._dictionary[keys[i]], keys[i]);
                }
            }
        },
        /**
         * Merges one or more Dictionary instances into this one.
         *
         * @param  {...Dictionary}    var_args  one or more Dictionary instances
         */
        merge: function () {
            for (var i = 0, length = arguments.length; i < length; i++) {
                var dictionary = arguments[i];
                if (!(dictionary instanceof Dictionary)) {
                    throw new TypeError('Expected an object of type Dictionary, but argument ' + i + ' is not.');
                }
                dictionary.forEach((function (value, key) {
                    this.set(key, value);
                }).bind(this));
            }
        },
        /**
         * Returns the object representation of the dictionary.
         *
         * @return  {object}  dictionary's object representation
         */
        toObject: function () {
            if (!_hasMap)
                return Core.clone(this._dictionary);
            var object = {};
            this._dictionary.forEach(function (value, key) {
                object[key] = value;
            });
            return object;
        },
    };
    /**
     * Creates a new Dictionary based on the given object.
     * All properties that are owned by the object will be added
     * as keys to the resulting Dictionary.
     *
     * @param  {object}  object
     * @return  {Dictionary}
     */
    Dictionary.fromObject = function (object) {
        var result = new Dictionary();
        for (var key in object) {
            if (object.hasOwnProperty(key)) {
                result.set(key, object[key]);
            }
        }
        return result;
    };
    Object.defineProperty(Dictionary.prototype, 'size', {
        enumerable: false,
        configurable: true,
        get: function () {
            if (_hasMap) {
                return this._dictionary.size;
            }
            else {
                return Object.keys(this._dictionary).length;
            }
        },
    });
    return Dictionary;
});
