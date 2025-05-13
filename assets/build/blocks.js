/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./client/blocks/alipay/index.jsx":
/*!****************************************!*\
  !*** ./client/blocks/alipay/index.jsx ***!
  \****************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('payone_alipay', (0, _i18n.__)('PAYONE Alipay', 'payone-woocommerce-3'), "".concat(_constants.PAYONE_ASSETS_URL, "/icon-alipay.svg"));

/***/ }),

/***/ "./client/blocks/bancontact/index.jsx":
/*!********************************************!*\
  !*** ./client/blocks/bancontact/index.jsx ***!
  \********************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('payone_bancontact', (0, _i18n.__)('PAYONE Bancontact', 'payone-woocommerce-3'), "".concat(_constants.PAYONE_ASSETS_URL, "/icon-bancontact.png"));

/***/ }),

/***/ "./client/blocks/credit-card/index.jsx":
/*!*********************************************!*\
  !*** ./client/blocks/credit-card/index.jsx ***!
  \*********************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _element = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return e; }; var t, e = {}, r = Object.prototype, n = r.hasOwnProperty, o = Object.defineProperty || function (t, e, r) { t[e] = r.value; }, i = "function" == typeof Symbol ? Symbol : {}, a = i.iterator || "@@iterator", c = i.asyncIterator || "@@asyncIterator", u = i.toStringTag || "@@toStringTag"; function define(t, e, r) { return Object.defineProperty(t, e, { value: r, enumerable: !0, configurable: !0, writable: !0 }), t[e]; } try { define({}, ""); } catch (t) { define = function define(t, e, r) { return t[e] = r; }; } function wrap(t, e, r, n) { var i = e && e.prototype instanceof Generator ? e : Generator, a = Object.create(i.prototype), c = new Context(n || []); return o(a, "_invoke", { value: makeInvokeMethod(t, r, c) }), a; } function tryCatch(t, e, r) { try { return { type: "normal", arg: t.call(e, r) }; } catch (t) { return { type: "throw", arg: t }; } } e.wrap = wrap; var h = "suspendedStart", l = "suspendedYield", f = "executing", s = "completed", y = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var p = {}; define(p, a, function () { return this; }); var d = Object.getPrototypeOf, v = d && d(d(values([]))); v && v !== r && n.call(v, a) && (p = v); var g = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(p); function defineIteratorMethods(t) { ["next", "throw", "return"].forEach(function (e) { define(t, e, function (t) { return this._invoke(e, t); }); }); } function AsyncIterator(t, e) { function invoke(r, o, i, a) { var c = tryCatch(t[r], t, o); if ("throw" !== c.type) { var u = c.arg, h = u.value; return h && "object" == _typeof(h) && n.call(h, "__await") ? e.resolve(h.__await).then(function (t) { invoke("next", t, i, a); }, function (t) { invoke("throw", t, i, a); }) : e.resolve(h).then(function (t) { u.value = t, i(u); }, function (t) { return invoke("throw", t, i, a); }); } a(c.arg); } var r; o(this, "_invoke", { value: function value(t, n) { function callInvokeWithMethodAndArg() { return new e(function (e, r) { invoke(t, n, e, r); }); } return r = r ? r.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(e, r, n) { var o = h; return function (i, a) { if (o === f) throw Error("Generator is already running"); if (o === s) { if ("throw" === i) throw a; return { value: t, done: !0 }; } for (n.method = i, n.arg = a;;) { var c = n.delegate; if (c) { var u = maybeInvokeDelegate(c, n); if (u) { if (u === y) continue; return u; } } if ("next" === n.method) n.sent = n._sent = n.arg;else if ("throw" === n.method) { if (o === h) throw o = s, n.arg; n.dispatchException(n.arg); } else "return" === n.method && n.abrupt("return", n.arg); o = f; var p = tryCatch(e, r, n); if ("normal" === p.type) { if (o = n.done ? s : l, p.arg === y) continue; return { value: p.arg, done: n.done }; } "throw" === p.type && (o = s, n.method = "throw", n.arg = p.arg); } }; } function maybeInvokeDelegate(e, r) { var n = r.method, o = e.iterator[n]; if (o === t) return r.delegate = null, "throw" === n && e.iterator.return && (r.method = "return", r.arg = t, maybeInvokeDelegate(e, r), "throw" === r.method) || "return" !== n && (r.method = "throw", r.arg = new TypeError("The iterator does not provide a '" + n + "' method")), y; var i = tryCatch(o, e.iterator, r.arg); if ("throw" === i.type) return r.method = "throw", r.arg = i.arg, r.delegate = null, y; var a = i.arg; return a ? a.done ? (r[e.resultName] = a.value, r.next = e.nextLoc, "return" !== r.method && (r.method = "next", r.arg = t), r.delegate = null, y) : a : (r.method = "throw", r.arg = new TypeError("iterator result is not an object"), r.delegate = null, y); } function pushTryEntry(t) { var e = { tryLoc: t[0] }; 1 in t && (e.catchLoc = t[1]), 2 in t && (e.finallyLoc = t[2], e.afterLoc = t[3]), this.tryEntries.push(e); } function resetTryEntry(t) { var e = t.completion || {}; e.type = "normal", delete e.arg, t.completion = e; } function Context(t) { this.tryEntries = [{ tryLoc: "root" }], t.forEach(pushTryEntry, this), this.reset(!0); } function values(e) { if (e || "" === e) { var r = e[a]; if (r) return r.call(e); if ("function" == typeof e.next) return e; if (!isNaN(e.length)) { var o = -1, i = function next() { for (; ++o < e.length;) if (n.call(e, o)) return next.value = e[o], next.done = !1, next; return next.value = t, next.done = !0, next; }; return i.next = i; } } throw new TypeError(_typeof(e) + " is not iterable"); } return GeneratorFunction.prototype = GeneratorFunctionPrototype, o(g, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), o(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, u, "GeneratorFunction"), e.isGeneratorFunction = function (t) { var e = "function" == typeof t && t.constructor; return !!e && (e === GeneratorFunction || "GeneratorFunction" === (e.displayName || e.name)); }, e.mark = function (t) { return Object.setPrototypeOf ? Object.setPrototypeOf(t, GeneratorFunctionPrototype) : (t.__proto__ = GeneratorFunctionPrototype, define(t, u, "GeneratorFunction")), t.prototype = Object.create(g), t; }, e.awrap = function (t) { return { __await: t }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, c, function () { return this; }), e.AsyncIterator = AsyncIterator, e.async = function (t, r, n, o, i) { void 0 === i && (i = Promise); var a = new AsyncIterator(wrap(t, r, n, o), i); return e.isGeneratorFunction(r) ? a : a.next().then(function (t) { return t.done ? t.value : a.next(); }); }, defineIteratorMethods(g), define(g, u, "Generator"), define(g, a, function () { return this; }), define(g, "toString", function () { return "[object Generator]"; }), e.keys = function (t) { var e = Object(t), r = []; for (var n in e) r.push(n); return r.reverse(), function next() { for (; r.length;) { var t = r.pop(); if (t in e) return next.value = t, next.done = !1, next; } return next.done = !0, next; }; }, e.values = values, Context.prototype = { constructor: Context, reset: function reset(e) { if (this.prev = 0, this.next = 0, this.sent = this._sent = t, this.done = !1, this.delegate = null, this.method = "next", this.arg = t, this.tryEntries.forEach(resetTryEntry), !e) for (var r in this) "t" === r.charAt(0) && n.call(this, r) && !isNaN(+r.slice(1)) && (this[r] = t); }, stop: function stop() { this.done = !0; var t = this.tryEntries[0].completion; if ("throw" === t.type) throw t.arg; return this.rval; }, dispatchException: function dispatchException(e) { if (this.done) throw e; var r = this; function handle(n, o) { return a.type = "throw", a.arg = e, r.next = n, o && (r.method = "next", r.arg = t), !!o; } for (var o = this.tryEntries.length - 1; o >= 0; --o) { var i = this.tryEntries[o], a = i.completion; if ("root" === i.tryLoc) return handle("end"); if (i.tryLoc <= this.prev) { var c = n.call(i, "catchLoc"), u = n.call(i, "finallyLoc"); if (c && u) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } else if (c) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); } else { if (!u) throw Error("try statement without catch or finally"); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } } } }, abrupt: function abrupt(t, e) { for (var r = this.tryEntries.length - 1; r >= 0; --r) { var o = this.tryEntries[r]; if (o.tryLoc <= this.prev && n.call(o, "finallyLoc") && this.prev < o.finallyLoc) { var i = o; break; } } i && ("break" === t || "continue" === t) && i.tryLoc <= e && e <= i.finallyLoc && (i = null); var a = i ? i.completion : {}; return a.type = t, a.arg = e, i ? (this.method = "next", this.next = i.finallyLoc, y) : this.complete(a); }, complete: function complete(t, e) { if ("throw" === t.type) throw t.arg; return "break" === t.type || "continue" === t.type ? this.next = t.arg : "return" === t.type ? (this.rval = this.arg = t.arg, this.method = "return", this.next = "end") : "normal" === t.type && e && (this.next = e), y; }, finish: function finish(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.finallyLoc === t) return this.complete(r.completion, r.afterLoc), resetTryEntry(r), y; } }, catch: function _catch(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.tryLoc === t) { var n = r.completion; if ("throw" === n.type) { var o = n.arg; resetTryEntry(r); } return o; } } throw Error("illegal catch attempt"); }, delegateYield: function delegateYield(e, r, n) { return this.delegate = { iterator: values(e), resultName: r, nextLoc: n }, "next" === this.method && (this.arg = t), y; } }, e; }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
window.creditCardCheckCallbackEventProxy = function (response) {
  window.dispatchEvent(new CustomEvent('creditCardCheckCallbackEvent', {
    detail: response
  }));
};
var PayoneCreditCard = function PayoneCreditCard(_ref) {
  var _cardTypes$0$value, _cardTypes$;
  var eventRegistration = _ref.eventRegistration,
    emitResponse = _ref.emitResponse,
    onSubmit = _ref.onSubmit;
  // Data from PayoneBlocksSupport.php - get_payment_method_data()
  var _wc$wcSettings$getSet = wc.wcSettings.getSetting('payone_data'),
    creditCardCheckRequestConfig = _wc$wcSettings$getSet.creditCardCheckRequestConfig,
    cardTypes = _wc$wcSettings$getSet.cardTypes,
    payoneConfig = _wc$wcSettings$getSet.payoneConfig;
  var onPaymentSetup = eventRegistration.onPaymentSetup,
    onCheckoutValidation = eventRegistration.onCheckoutValidation,
    onCheckoutAfterProcessingWithError = eventRegistration.onCheckoutAfterProcessingWithError;
  var responseTypes = emitResponse.responseTypes,
    noticeContexts = emitResponse.noticeContexts;
  var _useState = (0, _element.useState)(''),
    _useState2 = _slicedToArray(_useState, 2),
    cardHolder = _useState2[0],
    setCardHolder = _useState2[1];
  var _useState3 = (0, _element.useState)((_cardTypes$0$value = (_cardTypes$ = cardTypes[0]) === null || _cardTypes$ === void 0 ? void 0 : _cardTypes$.value) !== null && _cardTypes$0$value !== void 0 ? _cardTypes$0$value : ''),
    _useState4 = _slicedToArray(_useState3, 2),
    cardType = _useState4[0],
    setCardType = _useState4[1];
  var _useState5 = (0, _element.useState)(false),
    _useState6 = _slicedToArray(_useState5, 2),
    payoneCheckSucceeded = _useState6[0],
    setPayoneCheckSucceeded = _useState6[1];
  var _useState7 = (0, _element.useState)(null),
    _useState8 = _slicedToArray(_useState7, 2),
    paymentMethodData = _useState8[0],
    setPaymentMethodData = _useState8[1];
  var _useState9 = (0, _element.useState)(null),
    _useState10 = _slicedToArray(_useState9, 2),
    errorMessage = _useState10[0],
    setErrorMessage = _useState10[1];
  var payoneIFrames = (0, _element.useRef)(null);
  var cardHolderInput = (0, _element.useRef)(null);
  (0, _element.useEffect)(function () {
    window.addEventListener('creditCardCheckCallbackEvent', function (_ref2) {
      var detail = _ref2.detail;
      if (detail.status === 'VALID') {
        setPayoneCheckSucceeded(true);
        setPaymentMethodData({
          card_holder: cardHolder,
          card_pseudopan: detail.pseudocardpan,
          card_truncatedpan: detail.truncatedcardpan,
          card_type: detail.cardtype,
          card_expiredate: detail.cardexpiredate
        });

        // Re-Trigger payment processing
        onSubmit();
      } else if (detail.errormessage) {
        setErrorMessage(detail.errormessage);
      }
    });
  }, []);
  (0, _element.useEffect)(function () {
    if (payoneIFrames.current) {
      payoneIFrames.current.setCardType(cardType);
    }
  }, [cardType, payoneIFrames.current]);
  (0, _element.useEffect)(function () {
    var _payoneConfig$fields;
    if (cardHolderInput.current && payoneConfig !== null && payoneConfig !== void 0 && (_payoneConfig$fields = payoneConfig.fields) !== null && _payoneConfig$fields !== void 0 && _payoneConfig$fields.cardholder) {
      cardHolderInput.current.setAttribute('style', payoneConfig.fields.cardholder.style);
      cardHolderInput.current.setAttribute('size', payoneConfig.fields.cardholder.size);
      cardHolderInput.current.setAttribute('maxlength', payoneConfig.fields.cardholder.maxlength);
      cardHolderInput.current.setAttribute('type', payoneConfig.fields.cardholder.type);
    }
  }, [payoneConfig, cardHolderInput.current]);
  (0, _element.useEffect)(function () {
    payoneIFrames.current = new Payone.ClientApi.HostedIFrames(_objectSpread(_objectSpread({}, payoneConfig), {}, {
      returnType: 'handler',
      language: Payone.ClientApi.Language[payoneConfig.language]
    }), _objectSpread(_objectSpread({}, creditCardCheckRequestConfig), {}, {
      mid: creditCardCheckRequestConfig.merchant_id,
      aid: creditCardCheckRequestConfig.account_id,
      portalid: creditCardCheckRequestConfig.portal_id
    }));
  }, [creditCardCheckRequestConfig, payoneConfig]);
  (0, _element.useEffect)(function () {
    return onCheckoutValidation(/*#__PURE__*/_asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
      return _regeneratorRuntime().wrap(function _callee$(_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            if (!payoneCheckSucceeded) {
              _context.next = 2;
              break;
            }
            return _context.abrupt("return", true);
          case 2:
            if (!(cardHolder.length > 50 || cardHolder.match(/[^a-zA-Z \-äöüÄÖÜß]/g))) {
              _context.next = 4;
              break;
            }
            return _context.abrupt("return", setErrorMessage((0, _i18n.__)(
            // eslint-disable-next-line max-len
            'Bitte geben Sie maximal 50 Zeichen für den Karteninhaber ein, Sonderzeichen außer Deutsche Umlaute und einem Bindestrich sind nicht erlaubt.', 'payone-woocommerce-3')));
          case 4:
            if (payoneIFrames.current.isComplete()) {
              _context.next = 6;
              break;
            }
            return _context.abrupt("return", setErrorMessage((0, _i18n.__)('Bitte Formular vollständig ausfüllen!', 'payone-woocommerce-3')));
          case 6:
            payoneIFrames.current.creditCardCheck('creditCardCheckCallbackEventProxy');

            // Prevent automatical submit
            return _context.abrupt("return", false);
          case 8:
          case "end":
            return _context.stop();
        }
      }, _callee);
    })));
  }, [onCheckoutValidation, payoneCheckSucceeded, cardHolder]);
  (0, _element.useEffect)(function () {
    return onPaymentSetup(function () {
      if (errorMessage) {
        return {
          type: responseTypes.ERROR,
          message: errorMessage
        };
      }
      if (payoneCheckSucceeded) {
        return {
          type: responseTypes.SUCCESS,
          meta: {
            paymentMethodData: paymentMethodData
          }
        };
      }
      return {
        type: responseTypes.ERROR,
        message: (0, _i18n.__)('Die Zahlung konnte nicht erfolgreich durchgeführt werden.', 'payone-woocommerce-3')
      };
    });
  }, [onPaymentSetup, paymentMethodData, errorMessage]);

  // hook into and register callbacks for events.
  (0, _element.useEffect)(function () {
    return function () {
      return onCheckoutAfterProcessingWithError(function (_ref4) {
        var _processingResponse$p;
        var processingResponse = _ref4.processingResponse;
        if (processingResponse !== null && processingResponse !== void 0 && (_processingResponse$p = processingResponse.paymentDetails) !== null && _processingResponse$p !== void 0 && _processingResponse$p.errorMessage) {
          return {
            type: responseTypes.ERROR,
            message: processingResponse.paymentDetails.errorMessage,
            messageContext: noticeContexts.PAYMENTS
          };
        }

        // so we don't break the observers.
        return true;
      });
    };
  }, [onCheckoutAfterProcessingWithError, noticeContexts.PAYMENTS, responseTypes.ERROR]);
  return /*#__PURE__*/React.createElement("fieldset", null, /*#__PURE__*/React.createElement("div", {
    className: "form-row form-row-wide"
  }, /*#__PURE__*/React.createElement("label", {
    htmlFor: "card_holder",
    title: (0, _i18n.__)('as printed on card', 'payone-woocommerce-3')
  }, (0, _i18n.__)('Card Holder', 'payone-woocommerce-3')), /*#__PURE__*/React.createElement("input", {
    className: "payoneInput",
    ref: cardHolderInput,
    id: "card_holder",
    type: "text",
    name: "card_holder",
    value: cardHolder,
    onChange: function onChange(e) {
      return setCardHolder(e.target.value);
    },
    maxLength: "50"
  })), /*#__PURE__*/React.createElement("div", {
    className: "form-row form-row-wide"
  }, /*#__PURE__*/React.createElement("label", {
    htmlFor: "cardtype"
  }, (0, _i18n.__)('Card type', 'payone-woocommerce-3')), /*#__PURE__*/React.createElement("select", {
    id: "cardtype",
    className: "payoneSelect",
    onChange: function onChange(e) {
      return setCardType(e.target.value);
    }
  }, cardTypes.map(function (_ref5) {
    var value = _ref5.value,
      title = _ref5.title;
    return /*#__PURE__*/React.createElement("option", {
      key: value,
      value: value,
      selected: cardType === value
    }, title);
  }))), /*#__PURE__*/React.createElement("div", {
    className: "form-row form-row-wide"
  }, /*#__PURE__*/React.createElement("label", {
    htmlFor: "cardpan"
  }, (0, _i18n.__)('Cardpan', 'payone-woocommerce-3')), /*#__PURE__*/React.createElement("div", {
    className: "inputIframe",
    id: "cardpan"
  })), /*#__PURE__*/React.createElement("div", {
    className: "form-row form-row-wide"
  }, /*#__PURE__*/React.createElement("label", {
    htmlFor: "cardcvc2"
  }, (0, _i18n.__)('CVC', 'payone-woocommerce-3')), /*#__PURE__*/React.createElement("div", {
    className: "inputIframe",
    id: "cardcvc2"
  })), /*#__PURE__*/React.createElement("div", {
    className: "form-row form-row-wide"
  }, /*#__PURE__*/React.createElement("label", {
    htmlFor: "expireInput"
  }, (0, _i18n.__)('Expire Date', 'payone-woocommerce-3')), /*#__PURE__*/React.createElement("div", {
    className: "inputIframe",
    id: "expireInput"
  }, /*#__PURE__*/React.createElement("span", {
    id: "cardexpiremonth"
  }), /*#__PURE__*/React.createElement("span", {
    id: "cardexpireyear"
  }))));
};
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('bs_payone_creditcard', (0, _i18n.__)('PAYONE Kreditkarte', 'payone-woocommerce-3'), "".concat(_constants.PAYONE_ASSETS_URL, "/icon-creditcard.png"), /*#__PURE__*/React.createElement(PayoneCreditCard, null));

/***/ }),

/***/ "./client/blocks/eps/index.jsx":
/*!*************************************!*\
  !*** ./client/blocks/eps/index.jsx ***!
  \*************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _element = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
var _blocksCheckout = __webpack_require__(/*! @woocommerce/blocks-checkout */ "@woocommerce/blocks-checkout");
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
var PayoneEps = function PayoneEps(_ref) {
  var onPaymentSetup = _ref.eventRegistration.onPaymentSetup,
    responseTypes = _ref.emitResponse.responseTypes;
  var _wc$wcSettings$getSet = wc.wcSettings.getSetting('payone_data'),
    epsBankGroups = _wc$wcSettings$getSet.epsBankGroups;
  var _useState = (0, _element.useState)(Object.keys(epsBankGroups)[0]),
    _useState2 = _slicedToArray(_useState, 2),
    bankgroupType = _useState2[0],
    setBankgroupType = _useState2[1];
  (0, _element.useEffect)(function () {
    // TODO: Fehlermeldungen von der API für den User lesbar zurückgeben

    return onPaymentSetup(function () {
      if (bankgroupType) {
        return {
          type: responseTypes.SUCCESS,
          meta: {
            paymentMethodData: {
              bankgrouptype: bankgroupType
            }
          }
        };
      }
      return {
        type: responseTypes.ERROR,
        message: (0, _i18n.__)('Please select a valid bank group!', 'payone-woocommerce-3')
      };
    });
  }, [onPaymentSetup, bankgroupType]);
  return /*#__PURE__*/React.createElement("div", {
    className: "wc-block-sort-select wc-block-components-sort-select"
  }, /*#__PURE__*/React.createElement(_blocksCheckout.Label, {
    label: (0, _i18n.__)('Bank group', 'payone-woocommerce-3'),
    screenReaderLabel: (0, _i18n.__)('Select bank group', 'payone-woocommerce-3'),
    wrapperElement: "label",
    wrapperProps: {
      className: 'wc-block-sort-select__label wc-block-components-sort-select__label',
      htmlFor: 'bankgrouptype'
    }
  }), /*#__PURE__*/React.createElement("select", {
    id: "bankgrouptype",
    className: "wc-block-sort-select__select wc-block-components-sort-select__select payoneSelect",
    onChange: function onChange(e) {
      return setBankgroupType(e.target.value);
    },
    value: bankgroupType
  }, Object.keys(epsBankGroups).map(function (key) {
    return /*#__PURE__*/React.createElement("option", {
      key: key,
      value: key
    }, epsBankGroups[key]);
  })));
};
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('bs_payone_eps', (0, _i18n.__)('PAYONE eps', 'payone-woocommerce-3'), "".concat(_constants.PAYONE_ASSETS_URL, "/icon-eps.png"), /*#__PURE__*/React.createElement(PayoneEps, null));

/***/ }),

/***/ "./client/blocks/ideal/index.jsx":
/*!***************************************!*\
  !*** ./client/blocks/ideal/index.jsx ***!
  \***************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _element = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
var _blocksCheckout = __webpack_require__(/*! @woocommerce/blocks-checkout */ "@woocommerce/blocks-checkout");
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
var PayoneIdeal = function PayoneIdeal(_ref) {
  var onPaymentSetup = _ref.eventRegistration.onPaymentSetup,
    responseTypes = _ref.emitResponse.responseTypes;
  var _wc$wcSettings$getSet = wc.wcSettings.getSetting('payone_data'),
    idealBankGroups = _wc$wcSettings$getSet.idealBankGroups;
  var _useState = (0, _element.useState)(Object.keys(idealBankGroups)[0]),
    _useState2 = _slicedToArray(_useState, 2),
    bankgroupType = _useState2[0],
    setBankgroupType = _useState2[1];
  (0, _element.useEffect)(function () {
    // TODO: Server antwortet mit Fehlercode 923 "Payment type not available for this currency or card type"
    // TODO: Fehlermeldungen von der API für den User lesbar zurückgeben

    return onPaymentSetup(function () {
      if (!bankgroupType) {
        return {
          type: responseTypes.ERROR,
          message: (0, _i18n.__)('Please select a valid bank group!', 'payone-woocommerce-3')
        };
      }
      return {
        type: responseTypes.SUCCESS,
        meta: {
          paymentMethodData: {
            bankgrouptype: bankgroupType
          }
        }
      };
    });
  }, [onPaymentSetup, bankgroupType]);
  return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("div", {
    className: "wc-block-sort-select wc-block-components-sort-select"
  }, /*#__PURE__*/React.createElement(_blocksCheckout.Label, {
    label: (0, _i18n.__)('Bank group', 'payone-woocommerce-3'),
    screenReaderLabel: (0, _i18n.__)('Select bank group', 'payone-woocommerce-3'),
    wrapperElement: "label",
    wrapperProps: {
      className: 'wc-block-sort-select__label wc-block-components-sort-select__label',
      htmlFor: 'bankgrouptype'
    }
  }), /*#__PURE__*/React.createElement("select", {
    id: "bankgrouptype",
    className: "wc-block-sort-select__select wc-block-components-sort-select__select payoneSelect",
    onChange: function onChange(e) {
      return setBankgroupType(e.target.value);
    },
    value: bankgroupType
  }, Object.keys(idealBankGroups).map(function (key) {
    return /*#__PURE__*/React.createElement("option", {
      key: key,
      value: key
    }, idealBankGroups[key]);
  }))));
};
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('payone_ideal', (0, _i18n.__)('PAYONE iDEAL', 'payone-woocommerce-3'), 'https://cdn.pay1.de/clearingtypes/sb/idl/default.svg', /*#__PURE__*/React.createElement(PayoneIdeal, null));

/***/ }),

/***/ "./client/blocks/invoice/index.jsx":
/*!*****************************************!*\
  !*** ./client/blocks/invoice/index.jsx ***!
  \*****************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('bs_payone_invoice', (0, _i18n.__)('PAYONE Invoice', 'payone-woocommerce-3'), "".concat(_constants.PAYONE_ASSETS_URL, "/icon-rechnungskauf.png"));

/***/ }),

/***/ "./client/blocks/klarna/KlarnaService.js":
/*!***********************************************!*\
  !*** ./client/blocks/klarna/KlarnaService.js ***!
  \***********************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = exports.KLARNA_GATEWAY_IDS = exports.KLARNA_CATEGORIES = void 0;
var _AssetService = _interopRequireDefault(__webpack_require__(/*! ../../services/AssetService */ "./client/services/AssetService.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
/**
 * @readonly
 * @enum {string}
 */
var KLARNA_GATEWAY_IDS = exports.KLARNA_GATEWAY_IDS = {
  SOFORT: 'payone_klarna_sofort',
  INVOICE: 'payone_klarna_invoice',
  INSTALLMENTS: 'payone_klarna_installments'
};
var KLARNA_CATEGORIES = exports.KLARNA_CATEGORIES = {
  PAY_LATER: 'pay_later',
  PAY_OVER_TIME: 'pay_over_time',
  DIRECT_DEBIT: 'direct_debit'
};
var KlarnaService = exports["default"] = /*#__PURE__*/function () {
  function KlarnaService() {
    _classCallCheck(this, KlarnaService);
  }
  return _createClass(KlarnaService, null, [{
    key: "getCategoryForKlarnaGatewayId",
    value:
    /**
     * @param {string} gatewayId
     * @return {string}
     */
    function getCategoryForKlarnaGatewayId(gatewayId) {
      var klarnaCategories = _defineProperty(_defineProperty(_defineProperty({}, KLARNA_GATEWAY_IDS.INVOICE, KLARNA_CATEGORIES.PAY_LATER), KLARNA_GATEWAY_IDS.INSTALLMENTS, KLARNA_CATEGORIES.PAY_OVER_TIME), KLARNA_GATEWAY_IDS.SOFORT, KLARNA_CATEGORIES.DIRECT_DEBIT);
      if ({}.hasOwnProperty.call(klarnaCategories, gatewayId)) {
        return klarnaCategories[gatewayId];
      }
      return '';
    }
  }, {
    key: "loadKlarnaScript",
    value: function loadKlarnaScript() {
      if (!window.klarnaApiInitiated) {
        _AssetService.default.loadJsScript('https://x.klarnacdn.net/kp/lib/v1/api.js', function () {
          window.klarnaApiInitiated = true;
        });
      }
    }
  }]);
}();

/***/ }),

/***/ "./client/blocks/klarna/installments.jsx":
/*!***********************************************!*\
  !*** ./client/blocks/klarna/installments.jsx ***!
  \***********************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _IconLabel = _interopRequireDefault(__webpack_require__(/*! ../../components/IconLabel */ "./client/components/IconLabel.jsx"));
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _klarnaBase = _interopRequireDefault(__webpack_require__(/*! ./klarna-base */ "./client/blocks/klarna/klarna-base.jsx"));
var _KlarnaService = __webpack_require__(/*! ./KlarnaService */ "./client/blocks/klarna/KlarnaService.js");
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
var PAYMENT_METHOD_NAME = 'payone_klarna_installments';
var label = (0, _i18n.__)('PAYONE Klarna Ratenkauf', 'payone-woocommerce-3');
var KlarnaInstallments = function KlarnaInstallments(paymentMethodProps) {
  return /*#__PURE__*/React.createElement(_klarnaBase.default, _extends({
    klarnaCategory: _KlarnaService.KLARNA_CATEGORIES.PAY_OVER_TIME,
    label: label
  }, paymentMethodProps));
};
var _default = exports["default"] = {
  name: PAYMENT_METHOD_NAME,
  label: /*#__PURE__*/React.createElement(_IconLabel.default, {
    text: label,
    icon: "".concat(_constants.PAYONE_ASSETS_URL, "/icon-klarna.png")
  }),
  ariaLabel: label,
  content: /*#__PURE__*/React.createElement(KlarnaInstallments, null),
  edit: /*#__PURE__*/React.createElement(KlarnaInstallments, null),
  canMakePayment: function canMakePayment() {
    return true;
  },
  paymentMethodId: PAYMENT_METHOD_NAME,
  supports: {
    showSavedCards: false,
    showSaveOption: false
  }
};

/***/ }),

/***/ "./client/blocks/klarna/invoice.jsx":
/*!******************************************!*\
  !*** ./client/blocks/klarna/invoice.jsx ***!
  \******************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _IconLabel = _interopRequireDefault(__webpack_require__(/*! ../../components/IconLabel */ "./client/components/IconLabel.jsx"));
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _klarnaBase = _interopRequireDefault(__webpack_require__(/*! ./klarna-base */ "./client/blocks/klarna/klarna-base.jsx"));
var _KlarnaService = __webpack_require__(/*! ./KlarnaService */ "./client/blocks/klarna/KlarnaService.js");
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
var PAYMENT_METHOD_NAME = 'payone_klarna_invoice';
var label = (0, _i18n.__)('PAYONE Klarna Rechnung', 'payone-woocommerce-3');
var KlarnaInvoice = function KlarnaInvoice(paymentMethodProps) {
  return /*#__PURE__*/React.createElement(_klarnaBase.default, _extends({
    klarnaCategory: _KlarnaService.KLARNA_CATEGORIES.PAY_LATER,
    label: label
  }, paymentMethodProps));
};
var _default = exports["default"] = {
  name: PAYMENT_METHOD_NAME,
  label: /*#__PURE__*/React.createElement(_IconLabel.default, {
    text: label,
    icon: "".concat(_constants.PAYONE_ASSETS_URL, "/icon-klarna.png")
  }),
  ariaLabel: label,
  content: /*#__PURE__*/React.createElement(KlarnaInvoice, null),
  edit: /*#__PURE__*/React.createElement(KlarnaInvoice, null),
  canMakePayment: function canMakePayment() {
    return true;
  },
  paymentMethodId: PAYMENT_METHOD_NAME,
  supports: {
    showSavedCards: false,
    showSaveOption: false
  }
};

/***/ }),

/***/ "./client/blocks/klarna/klarna-base.jsx":
/*!**********************************************!*\
  !*** ./client/blocks/klarna/klarna-base.jsx ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = KlarnaBase;
var _element = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
var _data = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _KlarnaService = _interopRequireDefault(__webpack_require__(/*! ./KlarnaService */ "./client/blocks/klarna/KlarnaService.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return e; }; var t, e = {}, r = Object.prototype, n = r.hasOwnProperty, o = Object.defineProperty || function (t, e, r) { t[e] = r.value; }, i = "function" == typeof Symbol ? Symbol : {}, a = i.iterator || "@@iterator", c = i.asyncIterator || "@@asyncIterator", u = i.toStringTag || "@@toStringTag"; function define(t, e, r) { return Object.defineProperty(t, e, { value: r, enumerable: !0, configurable: !0, writable: !0 }), t[e]; } try { define({}, ""); } catch (t) { define = function define(t, e, r) { return t[e] = r; }; } function wrap(t, e, r, n) { var i = e && e.prototype instanceof Generator ? e : Generator, a = Object.create(i.prototype), c = new Context(n || []); return o(a, "_invoke", { value: makeInvokeMethod(t, r, c) }), a; } function tryCatch(t, e, r) { try { return { type: "normal", arg: t.call(e, r) }; } catch (t) { return { type: "throw", arg: t }; } } e.wrap = wrap; var h = "suspendedStart", l = "suspendedYield", f = "executing", s = "completed", y = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var p = {}; define(p, a, function () { return this; }); var d = Object.getPrototypeOf, v = d && d(d(values([]))); v && v !== r && n.call(v, a) && (p = v); var g = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(p); function defineIteratorMethods(t) { ["next", "throw", "return"].forEach(function (e) { define(t, e, function (t) { return this._invoke(e, t); }); }); } function AsyncIterator(t, e) { function invoke(r, o, i, a) { var c = tryCatch(t[r], t, o); if ("throw" !== c.type) { var u = c.arg, h = u.value; return h && "object" == _typeof(h) && n.call(h, "__await") ? e.resolve(h.__await).then(function (t) { invoke("next", t, i, a); }, function (t) { invoke("throw", t, i, a); }) : e.resolve(h).then(function (t) { u.value = t, i(u); }, function (t) { return invoke("throw", t, i, a); }); } a(c.arg); } var r; o(this, "_invoke", { value: function value(t, n) { function callInvokeWithMethodAndArg() { return new e(function (e, r) { invoke(t, n, e, r); }); } return r = r ? r.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(e, r, n) { var o = h; return function (i, a) { if (o === f) throw Error("Generator is already running"); if (o === s) { if ("throw" === i) throw a; return { value: t, done: !0 }; } for (n.method = i, n.arg = a;;) { var c = n.delegate; if (c) { var u = maybeInvokeDelegate(c, n); if (u) { if (u === y) continue; return u; } } if ("next" === n.method) n.sent = n._sent = n.arg;else if ("throw" === n.method) { if (o === h) throw o = s, n.arg; n.dispatchException(n.arg); } else "return" === n.method && n.abrupt("return", n.arg); o = f; var p = tryCatch(e, r, n); if ("normal" === p.type) { if (o = n.done ? s : l, p.arg === y) continue; return { value: p.arg, done: n.done }; } "throw" === p.type && (o = s, n.method = "throw", n.arg = p.arg); } }; } function maybeInvokeDelegate(e, r) { var n = r.method, o = e.iterator[n]; if (o === t) return r.delegate = null, "throw" === n && e.iterator.return && (r.method = "return", r.arg = t, maybeInvokeDelegate(e, r), "throw" === r.method) || "return" !== n && (r.method = "throw", r.arg = new TypeError("The iterator does not provide a '" + n + "' method")), y; var i = tryCatch(o, e.iterator, r.arg); if ("throw" === i.type) return r.method = "throw", r.arg = i.arg, r.delegate = null, y; var a = i.arg; return a ? a.done ? (r[e.resultName] = a.value, r.next = e.nextLoc, "return" !== r.method && (r.method = "next", r.arg = t), r.delegate = null, y) : a : (r.method = "throw", r.arg = new TypeError("iterator result is not an object"), r.delegate = null, y); } function pushTryEntry(t) { var e = { tryLoc: t[0] }; 1 in t && (e.catchLoc = t[1]), 2 in t && (e.finallyLoc = t[2], e.afterLoc = t[3]), this.tryEntries.push(e); } function resetTryEntry(t) { var e = t.completion || {}; e.type = "normal", delete e.arg, t.completion = e; } function Context(t) { this.tryEntries = [{ tryLoc: "root" }], t.forEach(pushTryEntry, this), this.reset(!0); } function values(e) { if (e || "" === e) { var r = e[a]; if (r) return r.call(e); if ("function" == typeof e.next) return e; if (!isNaN(e.length)) { var o = -1, i = function next() { for (; ++o < e.length;) if (n.call(e, o)) return next.value = e[o], next.done = !1, next; return next.value = t, next.done = !0, next; }; return i.next = i; } } throw new TypeError(_typeof(e) + " is not iterable"); } return GeneratorFunction.prototype = GeneratorFunctionPrototype, o(g, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), o(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, u, "GeneratorFunction"), e.isGeneratorFunction = function (t) { var e = "function" == typeof t && t.constructor; return !!e && (e === GeneratorFunction || "GeneratorFunction" === (e.displayName || e.name)); }, e.mark = function (t) { return Object.setPrototypeOf ? Object.setPrototypeOf(t, GeneratorFunctionPrototype) : (t.__proto__ = GeneratorFunctionPrototype, define(t, u, "GeneratorFunction")), t.prototype = Object.create(g), t; }, e.awrap = function (t) { return { __await: t }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, c, function () { return this; }), e.AsyncIterator = AsyncIterator, e.async = function (t, r, n, o, i) { void 0 === i && (i = Promise); var a = new AsyncIterator(wrap(t, r, n, o), i); return e.isGeneratorFunction(r) ? a : a.next().then(function (t) { return t.done ? t.value : a.next(); }); }, defineIteratorMethods(g), define(g, u, "Generator"), define(g, a, function () { return this; }), define(g, "toString", function () { return "[object Generator]"; }), e.keys = function (t) { var e = Object(t), r = []; for (var n in e) r.push(n); return r.reverse(), function next() { for (; r.length;) { var t = r.pop(); if (t in e) return next.value = t, next.done = !1, next; } return next.done = !0, next; }; }, e.values = values, Context.prototype = { constructor: Context, reset: function reset(e) { if (this.prev = 0, this.next = 0, this.sent = this._sent = t, this.done = !1, this.delegate = null, this.method = "next", this.arg = t, this.tryEntries.forEach(resetTryEntry), !e) for (var r in this) "t" === r.charAt(0) && n.call(this, r) && !isNaN(+r.slice(1)) && (this[r] = t); }, stop: function stop() { this.done = !0; var t = this.tryEntries[0].completion; if ("throw" === t.type) throw t.arg; return this.rval; }, dispatchException: function dispatchException(e) { if (this.done) throw e; var r = this; function handle(n, o) { return a.type = "throw", a.arg = e, r.next = n, o && (r.method = "next", r.arg = t), !!o; } for (var o = this.tryEntries.length - 1; o >= 0; --o) { var i = this.tryEntries[o], a = i.completion; if ("root" === i.tryLoc) return handle("end"); if (i.tryLoc <= this.prev) { var c = n.call(i, "catchLoc"), u = n.call(i, "finallyLoc"); if (c && u) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } else if (c) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); } else { if (!u) throw Error("try statement without catch or finally"); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } } } }, abrupt: function abrupt(t, e) { for (var r = this.tryEntries.length - 1; r >= 0; --r) { var o = this.tryEntries[r]; if (o.tryLoc <= this.prev && n.call(o, "finallyLoc") && this.prev < o.finallyLoc) { var i = o; break; } } i && ("break" === t || "continue" === t) && i.tryLoc <= e && e <= i.finallyLoc && (i = null); var a = i ? i.completion : {}; return a.type = t, a.arg = e, i ? (this.method = "next", this.next = i.finallyLoc, y) : this.complete(a); }, complete: function complete(t, e) { if ("throw" === t.type) throw t.arg; return "break" === t.type || "continue" === t.type ? this.next = t.arg : "return" === t.type ? (this.rval = this.arg = t.arg, this.method = "return", this.next = "end") : "normal" === t.type && e && (this.next = e), y; }, finish: function finish(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.finallyLoc === t) return this.complete(r.completion, r.afterLoc), resetTryEntry(r), y; } }, catch: function _catch(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.tryLoc === t) { var n = r.completion; if ("throw" === n.type) { var o = n.arg; resetTryEntry(r); } return o; } } throw Error("illegal catch attempt"); }, delegateYield: function delegateYield(e, r, n) { return this.delegate = { iterator: values(e), resultName: r, nextLoc: n }, "next" === this.method && (this.arg = t), y; } }, e; }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
/* global Klarna */
function KlarnaBase(props) {
  var _props$eventRegistrat = props.eventRegistration,
    onPaymentSetup = _props$eventRegistrat.onPaymentSetup,
    onCheckoutValidation = _props$eventRegistrat.onCheckoutValidation,
    responseTypes = props.emitResponse.responseTypes,
    onSubmit = props.onSubmit;
  var klarnaCategory = props.klarnaCategory,
    label = props.label;
  var _useState = (0, _element.useState)(false),
    _useState2 = _slicedToArray(_useState, 2),
    widgetShown = _useState2[0],
    setWidgetShown = _useState2[1];
  var _useState3 = (0, _element.useState)(null),
    _useState4 = _slicedToArray(_useState3, 2),
    klarnaWorkOrderId = _useState4[0],
    setKlarnaWorkOrderId = _useState4[1];
  var _useState5 = (0, _element.useState)(null),
    _useState6 = _slicedToArray(_useState5, 2),
    paymentMethodData = _useState6[0],
    setPaymentMethodData = _useState6[1];
  var _useState7 = (0, _element.useState)(false),
    _useState8 = _slicedToArray(_useState7, 2),
    klarnaCheckSucceeded = _useState8[0],
    setKlarnaCheckSucceeded = _useState8[1];
  var _useState9 = (0, _element.useState)(null),
    _useState10 = _slicedToArray(_useState9, 2),
    errorMessage = _useState10[0],
    setErrorMessage = _useState10[1];
  var _useState11 = (0, _element.useState)(null),
    _useState12 = _slicedToArray(_useState11, 2),
    klarnaData = _useState12[0],
    setKlarnaData = _useState12[1];
  var _wc$wcSettings$getSet = wc.wcSettings.getSetting('payone_data'),
    klarnaStartSessionUrl = _wc$wcSettings$getSet.klarnaStartSessionUrl;
  var CART_STORE_KEY = wc.wcBlocksData.CART_STORE_KEY;
  var initKlarnaWidget = function initKlarnaWidget() {
    _KlarnaService.default.loadKlarnaScript();
    var store = (0, _data.select)(CART_STORE_KEY);
    var _store$getCartData = store.getCartData(),
      billingAddress = _store$getCartData.billingAddress,
      shippingAddress = _store$getCartData.shippingAddress;
    var body = new FormData();
    body.append('category', klarnaCategory);
    body.append('currency', wc.wcSettings.CURRENCY.code);
    body.append('country', billingAddress.country);
    body.append('firstname', billingAddress.first_name);
    body.append('lastname', billingAddress.last_name);
    body.append('company', billingAddress.company);
    body.append('street', billingAddress.address_1);
    body.append('addressaddition', billingAddress.address_2);
    body.append('zip', billingAddress.postcode);
    body.append('city', billingAddress.city);
    body.append('email', billingAddress.email);
    body.append('telephonenumber', billingAddress.phone);
    body.append('shipping_country', shippingAddress.country);
    body.append('shipping_firstname', shippingAddress.first_name);
    body.append('shipping_lastname', shippingAddress.last_name);
    body.append('shipping_company', shippingAddress.company);
    body.append('shipping_street', shippingAddress.address_1);
    body.append('shipping_addressaddition', shippingAddress.address_2);
    body.append('shipping_zip', shippingAddress.postcode);
    body.append('shipping_city', shippingAddress.city);
    body.append('shipping_email', billingAddress.email);
    body.append('shipping_telephonenumber', billingAddress.phone);
    fetch(klarnaStartSessionUrl, {
      method: 'POST',
      body: body
    }).then(function (response) {
      return response.json();
    }).then(function (json) {
      if (json.status === 'ok') {
        setKlarnaWorkOrderId(json.workorderid);
        setKlarnaData(json.data);
        Klarna.Payments.init({
          client_token: json.client_token
        });
        Klarna.Payments.load({
          container: "#klarna_".concat(klarnaCategory, "_container"),
          payment_method_category: klarnaCategory
        }, function (klarnaResult) {
          if (klarnaResult.show_form) {
            setWidgetShown(true);
            setErrorMessage(null);
          } else {
            setErrorMessage((0, _i18n.__)("".concat(label, " kann nicht genutzt werden!"), 'payone-woocommerce-3'));
          }
        });
      } else if (json.message) {
        setErrorMessage(json.message);
      }
    });
  };
  (0, _element.useEffect)(function () {
    return initKlarnaWidget();
  }, []);
  (0, _element.useEffect)(function () {
    return onCheckoutValidation(/*#__PURE__*/_asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
      var store, _store$getCartData2, billingAddress;
      return _regeneratorRuntime().wrap(function _callee$(_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            if (!klarnaCheckSucceeded) {
              _context.next = 2;
              break;
            }
            return _context.abrupt("return", true);
          case 2:
            if (!errorMessage) {
              _context.next = 4;
              break;
            }
            return _context.abrupt("return", false);
          case 4:
            store = (0, _data.select)(CART_STORE_KEY);
            _store$getCartData2 = store.getCartData(), billingAddress = _store$getCartData2.billingAddress;
            Klarna.Payments.authorize({
              payment_method_category: klarnaCategory
            }, klarnaData, function (klarnaResult) {
              if (!klarnaResult.approved && !klarnaResult.show_form) {
                setErrorMessage((0, _i18n.__)("".concat(label, " kann nicht genutzt werden!"), 'payone-woocommerce-3'));
              } else if (!klarnaResult.approved) {
                setErrorMessage((0, _i18n.__)('Der Vorgang wurde abgebrochen', 'payone-woocommerce-3'));
              } else {
                setErrorMessage(null);
                setPaymentMethodData({
                  klarna_authorization_token: klarnaResult.klarna_authorization_token,
                  klarna_workorderid: klarnaWorkOrderId,
                  klarna_shipping_email: billingAddress.email,
                  klarna_shipping_telephonenumber: billingAddress.phone
                });
                setKlarnaCheckSucceeded(true);

                // Re-Trigger payment processing
                onSubmit();
              }
            });

            // Prevent automatical submit
            return _context.abrupt("return", false);
          case 8:
          case "end":
            return _context.stop();
        }
      }, _callee);
    })));
  }, [onCheckoutValidation, klarnaCheckSucceeded, klarnaWorkOrderId, errorMessage]);
  (0, _element.useEffect)(function () {
    return onPaymentSetup(function () {
      if (errorMessage) {
        return {
          type: responseTypes.ERROR,
          message: errorMessage
        };
      }
      if (!widgetShown) {
        initKlarnaWidget();
      }
      if (klarnaCheckSucceeded && paymentMethodData) {
        return {
          type: responseTypes.SUCCESS,
          meta: {
            paymentMethodData: paymentMethodData
          }
        };
      }
      return false;
    });
  }, [onPaymentSetup, klarnaCheckSucceeded, paymentMethodData]);
  return /*#__PURE__*/React.createElement(React.Fragment, null, errorMessage ? /*#__PURE__*/React.createElement("strong", {
    style: {
      color: 'red'
    }
  }, errorMessage) : null, /*#__PURE__*/React.createElement("div", {
    id: "klarna_".concat(klarnaCategory, "_container")
  }));
}

/***/ }),

/***/ "./client/blocks/klarna/sofort.jsx":
/*!*****************************************!*\
  !*** ./client/blocks/klarna/sofort.jsx ***!
  \*****************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _IconLabel = _interopRequireDefault(__webpack_require__(/*! ../../components/IconLabel */ "./client/components/IconLabel.jsx"));
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _klarnaBase = _interopRequireDefault(__webpack_require__(/*! ./klarna-base */ "./client/blocks/klarna/klarna-base.jsx"));
var _KlarnaService = __webpack_require__(/*! ./KlarnaService */ "./client/blocks/klarna/KlarnaService.js");
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }
var PAYMENT_METHOD_NAME = 'payone_klarna_sofort';
var label = (0, _i18n.__)('PAYONE Klarna Sofort bezahlen', 'payone-woocommerce-3');
var KlarnaInvoice = function KlarnaInvoice(paymentMethodProps) {
  return /*#__PURE__*/React.createElement(_klarnaBase.default, _extends({
    klarnaCategory: _KlarnaService.KLARNA_CATEGORIES.DIRECT_DEBIT,
    label: label
  }, paymentMethodProps));
};
var _default = exports["default"] = {
  name: PAYMENT_METHOD_NAME,
  label: /*#__PURE__*/React.createElement(_IconLabel.default, {
    text: label,
    icon: "".concat(_constants.PAYONE_ASSETS_URL, "/icon-klarna.png")
  }),
  ariaLabel: label,
  content: /*#__PURE__*/React.createElement(KlarnaInvoice, null),
  edit: /*#__PURE__*/React.createElement(KlarnaInvoice, null),
  canMakePayment: function canMakePayment() {
    return true;
  },
  paymentMethodId: PAYMENT_METHOD_NAME,
  supports: {
    showSavedCards: false,
    showSaveOption: false
  }
};

/***/ }),

/***/ "./client/blocks/payla/disclaimer.jsx":
/*!********************************************!*\
  !*** ./client/blocks/payla/disclaimer.jsx ***!
  \********************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = PaylaDisclaimer;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
function PaylaDisclaimer() {
  var data = (0, _i18n.__)("By placing this order, I agree to the \n         <a href=\"https://legal.paylater.payone.com/en/terms-of-payment.html\" target=\"_blank\" rel=\"noopener\">\n            supplementary payment terms\n         </a> and the performance of a risk assessment for the selected payment method. I am aware of the \n         <a href=\"https://legal.paylater.payone.com/en/data-protection-payments.html\" target=\"_blank\" rel=\"noopener\">\n            supplementary data protection notice\n         </a>.", 'payone-woocommerce-3');
  return /*#__PURE__*/React.createElement("p", {
    className: "wc-block-checkout__terms wp-block-woocommerce-checkout-terms-block",
    dangerouslySetInnerHTML: {
      __html: data
    }
  });
}

/***/ }),

/***/ "./client/blocks/payla/secured-direct-debit.jsx":
/*!******************************************************!*\
  !*** ./client/blocks/payla/secured-direct-debit.jsx ***!
  \******************************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _element = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
var _blocksCheckout = __webpack_require__(/*! @woocommerce/blocks-checkout */ "@woocommerce/blocks-checkout");
var _iban = _interopRequireDefault(__webpack_require__(/*! iban */ "./node_modules/iban/iban.js"));
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _AssetService = _interopRequireDefault(__webpack_require__(/*! ../../services/AssetService */ "./client/services/AssetService.js"));
var _disclaimer = _interopRequireDefault(__webpack_require__(/*! ./disclaimer */ "./client/blocks/payla/disclaimer.jsx"));
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
var PaylaSecuredDirectDebit = function PaylaSecuredDirectDebit(_ref) {
  var onPaymentSetup = _ref.eventRegistration.onPaymentSetup,
    responseTypes = _ref.emitResponse.responseTypes;
  var _wc$wcSettings$getSet = wc.wcSettings.getSetting('payone_data'),
    paylaConfig = _wc$wcSettings$getSet.paylaConfig;
  var _useState = (0, _element.useState)(''),
    _useState2 = _slicedToArray(_useState, 2),
    birthday = _useState2[0],
    setBirthday = _useState2[1];
  var _useState3 = (0, _element.useState)(''),
    _useState4 = _slicedToArray(_useState3, 2),
    iban = _useState4[0],
    setIban = _useState4[1];
  (0, _element.useEffect)(function () {
    return onPaymentSetup(function () {
      if (!birthday) {
        return {
          type: responseTypes.ERROR,
          message: (0, _i18n.__)('Please enter your birthday!', 'payone-woocommerce-3')
        };
      }
      return {
        type: responseTypes.SUCCESS,
        meta: {
          paymentMethodData: {
            payone_secured_direct_debit_birthday: birthday,
            payone_secured_direct_debit_iban: iban,
            payone_secured_direct_debit_token: paylaConfig.tokenSecuredDirectDebit
          }
        }
      };
    });
  }, [onPaymentSetup, birthday, iban]);
  (0, _element.useEffect)(function () {
    _AssetService.default.loadJsScript(paylaConfig.jsUrl, function () {
      /* global paylaDcs */
      if (typeof paylaDcs !== 'undefined') {
        paylaDcs.init(paylaConfig.environmentKey, paylaConfig.tokenSecuredDirectDebit);
      }
    });
    _AssetService.default.loadCssStylesheet(paylaConfig.cssUrl);
  }, []);
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(_blocksCheckout.ValidatedTextInput, {
    type: "date",
    id: "payone_secured_invoice_birthday",
    className: "payone-validated-date-input is-active",
    label: (0, _i18n.__)('Birthday', 'payone-woocommerce-3'),
    onChange: function onChange(value) {
      return setBirthday(value);
    },
    value: birthday,
    required: true
  }), /*#__PURE__*/React.createElement(_blocksCheckout.ValidatedTextInput, {
    id: "ratepay_installments_iban",
    type: "text",
    label: (0, _i18n.__)('IBAN', 'payone-woocommerce-3'),
    onChange: function onChange(value) {
      return setIban(value);
    },
    customValidation: function customValidation(inputObject) {
      if (!_iban.default.isValid(inputObject.value)) {
        inputObject.setCustomValidity((0, _i18n.__)('Please enter a valid IBAN!', 'payone-woocommerce-3'));
        return false;
      }
      return true;
    },
    value: iban,
    required: true
  }), /*#__PURE__*/React.createElement(_disclaimer.default, null));
};
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('payone_secured_direct_debit', (0, _i18n.__)('PAYONE Secured Direct Debit', 'payone-woocommerce-3'), "".concat(_constants.PAYONE_ASSETS_URL, "/icon-secured-lastschrift.png"), /*#__PURE__*/React.createElement(PaylaSecuredDirectDebit, null));

/***/ }),

/***/ "./client/blocks/payla/secured-installment.jsx":
/*!*****************************************************!*\
  !*** ./client/blocks/payla/secured-installment.jsx ***!
  \*****************************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _element = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
var _blocksCheckout = __webpack_require__(/*! @woocommerce/blocks-checkout */ "@woocommerce/blocks-checkout");
var _iban = _interopRequireDefault(__webpack_require__(/*! iban */ "./node_modules/iban/iban.js"));
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _AssetService = _interopRequireDefault(__webpack_require__(/*! ../../services/AssetService */ "./client/services/AssetService.js"));
var _disclaimer = _interopRequireDefault(__webpack_require__(/*! ./disclaimer */ "./client/blocks/payla/disclaimer.jsx"));
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
var InstallmentOptionsTable = function InstallmentOptionsTable(_ref) {
  var options = _ref.options,
    onSelect = _ref.onSelect;
  var columnLabels = {
    number_of_payments: (0, _i18n.__)('Numberja  of payments', 'payone-woocommerce-3'),
    monthly_amount: (0, _i18n.__)('Monthly rate', 'payone-woocommerce-3'),
    total_amount_value: (0, _i18n.__)('Total amount', 'payone-woocommerce-3'),
    nominal_interest_rate: (0, _i18n.__)('Interest rate', 'payone-woocommerce-3'),
    effective_interest_rate: (0, _i18n.__)('Annual percentage rate', 'payone-woocommerce-3')
  };
  return options.map(function (row, index) {
    var headline = (0, _i18n.__)('Payable in __num_installments__ installments, each __monthly_amount__', 'payone-woocommerce-3').replace('__num_installments__', row.number_of_payments).replace('__monthly_amount__', row.monthly_amount);
    return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("input", {
      type: "radio",
      className: "input-radio",
      id: "payone_secured_installment_option_".concat(index),
      name: "payone_secured_installment_option",
      value: row.option_id,
      onClick: function onClick() {
        return onSelect(row.option_id);
      }
    }), /*#__PURE__*/React.createElement("label", {
      htmlFor: "payone_secured_installment_option_".concat(index)
    }, headline), selectedOption === row.option_id ? /*#__PURE__*/React.createElement("table", {
      style: {
        marginTop: '0.5rem'
      }
    }, /*#__PURE__*/React.createElement("tbody", null, Object.keys(columnLabels).map(function (key) {
      return /*#__PURE__*/React.createElement("tr", null, /*#__PURE__*/React.createElement("th", null, columnLabels[key]), /*#__PURE__*/React.createElement("td", null, row[key].replace('&nbsp;', ' ')));
    }), /*#__PURE__*/React.createElement("tr", null, /*#__PURE__*/React.createElement("th", {
      colSpan: 2
    }, /*#__PURE__*/React.createElement("a", {
      href: row.info_url,
      target: "_blank",
      rel: "noopener"
    }, (0, _i18n.__)('Link to credit information', 'payone-woocommerce-3')))))) : null);
  });
};
var PaylaSecuredInstallment = function PaylaSecuredInstallment(_ref2) {
  var onPaymentSetup = _ref2.eventRegistration.onPaymentSetup,
    responseTypes = _ref2.emitResponse.responseTypes;
  var _wc$wcSettings$getSet = wc.wcSettings.getSetting('payone_data'),
    paylaConfig = _wc$wcSettings$getSet.paylaConfig;
  var _useState = (0, _element.useState)(''),
    _useState2 = _slicedToArray(_useState, 2),
    birthday = _useState2[0],
    setBirthday = _useState2[1];
  var _useState3 = (0, _element.useState)(''),
    _useState4 = _slicedToArray(_useState3, 2),
    iban = _useState4[0],
    setIban = _useState4[1];
  var _useState5 = (0, _element.useState)(''),
    _useState6 = _slicedToArray(_useState5, 2),
    resultsTable = _useState6[0],
    setResultsTable = _useState6[1];
  var _useState7 = (0, _element.useState)(null),
    _useState8 = _slicedToArray(_useState7, 2),
    selectedOption = _useState8[0],
    setSelectedOption = _useState8[1];
  var _useState9 = (0, _element.useState)(null),
    _useState10 = _slicedToArray(_useState9, 2),
    workOrderId = _useState10[0],
    setWorkOrderId = _useState10[1];
  var initWidget = function initWidget() {
    fetch(paylaConfig.urlSecuredInstallment, {
      method: 'POST'
    }).then(function (response) {
      return response.json();
    }).then(function (json) {
      setWorkOrderId(json.workorderid);
      setResultsTable(json);
    });
  };
  (0, _element.useEffect)(function () {
    return initWidget();
  }, []);
  (0, _element.useEffect)(function () {
    return onPaymentSetup(function () {
      if (!birthday) {
        return {
          type: responseTypes.ERROR,
          message: (0, _i18n.__)('Please enter your birthday!', 'payone-woocommerce-3')
        };
      }
      if (!selectedOption) {
        return {
          type: responseTypes.ERROR,
          message: (0, _i18n.__)('Please choose a payment plan!', 'payone-woocommerce-3')
        };
      }
      return {
        type: responseTypes.SUCCESS,
        meta: {
          paymentMethodData: {
            payone_secured_installment_birthday: birthday,
            payone_secured_installment_iban: iban,
            payone_secured_installment_token: paylaConfig.tokenSecuredInstallment,
            payone_secured_installment_option: selectedOption,
            payone_secured_installment_workorderid: workOrderId
          }
        }
      };
    });
  }, [onPaymentSetup, birthday, iban, selectedOption, workOrderId]);
  (0, _element.useEffect)(function () {
    _AssetService.default.loadJsScript(paylaConfig.jsUrl, function () {
      /* global paylaDcs */
      if (typeof paylaDcs !== 'undefined') {
        paylaDcs.init(paylaConfig.environmentKey, paylaConfig.tokenSecuredInstallment);
      }
    });
    _AssetService.default.loadCssStylesheet(paylaConfig.cssUrl);
  }, []);
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(_blocksCheckout.ValidatedTextInput, {
    type: "date",
    id: "payone_secured_invoice_birthday",
    className: "payone-validated-date-input is-active",
    label: (0, _i18n.__)('Birthday', 'payone-woocommerce-3'),
    onChange: function onChange(value) {
      return setBirthday(value);
    },
    value: birthday,
    required: true
  }), /*#__PURE__*/React.createElement(_blocksCheckout.ValidatedTextInput, {
    id: "ratepay_installments_iban",
    type: "text",
    label: (0, _i18n.__)('IBAN', 'payone-woocommerce-3'),
    onChange: function onChange(value) {
      return setIban(value);
    },
    customValidation: function customValidation(inputObject) {
      if (!_iban.default.isValid(inputObject.value)) {
        inputObject.setCustomValidity((0, _i18n.__)('Please enter a valid IBAN!', 'payone-woocommerce-3'));
        return false;
      }
      return true;
    },
    value: iban,
    required: true
  }), resultsTable ? /*#__PURE__*/React.createElement("div", {
    style: {
      marginTop: '1rem',
      display: 'flex',
      flexDirection: 'column',
      gap: '0.5rem'
    }
  }, /*#__PURE__*/React.createElement("p", {
    style: {
      margin: 0,
      fontWeight: 'bold'
    }
  }, (0, _i18n.__)('Select the number of payments', 'payone-woocommerce-3')), /*#__PURE__*/React.createElement(InstallmentOptionsTable, {
    options: resultsTable,
    onSelect: setSelectedOption
  })) : null, /*#__PURE__*/React.createElement(_disclaimer.default, null));
};
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('payone_secured_installment', (0, _i18n.__)('PAYONE Secured Installment', 'payone-woocommerce-3'), "".concat(_constants.PAYONE_ASSETS_URL, "/icon-rechnungskauf.png"), /*#__PURE__*/React.createElement(PaylaSecuredInstallment, null));

/***/ }),

/***/ "./client/blocks/payla/secured-invoice.jsx":
/*!*************************************************!*\
  !*** ./client/blocks/payla/secured-invoice.jsx ***!
  \*************************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _element = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
var _blocksCheckout = __webpack_require__(/*! @woocommerce/blocks-checkout */ "@woocommerce/blocks-checkout");
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _IconLabel = _interopRequireDefault(__webpack_require__(/*! ../../components/IconLabel */ "./client/components/IconLabel.jsx"));
var _AssetService = _interopRequireDefault(__webpack_require__(/*! ../../services/AssetService */ "./client/services/AssetService.js"));
var _disclaimer = _interopRequireDefault(__webpack_require__(/*! ./disclaimer */ "./client/blocks/payla/disclaimer.jsx"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
var PAYMENT_METHOD_NAME = 'payone_secured_invoice';
var PaylaSecuredInvoice = function PaylaSecuredInvoice(_ref) {
  var onPaymentSetup = _ref.eventRegistration.onPaymentSetup,
    responseTypes = _ref.emitResponse.responseTypes;
  var _wc$wcSettings$getSet = wc.wcSettings.getSetting('payone_data'),
    paylaConfig = _wc$wcSettings$getSet.paylaConfig;
  var _useState = (0, _element.useState)(''),
    _useState2 = _slicedToArray(_useState, 2),
    birthday = _useState2[0],
    setBirthday = _useState2[1];
  var _useState3 = (0, _element.useState)(''),
    _useState4 = _slicedToArray(_useState3, 2),
    vatId = _useState4[0],
    setVatId = _useState4[1];
  (0, _element.useEffect)(function () {
    return onPaymentSetup(function () {
      if (!birthday) {
        return {
          type: responseTypes.ERROR,
          message: (0, _i18n.__)('Please enter a valid birthday!', 'payone-woocommerce-3')
        };
      }
      if (!vatId) {
        return {
          type: responseTypes.ERROR,
          message: (0, _i18n.__)('Please enter a valid VAT-ID!', 'payone-woocommerce-3')
        };
      }
      return {
        type: responseTypes.SUCCESS,
        meta: {
          paymentMethodData: {
            payone_secured_invoice_birthday: birthday,
            payone_secured_invoice_vatid: vatId,
            payone_secured_invoice_token: paylaConfig.tokenSecuredIncoice
          }
        }
      };
    });
  }, [onPaymentSetup, birthday, vatId]);
  (0, _element.useEffect)(function () {
    _AssetService.default.loadJsScript(paylaConfig.jsUrl, function () {
      /* global paylaDcs */
      if (typeof paylaDcs !== 'undefined') {
        paylaDcs.init(paylaConfig.environmentKey, paylaConfig.tokenSecuredInvoice);
      }
    });
    _AssetService.default.loadCssStylesheet(paylaConfig.cssUrl);
  }, []);
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(_blocksCheckout.ValidatedTextInput, {
    type: "date",
    id: "payone_secured_invoice_birthday",
    className: "payone-validated-date-input is-active",
    label: (0, _i18n.__)('Birthday', 'payone-woocommerce-3'),
    onChange: function onChange(value) {
      return setBirthday(value);
    },
    value: birthday,
    required: true
  }), /*#__PURE__*/React.createElement(_blocksCheckout.ValidatedTextInput, {
    type: "text",
    id: "payone_secured_invoice_vatid",
    label: (0, _i18n.__)('VAT-ID', 'payone-woocommerce-3'),
    onChange: function onChange(value) {
      return setVatId(value);
    },
    value: vatId,
    required: true
  }), /*#__PURE__*/React.createElement(_disclaimer.default, null));
};
var label = (0, _i18n.__)('PAYONE Secured Invoice', 'payone-woocommerce-3');
var _default = exports["default"] = {
  name: PAYMENT_METHOD_NAME,
  label: /*#__PURE__*/React.createElement(_IconLabel.default, {
    text: label,
    icon: "".concat(_constants.PAYONE_ASSETS_URL, "/icon-rechnungskauf.png")
  }),
  ariaLabel: label,
  content: /*#__PURE__*/React.createElement(PaylaSecuredInvoice, null),
  edit: /*#__PURE__*/React.createElement(PaylaSecuredInvoice, null),
  canMakePayment: function canMakePayment() {
    return true;
  },
  paymentMethodId: PAYMENT_METHOD_NAME,
  supports: {
    showSavedCards: false,
    showSaveOption: false
  }
};

/***/ }),

/***/ "./client/blocks/paypal/index.jsx":
/*!****************************************!*\
  !*** ./client/blocks/paypal/index.jsx ***!
  \****************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('bs_payone_paypal', (0, _i18n.__)('PAYONE PayPal', 'payone-woocommerce-3'), "".concat(_constants.PAYONE_ASSETS_URL, "/icon-paypal.png"), null, {
  canMakePayment: function canMakePayment() {
    var _wc$wcSettings$getSet = wc.wcSettings.getSetting('payone_data'),
      paypalConfig = _wc$wcSettings$getSet.paypalConfig;
    return paypalConfig.isAvailable;
  }
});

/***/ }),

/***/ "./client/blocks/paypalv2/express.jsx":
/*!********************************************!*\
  !*** ./client/blocks/paypalv2/express.jsx ***!
  \********************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _element = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
var _AssetService = _interopRequireDefault(__webpack_require__(/*! ../../services/AssetService */ "./client/services/AssetService.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
var icon = "".concat(_constants.PAYONE_ASSETS_URL, "/").concat((0, _i18n.__)('checkout-paypal-en.png', 'payone-woocommerce-3'));
var PayPalV2Express = function PayPalV2Express() {
  var _wc$wcSettings$getSet = wc.wcSettings.getSetting('payone_data'),
    paypalExpressConfig = _wc$wcSettings$getSet.paypalExpressConfig;
  (0, _element.useEffect)(function () {
    _AssetService.default.loadJsScript(paypalExpressConfig.jsUrl, function () {
      /* global paypal */
      if (typeof paypal !== 'undefined') {
        paypal.Buttons({
          style: {
            layout: 'vertical',
            color: 'gold',
            shape: 'rect',
            label: 'paypal',
            height: 55
          },
          createOrder: function createOrder() {
            return fetch(paypalExpressConfig.callbackUrl, {
              method: 'post'
            }).then(function (res) {
              return res.text();
            }).then(function (orderID) {
              return orderID;
            });
          },
          onApprove: function onApprove() {
            window.location = paypalExpressConfig.redirectUrl;
          }
        }).render('#payone-paypalv2-express-button');
      }
    });
  }, []);
  return /*#__PURE__*/React.createElement("div", {
    id: "payone-paypalv2-express-button"
  });
};
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('payone_paypalv2_express', (0, _i18n.__)('PayPal v2 Express', 'payone-woocommerce-3'), icon, /*#__PURE__*/React.createElement(PayPalV2Express, null), {
  gatewayId: 'payone_paypalv2_express',
  canMakePayment: function canMakePayment() {
    return false;
    var _wc$wcSettings$getSet2 = wc.wcSettings.getSetting('payone_data'),
      paypalExpressConfig = _wc$wcSettings$getSet2.paypalExpressConfig;
    return paypalExpressConfig.isAvailable;
  }
});

/***/ }),

/***/ "./client/blocks/paypalv2/index.jsx":
/*!******************************************!*\
  !*** ./client/blocks/paypalv2/index.jsx ***!
  \******************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('payone_paypalv2', (0, _i18n.__)('PAYONE PayPal v2', 'payone-woocommerce-3'), "".concat(_constants.PAYONE_ASSETS_URL, "/icon-paypal.png"));

/***/ }),

/***/ "./client/blocks/pre-payment/index.jsx":
/*!*********************************************!*\
  !*** ./client/blocks/pre-payment/index.jsx ***!
  \*********************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('bs_payone_prepayment', (0, _i18n.__)('PAYONE Prepayment', 'payone-woocommerce-3'), "".concat(_constants.PAYONE_ASSETS_URL, "/icon-vorkasse.png"));

/***/ }),

/***/ "./client/blocks/przelewy24/index.jsx":
/*!********************************************!*\
  !*** ./client/blocks/przelewy24/index.jsx ***!
  \********************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('payone_przelewy24', (0, _i18n.__)('PAYONE Przelewy24', 'payone-woocommerce-3'), "".concat(_constants.PAYONE_ASSETS_URL, "/icon-przelewy24.png"));

/***/ }),

/***/ "./client/blocks/ratepay/direct-debit.jsx":
/*!************************************************!*\
  !*** ./client/blocks/ratepay/direct-debit.jsx ***!
  \************************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _iban = _interopRequireDefault(__webpack_require__(/*! iban */ "./node_modules/iban/iban.js"));
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _element = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
var _blocksCheckout = __webpack_require__(/*! @woocommerce/blocks-checkout */ "@woocommerce/blocks-checkout");
var _disclaimer = _interopRequireDefault(__webpack_require__(/*! ./disclaimer */ "./client/blocks/ratepay/disclaimer.jsx"));
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
var PAYMENT_METHOD_NAME = 'payone_ratepay_direct_debit';
var RatepayDirectDebit = function RatepayDirectDebit(_ref) {
  var onPaymentSetup = _ref.eventRegistration.onPaymentSetup,
    responseTypes = _ref.emitResponse.responseTypes;
  var _useState = (0, _element.useState)(''),
    _useState2 = _slicedToArray(_useState, 2),
    birthday = _useState2[0],
    setBirthday = _useState2[1];
  var _useState3 = (0, _element.useState)(''),
    _useState4 = _slicedToArray(_useState3, 2),
    iban = _useState4[0],
    setIban = _useState4[1];
  (0, _element.useEffect)(function () {
    // TODO: Fehlermeldungen von der API für den User lesbar zurückgeben

    var onSubmit = function onSubmit() {
      if (birthday && iban) {
        return {
          type: responseTypes.SUCCESS,
          meta: {
            paymentMethodData: {
              ratepay_direct_debit_birthday: birthday,
              ratepay_direct_debit_iban: iban
            }
          }
        };
      }
      return {
        type: responseTypes.ERROR,
        message: (0, _i18n.__)('Please enter a valid IBAN and birthday!', 'payone-woocommerce-3')
      };
    };
    var unsubscribeProcessing = onPaymentSetup(onSubmit);
    return function () {
      unsubscribeProcessing();
    };
  }, [onPaymentSetup, birthday, iban]);
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(_blocksCheckout.ValidatedTextInput, {
    type: "date",
    className: "payone-validated-date-input is-active",
    id: "ratepay_direct_debit_birthday",
    label: (0, _i18n.__)('Birthday', 'payone-woocommerce-3'),
    onChange: function onChange(value) {
      return setBirthday(value);
    },
    value: birthday,
    required: true
  }), /*#__PURE__*/React.createElement(_blocksCheckout.ValidatedTextInput, {
    id: "ratepay_direct_debit_iban",
    type: "text",
    label: (0, _i18n.__)('IBAN', 'payone-woocommerce-3'),
    onChange: function onChange(value) {
      return setIban(value);
    },
    customValidation: function customValidation(inputObject) {
      if (!_iban.default.isValid(inputObject.value)) {
        inputObject.setCustomValidity((0, _i18n.__)('Please enter a valid IBAN!', 'payone-woocommerce-3'));
        return false;
      }
      return true;
    },
    value: iban,
    required: true
  }), /*#__PURE__*/React.createElement("img", {
    src: "".concat(_constants.PAYONE_ASSETS_URL, "/icon-ratepay.svg"),
    alt: "Ratepay",
    width: "200"
  }), /*#__PURE__*/React.createElement(_disclaimer.default, null));
};
var Label = function Label(_ref2) {
  var components = _ref2.components;
  var PaymentMethodLabel = components.PaymentMethodLabel;
  return /*#__PURE__*/React.createElement(PaymentMethodLabel, {
    text: (0, _i18n.__)('Ratepay Direct Debit', 'payone-woocommerce-3')
  });
};
var _default = exports["default"] = {
  name: PAYMENT_METHOD_NAME,
  label: /*#__PURE__*/React.createElement(Label, null),
  ariaLabel: (0, _i18n.__)('Ratepay Direct Debit Zahlmethode', 'payone-woocommerce-3'),
  content: /*#__PURE__*/React.createElement(RatepayDirectDebit, null),
  edit: /*#__PURE__*/React.createElement(RatepayDirectDebit, null),
  canMakePayment: function canMakePayment() {
    return true;
  },
  paymentMethodId: PAYMENT_METHOD_NAME,
  supports: {
    showSavedCards: false,
    showSaveOption: false
  }
};

/***/ }),

/***/ "./client/blocks/ratepay/disclaimer.jsx":
/*!**********************************************!*\
  !*** ./client/blocks/ratepay/disclaimer.jsx ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = RatepayDisclaimer;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
function RatepayDisclaimer() {
  var data = (0, _i18n.__)("With clicking on Place Order you agree to \n         <a href=\"https://www.ratepay.com/legal-payment-terms/\" target=\"_blank\" rel=\"noopener\">\n            Ratepay Terms of Payment\n         </a> as well as to the performance of a \n         <a href=\"https://www.ratepay.com/legal-payment-dataprivacy/\" target=\"_blank\" rel=\"noopener\">\n            risk check by Ratepay\n         </a>.", 'payone-woocommerce-3');
  return /*#__PURE__*/React.createElement("p", {
    className: "wc-block-checkout__terms wp-block-woocommerce-checkout-terms-block",
    dangerouslySetInnerHTML: {
      __html: data
    }
  });
}

/***/ }),

/***/ "./client/blocks/ratepay/installments.jsx":
/*!************************************************!*\
  !*** ./client/blocks/ratepay/installments.jsx ***!
  \************************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _element = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
var _blocksCheckout = __webpack_require__(/*! @woocommerce/blocks-checkout */ "@woocommerce/blocks-checkout");
var _iban = _interopRequireDefault(__webpack_require__(/*! iban */ "./node_modules/iban/iban.js"));
var _disclaimer = _interopRequireDefault(__webpack_require__(/*! ./disclaimer */ "./client/blocks/ratepay/disclaimer.jsx"));
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _IconLabel = _interopRequireDefault(__webpack_require__(/*! ../../components/IconLabel */ "./client/components/IconLabel.jsx"));
var _installmentsCalculationLong = _interopRequireDefault(__webpack_require__(/*! ./installments/installments-calculation-long */ "./client/blocks/ratepay/installments/installments-calculation-long.jsx"));
var _installmentsCalculationShort = _interopRequireDefault(__webpack_require__(/*! ./installments/installments-calculation-short */ "./client/blocks/ratepay/installments/installments-calculation-short.jsx"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
var PAYMENT_METHOD_NAME = 'payone_ratepay_installments';

// TODO: Auf Typescript umbauen
/**
 * @typedef CalculationResult
 * @property {string} amount
 * @property {string} annual_percentage_rate
 * @property {string} interest_amount
 * @property {string} interest_rate
 * @property {string} last_rate
 * @property {string} monthly_debit_interest
 * @property {string} number_of_rates
 * @property {string} payment_firstday
 * @property {string} rate
 * @property {string} service_charge
 * @property {string} total_amount
 * @property {object} form
 * @property {string} form.amount
 * @property {string} form.installment_amount
 * @property {string} form.installment_number
 * @property {number} form.interest_rate - only field thats a number ?! :D
 * @property {string} form.last_installment_amount
 */

var RatepayInstallments = function RatepayInstallments(_ref) {
  var onPaymentSetup = _ref.eventRegistration.onPaymentSetup,
    responseTypes = _ref.emitResponse.responseTypes;
  var _wc$wcSettings$getSet = wc.wcSettings.getSetting('payone_data'),
    ratepayCalculationUrl = _wc$wcSettings$getSet.ratepayCalculationUrl,
    installmentMonthOptions = _wc$wcSettings$getSet.installmentMonthOptions;
  var _useState = (0, _element.useState)('0'),
    _useState2 = _slicedToArray(_useState, 2),
    selectedNumberOfMonths = _useState2[0],
    setSelectedNumberOfMonths = _useState2[1];
  var _useState3 = (0, _element.useState)(''),
    _useState4 = _slicedToArray(_useState3, 2),
    monthlyRate = _useState4[0],
    setMonthlyRate = _useState4[1];
  var _useState5 = (0, _element.useState)(''),
    _useState6 = _slicedToArray(_useState5, 2),
    birthday = _useState6[0],
    setBirthday = _useState6[1];
  var _useState7 = (0, _element.useState)(''),
    _useState8 = _slicedToArray(_useState7, 2),
    iban = _useState8[0],
    setIban = _useState8[1];
  var _useState9 = (0, _element.useState)(false),
    _useState10 = _slicedToArray(_useState9, 2),
    showDetailedCalculation = _useState10[0],
    setShowDetailedCalculation = _useState10[1];
  var _useState11 = (0, _element.useState)(false),
    _useState12 = _slicedToArray(_useState11, 2),
    showCalculation = _useState12[0],
    setShowCalculation = _useState12[1];
  var _useState13 = (0, _element.useState)(/** @type {CalculationResult|null} */null),
    _useState14 = _slicedToArray(_useState13, 2),
    calculationResult = _useState14[0],
    setCalculationResult = _useState14[1];
  var updateCalculation = function updateCalculation(calculationType) {
    var body = new FormData();
    body.append('calculation-type', calculationType);
    body.append('month', selectedNumberOfMonths);
    body.append('rate', monthlyRate);
    fetch(ratepayCalculationUrl, {
      method: 'POST',
      body: body
    }).then(function (response) {
      return response.json();
    }).then(function (/** @type CalculationResult */json) {
      if (json === -1) {
        // TODO: Fehlermeldung ausgeben?
        setCalculationResult(null);
        setShowCalculation(false);
      } else {
        setCalculationResult(json);
        setShowCalculation(true);
      }
    });
  };
  var hiddenFormFields = (0, _element.useMemo)(function () {
    if (!calculationResult || !calculationResult.form) {
      return {};
    }
    return {
      ratepay_installments_installment_amount: calculationResult.form.installment_amount,
      ratepay_installments_installment_number: calculationResult.form.installment_number,
      ratepay_installments_last_installment_amount: calculationResult.form.last_installment_amount,
      ratepay_installments_interest_rate: calculationResult.form.interest_rate.toString(),
      ratepay_installments_amount: calculationResult.form.amount
    };
  }, [calculationResult]);
  (0, _element.useEffect)(function () {
    if (calculationResult !== null) {
      setSelectedNumberOfMonths(calculationResult.number_of_rates);
    }
  }, [calculationResult]);
  (0, _element.useEffect)(function () {
    return onPaymentSetup(function () {
      if (!calculationResult || !hiddenFormFields) {
        return {
          type: responseTypes.ERROR,
          message: (0, _i18n.__)('Could not calculate your installment plan. Please try again with different values', 'payone-woocommerce-3')
        };
      }
      if (!birthday) {
        return {
          type: responseTypes.ERROR,
          message: (0, _i18n.__)('Please enter a valid birthday!', 'payone-woocommerce-3')
        };
      }
      return {
        type: responseTypes.SUCCESS,
        meta: {
          paymentMethodData: _objectSpread({
            ratepay_installments_months: selectedNumberOfMonths,
            ratepay_installments_rate: monthlyRate,
            ratepay_installments_birthday: birthday,
            ratepay_installments_iban: iban
          }, hiddenFormFields)
        }
      };
    });
  }, [onPaymentSetup, birthday, iban, selectedNumberOfMonths, monthlyRate, calculationResult, hiddenFormFields]);
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", {
    className: "wc-block-sort-select wc-block-components-sort-select"
  }, /*#__PURE__*/React.createElement(_blocksCheckout.Label, {
    label: (0, _i18n.__)('Number of monthly installments', 'payone-woocommerce-3'),
    screenReaderLabel: (0, _i18n.__)('Select bank group', 'payone-woocommerce-3'),
    wrapperElement: "label",
    wrapperProps: {
      className: 'wc-block-sort-select__label wc-block-components-sort-select__label',
      htmlFor: 'bankgrouptype'
    }
  }), /*#__PURE__*/React.createElement("br", null), /*#__PURE__*/React.createElement("select", {
    id: "ratepay_installments_months",
    className: "wc-block-sort-select__select wc-block-components-sort-select__select payoneSelect",
    onChange: function onChange(event) {
      setSelectedNumberOfMonths(event.target.value);
      updateCalculation('calculation-by-time');
    },
    value: selectedNumberOfMonths
  }, installmentMonthOptions.map(function (month) {
    return /*#__PURE__*/React.createElement("option", {
      key: month,
      value: month
    }, month === '0' ? (0, _i18n.__)('Choose', 'payone-woocommerce-3') : month);
  }))), /*#__PURE__*/React.createElement(_blocksCheckout.ValidatedTextInput, {
    type: "text",
    id: "ratepay_installments_rate",
    label: (0, _i18n.__)('Monthly rate', 'payone-woocommerce-3'),
    onChange: function onChange(value) {
      return setMonthlyRate(value);
    },
    value: monthlyRate,
    required: true
  }), /*#__PURE__*/React.createElement("div", {
    className: "wc-block-checkout__actions"
  }, /*#__PURE__*/React.createElement(_blocksCheckout.Button, {
    onClick: function onClick(event) {
      event.preventDefault();
      updateCalculation('calculation-by-rate');
    }
  }, (0, _i18n.__)('Calculate', 'payone-woocommerce-3'))), showCalculation ? /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("h3", null, (0, _i18n.__)('Personal calculation', 'payone-woocommerce-3')), showDetailedCalculation ? /*#__PURE__*/React.createElement(_installmentsCalculationLong.default, {
    calculationResult: calculationResult
  }) : /*#__PURE__*/React.createElement(_installmentsCalculationShort.default, {
    calculationResult: calculationResult
  }), /*#__PURE__*/React.createElement("small", null, /*#__PURE__*/React.createElement("button", {
    onClick: function onClick(event) {
      event.preventDefault();
      setShowDetailedCalculation(!showDetailedCalculation);
    }
  }, showDetailedCalculation ? (0, _i18n.__)('Hide details', 'payone-woocommerce-3') : (0, _i18n.__)('Show details', 'payone-woocommerce-3'))), /*#__PURE__*/React.createElement(_blocksCheckout.ValidatedTextInput, {
    type: "date",
    className: "payone-validated-date-input is-active",
    id: "ratepay_installments_birthday",
    label: (0, _i18n.__)('Birthday', 'payone-woocommerce-3'),
    onChange: function onChange(value) {
      return setBirthday(value);
    },
    value: birthday,
    required: true
  }), /*#__PURE__*/React.createElement(_blocksCheckout.ValidatedTextInput, {
    id: "ratepay_installments_iban",
    type: "text",
    label: (0, _i18n.__)('IBAN', 'payone-woocommerce-3'),
    onChange: function onChange(value) {
      return setIban(value);
    },
    customValidation: function customValidation(inputObject) {
      if (!_iban.default.isValid(inputObject.value)) {
        inputObject.setCustomValidity((0, _i18n.__)('Please enter a valid IBAN!', 'payone-woocommerce-3'));
        return false;
      }
      return true;
    },
    value: iban,
    required: true
  })) : null, /*#__PURE__*/React.createElement(_disclaimer.default, null));
};
var label = (0, _i18n.__)('PAYONE Ratepay Installments', 'payone-woocommerce-3');
var _default = exports["default"] = {
  name: PAYMENT_METHOD_NAME,
  label: /*#__PURE__*/React.createElement(_IconLabel.default, {
    text: label,
    icon: "".concat(_constants.PAYONE_ASSETS_URL, "/icon-ratepay.svg")
  }),
  ariaLabel: label,
  content: /*#__PURE__*/React.createElement(RatepayInstallments, null),
  edit: /*#__PURE__*/React.createElement(RatepayInstallments, null),
  canMakePayment: function canMakePayment() {
    return true;
  },
  paymentMethodId: PAYMENT_METHOD_NAME,
  supports: {
    showSavedCards: false,
    showSaveOption: false
  }
};

/***/ }),

/***/ "./client/blocks/ratepay/installments/installments-calculation-long.jsx":
/*!******************************************************************************!*\
  !*** ./client/blocks/ratepay/installments/installments-calculation-long.jsx ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = InstallmentsCalculationLong;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/**
 * @param {CalculationResult} calculationResult
 */
function InstallmentsCalculationLong(_ref) {
  var calculationResult = _ref.calculationResult;
  var currency = wc.wcSettings.CURRENCY.symbol;
  if (!calculationResult) return null;
  return /*#__PURE__*/React.createElement("table", {
    className: "table"
  }, /*#__PURE__*/React.createElement("tbody", null, /*#__PURE__*/React.createElement("tr", null, /*#__PURE__*/React.createElement("th", null, (0, _i18n.__)('Basket amount', 'payone-woocommerce-3')), /*#__PURE__*/React.createElement("td", null, calculationResult.amount, "\xA0", currency)), /*#__PURE__*/React.createElement("tr", null, /*#__PURE__*/React.createElement("th", null, (0, _i18n.__)('Servicecharge', 'payone-woocommerce-3')), /*#__PURE__*/React.createElement("td", null, calculationResult.service_charge, "\xA0", currency)), /*#__PURE__*/React.createElement("tr", null, /*#__PURE__*/React.createElement("th", null, (0, _i18n.__)('Annual percentage rate', 'payone-woocommerce-3')), /*#__PURE__*/React.createElement("td", null, calculationResult.annual_percentage_rate, "\xA0%")), /*#__PURE__*/React.createElement("tr", null, /*#__PURE__*/React.createElement("th", null, (0, _i18n.__)('Interest rate', 'payone-woocommerce-3')), /*#__PURE__*/React.createElement("td", null, calculationResult.interest_rate, "\xA0", currency)), /*#__PURE__*/React.createElement("tr", null, /*#__PURE__*/React.createElement("th", null, (0, _i18n.__)('Interest amount', 'payone-woocommerce-3')), /*#__PURE__*/React.createElement("td", null, calculationResult.interest_amount, "\xA0", currency)), /*#__PURE__*/React.createElement("tr", null), /*#__PURE__*/React.createElement("tr", null, /*#__PURE__*/React.createElement("th", null, calculationResult.number_of_rates - 1, "\xA0", (0, _i18n.__)('monthly installments', 'payone-woocommerce-3'), "\xA0\xE0"), /*#__PURE__*/React.createElement("td", null, calculationResult.rate, "\xA0", currency)), /*#__PURE__*/React.createElement("tr", null, /*#__PURE__*/React.createElement("th", null, (0, _i18n.__)('incl. one final installment', 'payone-woocommerce-3')), /*#__PURE__*/React.createElement("td", null, calculationResult.last_rate, "\xA0", currency)), /*#__PURE__*/React.createElement("tr", null, /*#__PURE__*/React.createElement("th", null, (0, _i18n.__)('Total amount', 'payone-woocommerce-3')), /*#__PURE__*/React.createElement("td", null, calculationResult.total_amount, "\xA0", currency))));
}

/***/ }),

/***/ "./client/blocks/ratepay/installments/installments-calculation-short.jsx":
/*!*******************************************************************************!*\
  !*** ./client/blocks/ratepay/installments/installments-calculation-short.jsx ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = InstallmentsCalculationShort;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/**
 * @param {CalculationResult} calculationResult
 */
function InstallmentsCalculationShort(_ref) {
  var calculationResult = _ref.calculationResult;
  var currency = wc.wcSettings.CURRENCY.symbol;
  if (!calculationResult) return null;
  return /*#__PURE__*/React.createElement("table", {
    className: "table"
  }, /*#__PURE__*/React.createElement("tbody", null, /*#__PURE__*/React.createElement("tr", null, /*#__PURE__*/React.createElement("th", null, calculationResult.number_of_rates, "\xA0", (0, _i18n.__)('monthly installments', 'payone-woocommerce-3')), /*#__PURE__*/React.createElement("td", null, calculationResult.rate, "\xA0", currency)), /*#__PURE__*/React.createElement("tr", null, /*#__PURE__*/React.createElement("th", null, (0, _i18n.__)('Total amount', 'payone-woocommerce-3')), /*#__PURE__*/React.createElement("td", null, calculationResult.total_amount, "\xA0", currency))));
}

/***/ }),

/***/ "./client/blocks/ratepay/open-invoice.jsx":
/*!************************************************!*\
  !*** ./client/blocks/ratepay/open-invoice.jsx ***!
  \************************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _element = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
var _blocksCheckout = __webpack_require__(/*! @woocommerce/blocks-checkout */ "@woocommerce/blocks-checkout");
var _disclaimer = _interopRequireDefault(__webpack_require__(/*! ./disclaimer */ "./client/blocks/ratepay/disclaimer.jsx"));
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _IconLabel = _interopRequireDefault(__webpack_require__(/*! ../../components/IconLabel */ "./client/components/IconLabel.jsx"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
var PAYMENT_METHOD_NAME = 'payone_ratepay_open_invoice';
var RatepayOpenInvoice = function RatepayOpenInvoice(_ref) {
  var onPaymentSetup = _ref.eventRegistration.onPaymentSetup,
    responseTypes = _ref.emitResponse.responseTypes;
  var _useState = (0, _element.useState)(''),
    _useState2 = _slicedToArray(_useState, 2),
    birthday = _useState2[0],
    setBirthday = _useState2[1];
  (0, _element.useEffect)(function () {
    // TODO: Server antwortet mit Fehlercode 1000 "Parameter faulty or missing"
    // TODO: Fehlermeldungen von der API für den User lesbar zurückgeben

    return onPaymentSetup(function () {
      if (birthday) {
        return {
          type: responseTypes.SUCCESS,
          meta: {
            paymentMethodData: {
              ratepay_open_invoice_birthday: birthday
            }
          }
        };
      }
      return {
        type: responseTypes.ERROR,
        message: (0, _i18n.__)('Please enter a valid birthday!', 'payone-woocommerce-3')
      };
    });
  }, [onPaymentSetup, birthday]);
  return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(_blocksCheckout.ValidatedTextInput, {
    type: "date",
    id: "ratepay_open_invoice_birthday",
    className: "payone-validated-date-input is-active",
    label: (0, _i18n.__)('Birthday', 'payone-woocommerce-3'),
    onChange: function onChange(value) {
      return setBirthday(value);
    },
    value: birthday,
    required: true
  }), /*#__PURE__*/React.createElement(_disclaimer.default, null));
};
var label = (0, _i18n.__)('Ratepay Open Invoice', 'payone-woocommerce-3');
var _default = exports["default"] = {
  name: PAYMENT_METHOD_NAME,
  label: /*#__PURE__*/React.createElement(_IconLabel.default, {
    text: label,
    icon: "".concat(_constants.PAYONE_ASSETS_URL, "/icon-ratepay.svg")
  }),
  ariaLabel: label,
  content: /*#__PURE__*/React.createElement(RatepayOpenInvoice, null),
  edit: /*#__PURE__*/React.createElement(RatepayOpenInvoice, null),
  canMakePayment: function canMakePayment() {
    return true;
  },
  paymentMethodId: PAYMENT_METHOD_NAME,
  supports: {
    showSavedCards: false,
    showSaveOption: false
  }
};

/***/ }),

/***/ "./client/blocks/safe-invoice/index.jsx":
/*!**********************************************!*\
  !*** ./client/blocks/safe-invoice/index.jsx ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('bs_payone_safeinvoice', (0, _i18n.__)('PAYONE Secure Invoice', 'payone-woocommerce-3'), "".concat(_constants.PAYONE_ASSETS_URL, "/icon-rechnungskauf.png"));

/***/ }),

/***/ "./client/blocks/sepa/index.jsx":
/*!**************************************!*\
  !*** ./client/blocks/sepa/index.jsx ***!
  \**************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _element = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
var _data = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _iban = _interopRequireDefault(__webpack_require__(/*! iban */ "./node_modules/iban/iban.js"));
var _blocksCheckout = __webpack_require__(/*! @woocommerce/blocks-checkout */ "@woocommerce/blocks-checkout");
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _regeneratorRuntime() { "use strict"; /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */ _regeneratorRuntime = function _regeneratorRuntime() { return e; }; var t, e = {}, r = Object.prototype, n = r.hasOwnProperty, o = Object.defineProperty || function (t, e, r) { t[e] = r.value; }, i = "function" == typeof Symbol ? Symbol : {}, a = i.iterator || "@@iterator", c = i.asyncIterator || "@@asyncIterator", u = i.toStringTag || "@@toStringTag"; function define(t, e, r) { return Object.defineProperty(t, e, { value: r, enumerable: !0, configurable: !0, writable: !0 }), t[e]; } try { define({}, ""); } catch (t) { define = function define(t, e, r) { return t[e] = r; }; } function wrap(t, e, r, n) { var i = e && e.prototype instanceof Generator ? e : Generator, a = Object.create(i.prototype), c = new Context(n || []); return o(a, "_invoke", { value: makeInvokeMethod(t, r, c) }), a; } function tryCatch(t, e, r) { try { return { type: "normal", arg: t.call(e, r) }; } catch (t) { return { type: "throw", arg: t }; } } e.wrap = wrap; var h = "suspendedStart", l = "suspendedYield", f = "executing", s = "completed", y = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} var p = {}; define(p, a, function () { return this; }); var d = Object.getPrototypeOf, v = d && d(d(values([]))); v && v !== r && n.call(v, a) && (p = v); var g = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(p); function defineIteratorMethods(t) { ["next", "throw", "return"].forEach(function (e) { define(t, e, function (t) { return this._invoke(e, t); }); }); } function AsyncIterator(t, e) { function invoke(r, o, i, a) { var c = tryCatch(t[r], t, o); if ("throw" !== c.type) { var u = c.arg, h = u.value; return h && "object" == _typeof(h) && n.call(h, "__await") ? e.resolve(h.__await).then(function (t) { invoke("next", t, i, a); }, function (t) { invoke("throw", t, i, a); }) : e.resolve(h).then(function (t) { u.value = t, i(u); }, function (t) { return invoke("throw", t, i, a); }); } a(c.arg); } var r; o(this, "_invoke", { value: function value(t, n) { function callInvokeWithMethodAndArg() { return new e(function (e, r) { invoke(t, n, e, r); }); } return r = r ? r.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg(); } }); } function makeInvokeMethod(e, r, n) { var o = h; return function (i, a) { if (o === f) throw Error("Generator is already running"); if (o === s) { if ("throw" === i) throw a; return { value: t, done: !0 }; } for (n.method = i, n.arg = a;;) { var c = n.delegate; if (c) { var u = maybeInvokeDelegate(c, n); if (u) { if (u === y) continue; return u; } } if ("next" === n.method) n.sent = n._sent = n.arg;else if ("throw" === n.method) { if (o === h) throw o = s, n.arg; n.dispatchException(n.arg); } else "return" === n.method && n.abrupt("return", n.arg); o = f; var p = tryCatch(e, r, n); if ("normal" === p.type) { if (o = n.done ? s : l, p.arg === y) continue; return { value: p.arg, done: n.done }; } "throw" === p.type && (o = s, n.method = "throw", n.arg = p.arg); } }; } function maybeInvokeDelegate(e, r) { var n = r.method, o = e.iterator[n]; if (o === t) return r.delegate = null, "throw" === n && e.iterator.return && (r.method = "return", r.arg = t, maybeInvokeDelegate(e, r), "throw" === r.method) || "return" !== n && (r.method = "throw", r.arg = new TypeError("The iterator does not provide a '" + n + "' method")), y; var i = tryCatch(o, e.iterator, r.arg); if ("throw" === i.type) return r.method = "throw", r.arg = i.arg, r.delegate = null, y; var a = i.arg; return a ? a.done ? (r[e.resultName] = a.value, r.next = e.nextLoc, "return" !== r.method && (r.method = "next", r.arg = t), r.delegate = null, y) : a : (r.method = "throw", r.arg = new TypeError("iterator result is not an object"), r.delegate = null, y); } function pushTryEntry(t) { var e = { tryLoc: t[0] }; 1 in t && (e.catchLoc = t[1]), 2 in t && (e.finallyLoc = t[2], e.afterLoc = t[3]), this.tryEntries.push(e); } function resetTryEntry(t) { var e = t.completion || {}; e.type = "normal", delete e.arg, t.completion = e; } function Context(t) { this.tryEntries = [{ tryLoc: "root" }], t.forEach(pushTryEntry, this), this.reset(!0); } function values(e) { if (e || "" === e) { var r = e[a]; if (r) return r.call(e); if ("function" == typeof e.next) return e; if (!isNaN(e.length)) { var o = -1, i = function next() { for (; ++o < e.length;) if (n.call(e, o)) return next.value = e[o], next.done = !1, next; return next.value = t, next.done = !0, next; }; return i.next = i; } } throw new TypeError(_typeof(e) + " is not iterable"); } return GeneratorFunction.prototype = GeneratorFunctionPrototype, o(g, "constructor", { value: GeneratorFunctionPrototype, configurable: !0 }), o(GeneratorFunctionPrototype, "constructor", { value: GeneratorFunction, configurable: !0 }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, u, "GeneratorFunction"), e.isGeneratorFunction = function (t) { var e = "function" == typeof t && t.constructor; return !!e && (e === GeneratorFunction || "GeneratorFunction" === (e.displayName || e.name)); }, e.mark = function (t) { return Object.setPrototypeOf ? Object.setPrototypeOf(t, GeneratorFunctionPrototype) : (t.__proto__ = GeneratorFunctionPrototype, define(t, u, "GeneratorFunction")), t.prototype = Object.create(g), t; }, e.awrap = function (t) { return { __await: t }; }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, c, function () { return this; }), e.AsyncIterator = AsyncIterator, e.async = function (t, r, n, o, i) { void 0 === i && (i = Promise); var a = new AsyncIterator(wrap(t, r, n, o), i); return e.isGeneratorFunction(r) ? a : a.next().then(function (t) { return t.done ? t.value : a.next(); }); }, defineIteratorMethods(g), define(g, u, "Generator"), define(g, a, function () { return this; }), define(g, "toString", function () { return "[object Generator]"; }), e.keys = function (t) { var e = Object(t), r = []; for (var n in e) r.push(n); return r.reverse(), function next() { for (; r.length;) { var t = r.pop(); if (t in e) return next.value = t, next.done = !1, next; } return next.done = !0, next; }; }, e.values = values, Context.prototype = { constructor: Context, reset: function reset(e) { if (this.prev = 0, this.next = 0, this.sent = this._sent = t, this.done = !1, this.delegate = null, this.method = "next", this.arg = t, this.tryEntries.forEach(resetTryEntry), !e) for (var r in this) "t" === r.charAt(0) && n.call(this, r) && !isNaN(+r.slice(1)) && (this[r] = t); }, stop: function stop() { this.done = !0; var t = this.tryEntries[0].completion; if ("throw" === t.type) throw t.arg; return this.rval; }, dispatchException: function dispatchException(e) { if (this.done) throw e; var r = this; function handle(n, o) { return a.type = "throw", a.arg = e, r.next = n, o && (r.method = "next", r.arg = t), !!o; } for (var o = this.tryEntries.length - 1; o >= 0; --o) { var i = this.tryEntries[o], a = i.completion; if ("root" === i.tryLoc) return handle("end"); if (i.tryLoc <= this.prev) { var c = n.call(i, "catchLoc"), u = n.call(i, "finallyLoc"); if (c && u) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } else if (c) { if (this.prev < i.catchLoc) return handle(i.catchLoc, !0); } else { if (!u) throw Error("try statement without catch or finally"); if (this.prev < i.finallyLoc) return handle(i.finallyLoc); } } } }, abrupt: function abrupt(t, e) { for (var r = this.tryEntries.length - 1; r >= 0; --r) { var o = this.tryEntries[r]; if (o.tryLoc <= this.prev && n.call(o, "finallyLoc") && this.prev < o.finallyLoc) { var i = o; break; } } i && ("break" === t || "continue" === t) && i.tryLoc <= e && e <= i.finallyLoc && (i = null); var a = i ? i.completion : {}; return a.type = t, a.arg = e, i ? (this.method = "next", this.next = i.finallyLoc, y) : this.complete(a); }, complete: function complete(t, e) { if ("throw" === t.type) throw t.arg; return "break" === t.type || "continue" === t.type ? this.next = t.arg : "return" === t.type ? (this.rval = this.arg = t.arg, this.method = "return", this.next = "end") : "normal" === t.type && e && (this.next = e), y; }, finish: function finish(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.finallyLoc === t) return this.complete(r.completion, r.afterLoc), resetTryEntry(r), y; } }, catch: function _catch(t) { for (var e = this.tryEntries.length - 1; e >= 0; --e) { var r = this.tryEntries[e]; if (r.tryLoc === t) { var n = r.completion; if ("throw" === n.type) { var o = n.arg; resetTryEntry(r); } return o; } } throw Error("illegal catch attempt"); }, delegateYield: function delegateYield(e, r, n) { return this.delegate = { iterator: values(e), resultName: r, nextLoc: n }, "next" === this.method && (this.arg = t), y; } }, e; }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
var PAYONE_TEST_IBAN = 'DE00123456782599100003';
var SepaDirectDebit = function SepaDirectDebit(_ref) {
  var _ref$eventRegistratio = _ref.eventRegistration,
    onPaymentSetup = _ref$eventRegistratio.onPaymentSetup,
    onCheckoutValidation = _ref$eventRegistratio.onCheckoutValidation,
    responseTypes = _ref.emitResponse.responseTypes,
    onSubmit = _ref.onSubmit;
  var _wc$wcSettings$getSet = wc.wcSettings.getSetting('payone_data'),
    sepaManageMandateUrl = _wc$wcSettings$getSet.sepaManageMandateUrl;
  var CART_STORE_KEY = wc.wcBlocksData.CART_STORE_KEY;
  var _useState = (0, _element.useState)(''),
    _useState2 = _slicedToArray(_useState, 2),
    iban = _useState2[0],
    setIban = _useState2[1];
  var _useState3 = (0, _element.useState)(false),
    _useState4 = _slicedToArray(_useState3, 2),
    mandateCheckSucceeded = _useState4[0],
    setMandateCheckSucceeded = _useState4[1];
  var _useState5 = (0, _element.useState)(false),
    _useState6 = _slicedToArray(_useState5, 2),
    showConfirmationCheck = _useState6[0],
    setShowConfirmationCheck = _useState6[1];
  var _useState7 = (0, _element.useState)(true),
    _useState8 = _slicedToArray(_useState7, 2),
    confirmationChecked = _useState8[0],
    setConfirmationChecked = _useState8[1];
  var _useState9 = (0, _element.useState)(''),
    _useState10 = _slicedToArray(_useState9, 2),
    mandateConfirmationText = _useState10[0],
    setMandateConfirmationText = _useState10[1];
  var _useState11 = (0, _element.useState)(''),
    _useState12 = _slicedToArray(_useState11, 2),
    mandateReference = _useState12[0],
    setMandateReference = _useState12[1];
  var _useState13 = (0, _element.useState)(''),
    _useState14 = _slicedToArray(_useState13, 2),
    errorMessage = _useState14[0],
    setErrorMessage = _useState14[1];
  (0, _element.useEffect)(function () {
    return onCheckoutValidation(/*#__PURE__*/_asyncToGenerator(/*#__PURE__*/_regeneratorRuntime().mark(function _callee() {
      var store, _store$getCartData, billingAddress, body;
      return _regeneratorRuntime().wrap(function _callee$(_context) {
        while (1) switch (_context.prev = _context.next) {
          case 0:
            if (!mandateCheckSucceeded) {
              _context.next = 2;
              break;
            }
            return _context.abrupt("return", true);
          case 2:
            if (!errorMessage) {
              _context.next = 5;
              break;
            }
            setErrorMessage('');
            return _context.abrupt("return", false);
          case 5:
            store = (0, _data.select)(CART_STORE_KEY);
            _store$getCartData = store.getCartData(), billingAddress = _store$getCartData.billingAddress;
            body = new FormData();
            body.append('currency', wc.wcSettings.CURRENCY.code);
            body.append('lastname', billingAddress.last_name);
            body.append('country', billingAddress.country);
            body.append('city', billingAddress.city);
            body.append('confirmation_check', confirmationChecked ? 1 : 0);
            body.append('mandate_identification', mandateReference);
            body.append('iban', iban);
            if (!showConfirmationCheck) {
              body.set('confirmation_check', -1);
            }
            setMandateCheckSucceeded(false);
            fetch(sepaManageMandateUrl, {
              method: 'POST',
              body: body
            }).then(function (response) {
              return response.json();
            }).then(function (json) {
              if (json.status === 'error') {
                setErrorMessage(json.message);
              } else if (json.status === 'active') {
                setMandateReference(json.reference);
                setMandateCheckSucceeded(true);
                setMandateConfirmationText('');
                setShowConfirmationCheck(false);

                // Re-Trigger payment processing
                onSubmit();
              } else if (json.status === 'pending') {
                setMandateReference(json.reference);

                // If has re-submitted after confirmation
                if (showConfirmationCheck && confirmationChecked) {
                  setMandateCheckSucceeded(true);
                  setMandateConfirmationText('');
                  setShowConfirmationCheck(false);
                } else {
                  setConfirmationChecked(false);
                  setMandateConfirmationText(json.text);
                  setShowConfirmationCheck(true);
                }
              }
            });

            // Prevent automatical submit
            return _context.abrupt("return", false);
          case 19:
          case "end":
            return _context.stop();
        }
      }, _callee);
    })));
  }, [onCheckoutValidation, mandateCheckSucceeded, confirmationChecked, mandateReference, iban, errorMessage]);
  (0, _element.useEffect)(function () {
    return onPaymentSetup(function () {
      if (errorMessage) {
        return {
          type: responseTypes.ERROR,
          message: errorMessage
        };
      }
      if (showConfirmationCheck && !confirmationChecked) {
        return {
          type: responseTypes.ERROR,
          message: (0, _i18n.__)('Du musst diese Checkbox ankreuzen, um fortfahren zu können\n', 'payone-woocommerce-3')
        };
      }
      if (mandateReference) {
        return {
          type: responseTypes.SUCCESS,
          meta: {
            paymentMethodData: {
              direct_debit_iban: iban,
              direct_debit_confirmation_check: confirmationChecked ? 1 : 0,
              direct_debit_reference: mandateReference
            }
          }
        };
      }
      return {
        type: responseTypes.ERROR
      };
    });
  }, [onPaymentSetup, mandateCheckSucceeded, errorMessage, iban, mandateReference, confirmationChecked, showConfirmationCheck]);
  return /*#__PURE__*/React.createElement(React.Fragment, null, errorMessage ? /*#__PURE__*/React.createElement("strong", {
    style: {
      color: 'red'
    }
  }, errorMessage) : null, /*#__PURE__*/React.createElement(_blocksCheckout.ValidatedTextInput, {
    id: "direct_debit_iban_field",
    type: "text",
    label: (0, _i18n.__)('IBAN', 'payone-woocommerce-3'),
    onChange: function onChange(value) {
      return setIban(value);
    },
    customValidation: function customValidation(inputObject) {
      if (inputObject.value === PAYONE_TEST_IBAN) return true;
      if (!_iban.default.isValid(inputObject.value)) {
        inputObject.setCustomValidity((0, _i18n.__)('Please enter a valid IBAN!', 'payone-woocommerce-3'));
        return false;
      }
      return true;
    },
    value: iban,
    required: true
  }), showConfirmationCheck ? /*#__PURE__*/React.createElement(React.Fragment, null, mandateConfirmationText ? /*#__PURE__*/React.createElement("p", {
    dangerouslySetInnerHTML: {
      __html: mandateConfirmationText
    },
    style: {
      marginTop: '1rem'
    }
  }) : null, /*#__PURE__*/React.createElement("div", {
    style: {
      display: 'flex',
      alignItems: 'center'
    }
  }, /*#__PURE__*/React.createElement("input", {
    type: "checkbox",
    id: "direct_debit_confirmation_check",
    onChange: function onChange(event) {
      setConfirmationChecked(event.target.checked);
    },
    checked: confirmationChecked
  }), /*#__PURE__*/React.createElement("label", {
    htmlFor: "direct_debit_confirmation_check"
  }, (0, _i18n.__)('Ich erteile das SEPA-Lastschriftmandat', 'payone-woocommerce-3')))) : null);
};
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('bs_payone_sepa', (0, _i18n.__)('PAYONE Direct Debit', 'payone-woocommerce-3'), "".concat(_constants.PAYONE_ASSETS_URL, "/icon-lastschrift.png"), /*#__PURE__*/React.createElement(SepaDirectDebit, null));

/***/ }),

/***/ "./client/blocks/sofort/index.jsx":
/*!****************************************!*\
  !*** ./client/blocks/sofort/index.jsx ***!
  \****************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
var _i18n = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
var _constants = __webpack_require__(/*! ../../constants */ "./client/constants.js");
var _getPaymentMethodConfig = _interopRequireDefault(__webpack_require__(/*! ../../services/getPaymentMethodConfig */ "./client/services/getPaymentMethodConfig.js"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
var _default = exports["default"] = (0, _getPaymentMethodConfig.default)('bs_payone_sofort', (0, _i18n.__)('PAYONE Sofort', 'payone-woocommerce-3'), "".concat(_constants.PAYONE_ASSETS_URL, "/icon-klarna.png"));

/***/ }),

/***/ "./client/components/IconLabel.jsx":
/*!*****************************************!*\
  !*** ./client/components/IconLabel.jsx ***!
  \*****************************************/
/***/ (function(__unused_webpack_module, exports) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = Label;
function Label(_ref) {
  var text = _ref.text,
    icon = _ref.icon;
  return /*#__PURE__*/React.createElement("span", {
    style: {
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'space-between',
      paddingRight: '16px',
      width: '100%',
      gap: '16px'
    }
  }, /*#__PURE__*/React.createElement("strong", null, text), /*#__PURE__*/React.createElement("img", {
    src: icon,
    alt: text
  }));
}

/***/ }),

/***/ "./client/constants.js":
/*!*****************************!*\
  !*** ./client/constants.js ***!
  \*****************************/
/***/ (function(__unused_webpack_module, exports) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.PAYONE_PLUGIN_URL = exports.PAYONE_ASSETS_URL = void 0;
var PAYONE_PLUGIN_URL = exports.PAYONE_PLUGIN_URL = '/wp-content/plugins/payone-woocommerce-3';
var PAYONE_ASSETS_URL = exports.PAYONE_ASSETS_URL = '/wp-content/plugins/payone-woocommerce-3/assets';

/***/ }),

/***/ "./client/services/AssetService.js":
/*!*****************************************!*\
  !*** ./client/services/AssetService.js ***!
  \*****************************************/
/***/ (function(__unused_webpack_module, exports) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
var AssetService = exports["default"] = /*#__PURE__*/function () {
  function AssetService() {
    _classCallCheck(this, AssetService);
  }
  return _createClass(AssetService, null, [{
    key: "loadJsScript",
    value: function loadJsScript(url, callback) {
      var script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = url;
      document.body.appendChild(script);
      script.addEventListener('load', callback);
    }
  }, {
    key: "loadCssStylesheet",
    value: function loadCssStylesheet(url, callback) {
      var link = document.createElement('link');
      link.type = 'text/css';
      link.rel = 'stylesheet';
      link.href = url;
      document.body.appendChild(link);
      link.addEventListener('load', callback);
    }
  }]);
}();

/***/ }),

/***/ "./client/services/getPaymentMethodConfig.js":
/*!***************************************************!*\
  !*** ./client/services/getPaymentMethodConfig.js ***!
  \***************************************************/
/***/ (function(__unused_webpack_module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = getPaymentMethodConfig;
var _IconLabel = _interopRequireDefault(__webpack_require__(/*! ../components/IconLabel */ "./client/components/IconLabel.jsx"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function getPaymentMethodConfig(name, label, icon) {
  var content = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : null;
  var additionalOptions = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : {};
  var component = content || /*#__PURE__*/React.createElement(React.Fragment, null);
  return _objectSpread({
    name: name,
    label: /*#__PURE__*/React.createElement(_IconLabel.default, {
      text: label,
      icon: icon
    }),
    ariaLabel: label,
    content: component,
    edit: component,
    canMakePayment: function canMakePayment() {
      return true;
    },
    paymentMethodId: name,
    supports: {
      showSavedCards: false,
      showSaveOption: false
    }
  }, additionalOptions);
}

/***/ }),

/***/ "./node_modules/iban/iban.js":
/*!***********************************!*\
  !*** ./node_modules/iban/iban.js ***!
  \***********************************/
/***/ (function(module, exports) {

var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (root, factory) {
    if (true) {
        // AMD. Register as an anonymous module.
        !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory),
		__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
		(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
    } else {}
}(this, function(exports){

    // Array.prototype.map polyfill
    // code from https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/Array/map
    if (!Array.prototype.map){
        Array.prototype.map = function(fun /*, thisArg */){
            "use strict";

            if (this === void 0 || this === null)
                throw new TypeError();

            var t = Object(this);
            var len = t.length >>> 0;
            if (typeof fun !== "function")
                throw new TypeError();

            var res = new Array(len);
            var thisArg = arguments.length >= 2 ? arguments[1] : void 0;
            for (var i = 0; i < len; i++)
            {
                // NOTE: Absolute correctness would demand Object.defineProperty
                //       be used.  But this method is fairly new, and failure is
                //       possible only if Object.prototype or Array.prototype
                //       has a property |i| (very unlikely), so use a less-correct
                //       but more portable alternative.
                if (i in t)
                    res[i] = fun.call(thisArg, t[i], i, t);
            }

            return res;
        };
    }

    var A = 'A'.charCodeAt(0),
        Z = 'Z'.charCodeAt(0);

    /**
     * Prepare an IBAN for mod 97 computation by moving the first 4 chars to the end and transforming the letters to
     * numbers (A = 10, B = 11, ..., Z = 35), as specified in ISO13616.
     *
     * @param {string} iban the IBAN
     * @returns {string} the prepared IBAN
     */
    function iso13616Prepare(iban) {
        iban = iban.toUpperCase();
        iban = iban.substr(4) + iban.substr(0,4);

        return iban.split('').map(function(n){
            var code = n.charCodeAt(0);
            if (code >= A && code <= Z){
                // A = 10, B = 11, ... Z = 35
                return code - A + 10;
            } else {
                return n;
            }
        }).join('');
    }

    /**
     * Calculates the MOD 97 10 of the passed IBAN as specified in ISO7064.
     *
     * @param iban
     * @returns {number}
     */
    function iso7064Mod97_10(iban) {
        var remainder = iban,
            block;

        while (remainder.length > 2){
            block = remainder.slice(0, 9);
            remainder = parseInt(block, 10) % 97 + remainder.slice(block.length);
        }

        return parseInt(remainder, 10) % 97;
    }

    /**
     * Parse the BBAN structure used to configure each IBAN Specification and returns a matching regular expression.
     * A structure is composed of blocks of 3 characters (one letter and 2 digits). Each block represents
     * a logical group in the typical representation of the BBAN. For each group, the letter indicates which characters
     * are allowed in this group and the following 2-digits number tells the length of the group.
     *
     * @param {string} structure the structure to parse
     * @returns {RegExp}
     */
    function parseStructure(structure){
        // split in blocks of 3 chars
        var regex = structure.match(/(.{3})/g).map(function(block){

            // parse each structure block (1-char + 2-digits)
            var format,
                pattern = block.slice(0, 1),
                repeats = parseInt(block.slice(1), 10);

            switch (pattern){
                case "A": format = "0-9A-Za-z"; break;
                case "B": format = "0-9A-Z"; break;
                case "C": format = "A-Za-z"; break;
                case "F": format = "0-9"; break;
                case "L": format = "a-z"; break;
                case "U": format = "A-Z"; break;
                case "W": format = "0-9a-z"; break;
            }

            return '([' + format + ']{' + repeats + '})';
        });

        return new RegExp('^' + regex.join('') + '$');
    }

    /**
     *
     * @param iban
     * @returns {string}
     */
    function electronicFormat(iban){
        return iban.replace(NON_ALPHANUM, '').toUpperCase();
    }


    /**
     * Create a new Specification for a valid IBAN number.
     *
     * @param countryCode the code of the country
     * @param length the length of the IBAN
     * @param structure the structure of the underlying BBAN (for validation and formatting)
     * @param example an example valid IBAN
     * @constructor
     */
    function Specification(countryCode, length, structure, example){

        this.countryCode = countryCode;
        this.length = length;
        this.structure = structure;
        this.example = example;
    }

    /**
     * Lazy-loaded regex (parse the structure and construct the regular expression the first time we need it for validation)
     */
    Specification.prototype._regex = function(){
        return this._cachedRegex || (this._cachedRegex = parseStructure(this.structure))
    };

    /**
     * Check if the passed iban is valid according to this specification.
     *
     * @param {String} iban the iban to validate
     * @returns {boolean} true if valid, false otherwise
     */
    Specification.prototype.isValid = function(iban){
        return this.length == iban.length
            && this.countryCode === iban.slice(0,2)
            && this._regex().test(iban.slice(4))
            && iso7064Mod97_10(iso13616Prepare(iban)) == 1;
    };

    /**
     * Convert the passed IBAN to a country-specific BBAN.
     *
     * @param iban the IBAN to convert
     * @param separator the separator to use between BBAN blocks
     * @returns {string} the BBAN
     */
    Specification.prototype.toBBAN = function(iban, separator) {
        return this._regex().exec(iban.slice(4)).slice(1).join(separator);
    };

    /**
     * Convert the passed BBAN to an IBAN for this country specification.
     * Please note that <i>"generation of the IBAN shall be the exclusive responsibility of the bank/branch servicing the account"</i>.
     * This method implements the preferred algorithm described in http://en.wikipedia.org/wiki/International_Bank_Account_Number#Generating_IBAN_check_digits
     *
     * @param bban the BBAN to convert to IBAN
     * @returns {string} the IBAN
     */
    Specification.prototype.fromBBAN = function(bban) {
        if (!this.isValidBBAN(bban)){
            throw new Error('Invalid BBAN');
        }

        var remainder = iso7064Mod97_10(iso13616Prepare(this.countryCode + '00' + bban)),
            checkDigit = ('0' + (98 - remainder)).slice(-2);

        return this.countryCode + checkDigit + bban;
    };

    /**
     * Check of the passed BBAN is valid.
     * This function only checks the format of the BBAN (length and matching the letetr/number specs) but does not
     * verify the check digit.
     *
     * @param bban the BBAN to validate
     * @returns {boolean} true if the passed bban is a valid BBAN according to this specification, false otherwise
     */
    Specification.prototype.isValidBBAN = function(bban) {
        return this.length - 4 == bban.length
            && this._regex().test(bban);
    };

    var countries = {};

    function addSpecification(IBAN){
        countries[IBAN.countryCode] = IBAN;
    }

    addSpecification(new Specification("AD", 24, "F04F04A12",          "AD1200012030200359100100"));
    addSpecification(new Specification("AE", 23, "F03F16",             "AE070331234567890123456"));
    addSpecification(new Specification("AL", 28, "F08A16",             "AL47212110090000000235698741"));
    addSpecification(new Specification("AT", 20, "F05F11",             "AT611904300234573201"));
    addSpecification(new Specification("AZ", 28, "U04A20",             "AZ21NABZ00000000137010001944"));
    addSpecification(new Specification("BA", 20, "F03F03F08F02",       "BA391290079401028494"));
    addSpecification(new Specification("BE", 16, "F03F07F02",          "BE68539007547034"));
    addSpecification(new Specification("BG", 22, "U04F04F02A08",       "BG80BNBG96611020345678"));
    addSpecification(new Specification("BH", 22, "U04A14",             "BH67BMAG00001299123456"));
    addSpecification(new Specification("BR", 29, "F08F05F10U01A01",    "BR9700360305000010009795493P1"));
    addSpecification(new Specification("BY", 28, "A04F04A16",          "BY13NBRB3600900000002Z00AB00"));
    addSpecification(new Specification("CH", 21, "F05A12",             "CH9300762011623852957"));
    addSpecification(new Specification("CR", 22, "F04F14",             "CR72012300000171549015"));
    addSpecification(new Specification("CY", 28, "F03F05A16",          "CY17002001280000001200527600"));
    addSpecification(new Specification("CZ", 24, "F04F06F10",          "CZ6508000000192000145399"));
    addSpecification(new Specification("DE", 22, "F08F10",             "DE89370400440532013000"));
    addSpecification(new Specification("DK", 18, "F04F09F01",          "DK5000400440116243"));
    addSpecification(new Specification("DO", 28, "U04F20",             "DO28BAGR00000001212453611324"));
    addSpecification(new Specification("EE", 20, "F02F02F11F01",       "EE382200221020145685"));
    addSpecification(new Specification("EG", 29, "F04F04F17",          "EG800002000156789012345180002"));
    addSpecification(new Specification("ES", 24, "F04F04F01F01F10",    "ES9121000418450200051332"));
    addSpecification(new Specification("FI", 18, "F06F07F01",          "FI2112345600000785"));
    addSpecification(new Specification("FO", 18, "F04F09F01",          "FO6264600001631634"));
    addSpecification(new Specification("FR", 27, "F05F05A11F02",       "FR1420041010050500013M02606"));
    addSpecification(new Specification("GB", 22, "U04F06F08",          "GB29NWBK60161331926819"));
    addSpecification(new Specification("GE", 22, "U02F16",             "GE29NB0000000101904917"));
    addSpecification(new Specification("GI", 23, "U04A15",             "GI75NWBK000000007099453"));
    addSpecification(new Specification("GL", 18, "F04F09F01",          "GL8964710001000206"));
    addSpecification(new Specification("GR", 27, "F03F04A16",          "GR1601101250000000012300695"));
    addSpecification(new Specification("GT", 28, "A04A20",             "GT82TRAJ01020000001210029690"));
    addSpecification(new Specification("HR", 21, "F07F10",             "HR1210010051863000160"));
    addSpecification(new Specification("HU", 28, "F03F04F01F15F01",    "HU42117730161111101800000000"));
    addSpecification(new Specification("IE", 22, "U04F06F08",          "IE29AIBK93115212345678"));
    addSpecification(new Specification("IL", 23, "F03F03F13",          "IL620108000000099999999"));
    addSpecification(new Specification("IS", 26, "F04F02F06F10",       "IS140159260076545510730339"));
    addSpecification(new Specification("IT", 27, "U01F05F05A12",       "IT60X0542811101000000123456"));
    addSpecification(new Specification("IQ", 23, "U04F03A12",          "IQ98NBIQ850123456789012"));
    addSpecification(new Specification("JO", 30, "A04F22",             "JO15AAAA1234567890123456789012"));
    addSpecification(new Specification("KW", 30, "U04A22",             "KW81CBKU0000000000001234560101"));
    addSpecification(new Specification("KZ", 20, "F03A13",             "KZ86125KZT5004100100"));
    addSpecification(new Specification("LB", 28, "F04A20",             "LB62099900000001001901229114"));
    addSpecification(new Specification("LC", 32, "U04F24",             "LC07HEMM000100010012001200013015"));
    addSpecification(new Specification("LI", 21, "F05A12",             "LI21088100002324013AA"));
    addSpecification(new Specification("LT", 20, "F05F11",             "LT121000011101001000"));
    addSpecification(new Specification("LU", 20, "F03A13",             "LU280019400644750000"));
    addSpecification(new Specification("LV", 21, "U04A13",             "LV80BANK0000435195001"));
    addSpecification(new Specification("MC", 27, "F05F05A11F02",       "MC5811222000010123456789030"));
    addSpecification(new Specification("MD", 24, "U02A18",             "MD24AG000225100013104168"));
    addSpecification(new Specification("ME", 22, "F03F13F02",          "ME25505000012345678951"));
    addSpecification(new Specification("MK", 19, "F03A10F02",          "MK07250120000058984"));
    addSpecification(new Specification("MR", 27, "F05F05F11F02",       "MR1300020001010000123456753"));
    addSpecification(new Specification("MT", 31, "U04F05A18",          "MT84MALT011000012345MTLCAST001S"));
    addSpecification(new Specification("MU", 30, "U04F02F02F12F03U03", "MU17BOMM0101101030300200000MUR"));
    addSpecification(new Specification("NL", 18, "U04F10",             "NL91ABNA0417164300"));
    addSpecification(new Specification("NO", 15, "F04F06F01",          "NO9386011117947"));
    addSpecification(new Specification("PK", 24, "U04A16",             "PK36SCBL0000001123456702"));
    addSpecification(new Specification("PL", 28, "F08F16",             "PL61109010140000071219812874"));
    addSpecification(new Specification("PS", 29, "U04A21",             "PS92PALS000000000400123456702"));
    addSpecification(new Specification("PT", 25, "F04F04F11F02",       "PT50000201231234567890154"));
    addSpecification(new Specification("QA", 29, "U04A21",             "QA30AAAA123456789012345678901"));
    addSpecification(new Specification("RO", 24, "U04A16",             "RO49AAAA1B31007593840000"));
    addSpecification(new Specification("RS", 22, "F03F13F02",          "RS35260005601001611379"));
    addSpecification(new Specification("SA", 24, "F02A18",             "SA0380000000608010167519"));
    addSpecification(new Specification("SC", 31, "U04F04F16U03",       "SC18SSCB11010000000000001497USD"));
    addSpecification(new Specification("SE", 24, "F03F16F01",          "SE4550000000058398257466"));
    addSpecification(new Specification("SI", 19, "F05F08F02",          "SI56263300012039086"));
    addSpecification(new Specification("SK", 24, "F04F06F10",          "SK3112000000198742637541"));
    addSpecification(new Specification("SM", 27, "U01F05F05A12",       "SM86U0322509800000000270100"));
    addSpecification(new Specification("ST", 25, "F08F11F02",          "ST68000100010051845310112"));
    addSpecification(new Specification("SV", 28, "U04F20",             "SV62CENR00000000000000700025"));
    addSpecification(new Specification("TL", 23, "F03F14F02",          "TL380080012345678910157"));
    addSpecification(new Specification("TN", 24, "F02F03F13F02",       "TN5910006035183598478831"));
    addSpecification(new Specification("TR", 26, "F05F01A16",          "TR330006100519786457841326"));
    addSpecification(new Specification("UA", 29, "F25",                "UA511234567890123456789012345"));
    addSpecification(new Specification("VA", 22, "F18",                "VA59001123000012345678"));
    addSpecification(new Specification("VG", 24, "U04F16",             "VG96VPVG0000012345678901"));
    addSpecification(new Specification("XK", 20, "F04F10F02",          "XK051212012345678906"));


    // The following countries are not included in the official IBAN registry but use the IBAN specification

    // Angola
    addSpecification(new Specification("AO", 25, "F21",                "AO69123456789012345678901"));
    // Burkina
    addSpecification(new Specification("BF", 27, "F23",                "BF2312345678901234567890123"));
    // Burundi
    addSpecification(new Specification("BI", 16, "F12",                "BI41123456789012"));
    // Benin
    addSpecification(new Specification("BJ", 28, "F24",                "BJ39123456789012345678901234"));
    // Ivory
    addSpecification(new Specification("CI", 28, "U02F22",             "CI70CI1234567890123456789012"));
    // Cameron
    addSpecification(new Specification("CM", 27, "F23",                "CM9012345678901234567890123"));
    // Cape Verde
    addSpecification(new Specification("CV", 25, "F21",                "CV30123456789012345678901"));
    // Algeria
    addSpecification(new Specification("DZ", 24, "F20",                "DZ8612345678901234567890"));
    // Iran
    addSpecification(new Specification("IR", 26, "F22",                "IR861234568790123456789012"));
    // Madagascar
    addSpecification(new Specification("MG", 27, "F23",                "MG1812345678901234567890123"));
    // Mali
    addSpecification(new Specification("ML", 28, "U01F23",             "ML15A12345678901234567890123"));
    // Mozambique
    addSpecification(new Specification("MZ", 25, "F21",                "MZ25123456789012345678901"));
    // Senegal
    addSpecification(new Specification("SN", 28, "U01F23",             "SN52A12345678901234567890123"));

    // The following are regional and administrative French Republic subdivision IBAN specification (same structure as FR, only country code vary)
    addSpecification(new Specification("GF", 27, "F05F05A11F02",       "GF121234512345123456789AB13"));
    addSpecification(new Specification("GP", 27, "F05F05A11F02",       "GP791234512345123456789AB13"));
    addSpecification(new Specification("MQ", 27, "F05F05A11F02",       "MQ221234512345123456789AB13"));
    addSpecification(new Specification("RE", 27, "F05F05A11F02",       "RE131234512345123456789AB13"));
    addSpecification(new Specification("PF", 27, "F05F05A11F02",       "PF281234512345123456789AB13"));
    addSpecification(new Specification("TF", 27, "F05F05A11F02",       "TF891234512345123456789AB13"));
    addSpecification(new Specification("YT", 27, "F05F05A11F02",       "YT021234512345123456789AB13"));
    addSpecification(new Specification("NC", 27, "F05F05A11F02",       "NC551234512345123456789AB13"));
    addSpecification(new Specification("BL", 27, "F05F05A11F02",       "BL391234512345123456789AB13"));
    addSpecification(new Specification("MF", 27, "F05F05A11F02",       "MF551234512345123456789AB13"));
    addSpecification(new Specification("PM", 27, "F05F05A11F02",       "PM071234512345123456789AB13"));
    addSpecification(new Specification("WF", 27, "F05F05A11F02",       "WF621234512345123456789AB13"));

    var NON_ALPHANUM = /[^a-zA-Z0-9]/g,
        EVERY_FOUR_CHARS =/(.{4})(?!$)/g;

    /**
     * Utility function to check if a variable is a String.
     *
     * @param v
     * @returns {boolean} true if the passed variable is a String, false otherwise.
     */
    function isString(v){
        return (typeof v == 'string' || v instanceof String);
    }

    /**
     * Check if an IBAN is valid.
     *
     * @param {String} iban the IBAN to validate.
     * @returns {boolean} true if the passed IBAN is valid, false otherwise
     */
    exports.isValid = function(iban){
        if (!isString(iban)){
            return false;
        }
        iban = electronicFormat(iban);
        var countryStructure = countries[iban.slice(0,2)];
        return !!countryStructure && countryStructure.isValid(iban);
    };

    /**
     * Convert an IBAN to a BBAN.
     *
     * @param iban
     * @param {String} [separator] the separator to use between the blocks of the BBAN, defaults to ' '
     * @returns {string|*}
     */
    exports.toBBAN = function(iban, separator){
        if (typeof separator == 'undefined'){
            separator = ' ';
        }
        iban = electronicFormat(iban);
        var countryStructure = countries[iban.slice(0,2)];
        if (!countryStructure) {
            throw new Error('No country with code ' + iban.slice(0,2));
        }
        return countryStructure.toBBAN(iban, separator);
    };

    /**
     * Convert the passed BBAN to an IBAN for this country specification.
     * Please note that <i>"generation of the IBAN shall be the exclusive responsibility of the bank/branch servicing the account"</i>.
     * This method implements the preferred algorithm described in http://en.wikipedia.org/wiki/International_Bank_Account_Number#Generating_IBAN_check_digits
     *
     * @param countryCode the country of the BBAN
     * @param bban the BBAN to convert to IBAN
     * @returns {string} the IBAN
     */
    exports.fromBBAN = function(countryCode, bban){
        var countryStructure = countries[countryCode];
        if (!countryStructure) {
            throw new Error('No country with code ' + countryCode);
        }
        return countryStructure.fromBBAN(electronicFormat(bban));
    };

    /**
     * Check the validity of the passed BBAN.
     *
     * @param countryCode the country of the BBAN
     * @param bban the BBAN to check the validity of
     */
    exports.isValidBBAN = function(countryCode, bban){
        if (!isString(bban)){
            return false;
        }
        var countryStructure = countries[countryCode];
        return countryStructure && countryStructure.isValidBBAN(electronicFormat(bban));
    };

    /**
     *
     * @param iban
     * @param separator
     * @returns {string}
     */
    exports.printFormat = function(iban, separator){
        if (typeof separator == 'undefined'){
            separator = ' ';
        }
        return electronicFormat(iban).replace(EVERY_FOUR_CHARS, "$1" + separator);
    };

    exports.electronicFormat = electronicFormat;
    /**
     * An object containing all the known IBAN specifications.
     */
    exports.countries = countries;

}));


/***/ }),

/***/ "@woocommerce/blocks-checkout":
/*!****************************************!*\
  !*** external ["wc","blocksCheckout"] ***!
  \****************************************/
/***/ (function(module) {

"use strict";
module.exports = window["wc"]["blocksCheckout"];

/***/ }),

/***/ "@woocommerce/blocks-registry":
/*!******************************************!*\
  !*** external ["wc","wcBlocksRegistry"] ***!
  \******************************************/
/***/ (function(module) {

"use strict";
module.exports = window["wc"]["wcBlocksRegistry"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ (function(module) {

"use strict";
module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ (function(module) {

"use strict";
module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ (function(module) {

"use strict";
module.exports = window["wp"]["i18n"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
!function() {
"use strict";
/*!********************************!*\
  !*** ./client/blocks/index.js ***!
  \********************************/


var _blocksRegistry = __webpack_require__(/*! @woocommerce/blocks-registry */ "@woocommerce/blocks-registry");
var _alipay = _interopRequireDefault(__webpack_require__(/*! ./alipay */ "./client/blocks/alipay/index.jsx"));
var _bancontact = _interopRequireDefault(__webpack_require__(/*! ./bancontact */ "./client/blocks/bancontact/index.jsx"));
var _directDebit = _interopRequireDefault(__webpack_require__(/*! ./ratepay/direct-debit */ "./client/blocks/ratepay/direct-debit.jsx"));
var _openInvoice = _interopRequireDefault(__webpack_require__(/*! ./ratepay/open-invoice */ "./client/blocks/ratepay/open-invoice.jsx"));
var _installments = _interopRequireDefault(__webpack_require__(/*! ./ratepay/installments */ "./client/blocks/ratepay/installments.jsx"));
var _creditCard = _interopRequireDefault(__webpack_require__(/*! ./credit-card */ "./client/blocks/credit-card/index.jsx"));
var _przelewy = _interopRequireDefault(__webpack_require__(/*! ./przelewy24 */ "./client/blocks/przelewy24/index.jsx"));
var _prePayment = _interopRequireDefault(__webpack_require__(/*! ./pre-payment */ "./client/blocks/pre-payment/index.jsx"));
var _safeInvoice = _interopRequireDefault(__webpack_require__(/*! ./safe-invoice */ "./client/blocks/safe-invoice/index.jsx"));
var _invoice = _interopRequireDefault(__webpack_require__(/*! ./invoice */ "./client/blocks/invoice/index.jsx"));
var _eps = _interopRequireDefault(__webpack_require__(/*! ./eps */ "./client/blocks/eps/index.jsx"));
var _ideal = _interopRequireDefault(__webpack_require__(/*! ./ideal */ "./client/blocks/ideal/index.jsx"));
var _sofort = _interopRequireDefault(__webpack_require__(/*! ./sofort */ "./client/blocks/sofort/index.jsx"));
var _paypal = _interopRequireDefault(__webpack_require__(/*! ./paypal */ "./client/blocks/paypal/index.jsx"));
var _paypalv = _interopRequireDefault(__webpack_require__(/*! ./paypalv2 */ "./client/blocks/paypalv2/index.jsx"));
var _express = _interopRequireDefault(__webpack_require__(/*! ./paypalv2/express */ "./client/blocks/paypalv2/express.jsx"));
var _invoice2 = _interopRequireDefault(__webpack_require__(/*! ./klarna/invoice */ "./client/blocks/klarna/invoice.jsx"));
var _installments2 = _interopRequireDefault(__webpack_require__(/*! ./klarna/installments */ "./client/blocks/klarna/installments.jsx"));
var _sofort2 = _interopRequireDefault(__webpack_require__(/*! ./klarna/sofort */ "./client/blocks/klarna/sofort.jsx"));
var _securedInvoice = _interopRequireDefault(__webpack_require__(/*! ./payla/secured-invoice */ "./client/blocks/payla/secured-invoice.jsx"));
var _securedInstallment = _interopRequireDefault(__webpack_require__(/*! ./payla/secured-installment */ "./client/blocks/payla/secured-installment.jsx"));
var _securedDirectDebit = _interopRequireDefault(__webpack_require__(/*! ./payla/secured-direct-debit */ "./client/blocks/payla/secured-direct-debit.jsx"));
var _sepa = _interopRequireDefault(__webpack_require__(/*! ./sepa */ "./client/blocks/sepa/index.jsx"));
function _interopRequireDefault(e) { return e && e.__esModule ? e : { default: e }; }
/* eslint-disable @typescript-eslint/no-unused-vars */

(0, _blocksRegistry.registerPaymentMethod)(_alipay.default);
(0, _blocksRegistry.registerPaymentMethod)(_bancontact.default);
(0, _blocksRegistry.registerPaymentMethod)(_przelewy.default);
(0, _blocksRegistry.registerPaymentMethod)(_directDebit.default);
(0, _blocksRegistry.registerPaymentMethod)(_openInvoice.default);
(0, _blocksRegistry.registerPaymentMethod)(_installments.default);
(0, _blocksRegistry.registerPaymentMethod)(_creditCard.default);
(0, _blocksRegistry.registerPaymentMethod)(_prePayment.default);
(0, _blocksRegistry.registerPaymentMethod)(_safeInvoice.default);
(0, _blocksRegistry.registerPaymentMethod)(_invoice.default);
(0, _blocksRegistry.registerPaymentMethod)(_eps.default);
(0, _blocksRegistry.registerPaymentMethod)(_ideal.default);
(0, _blocksRegistry.registerPaymentMethod)(_sofort.default);
(0, _blocksRegistry.registerPaymentMethod)(_paypal.default);
(0, _blocksRegistry.registerPaymentMethod)(_paypalv.default);
(0, _blocksRegistry.registerExpressPaymentMethod)(_express.default);
(0, _blocksRegistry.registerPaymentMethod)(_invoice2.default);
(0, _blocksRegistry.registerPaymentMethod)(_installments2.default);
(0, _blocksRegistry.registerPaymentMethod)(_sofort2.default);
(0, _blocksRegistry.registerPaymentMethod)(_securedInvoice.default);
(0, _blocksRegistry.registerPaymentMethod)(_securedInstallment.default);
(0, _blocksRegistry.registerPaymentMethod)(_securedDirectDebit.default);
(0, _blocksRegistry.registerPaymentMethod)(_sepa.default);
}();
/******/ })()
;
//# sourceMappingURL=blocks.js.map