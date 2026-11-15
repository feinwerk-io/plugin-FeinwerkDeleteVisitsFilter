(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory(require("CoreHome"), require("vue"), require("CorePluginsAdmin"), require("SegmentEditor"));
	else if(typeof define === 'function' && define.amd)
		define(["CoreHome", , "CorePluginsAdmin", "SegmentEditor"], factory);
	else if(typeof exports === 'object')
		exports["FeinwerkDeleteVisitsFilter"] = factory(require("CoreHome"), require("vue"), require("CorePluginsAdmin"), require("SegmentEditor"));
	else
		root["FeinwerkDeleteVisitsFilter"] = factory(root["CoreHome"], root["Vue"], root["CorePluginsAdmin"], root["SegmentEditor"]);
})((typeof self !== 'undefined' ? self : this), function(__WEBPACK_EXTERNAL_MODULE__19dc__, __WEBPACK_EXTERNAL_MODULE__8bbf__, __WEBPACK_EXTERNAL_MODULE_a5a2__, __WEBPACK_EXTERNAL_MODULE_f06f__) {
return /******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "plugins/FeinwerkDeleteVisitsFilter/vue/dist/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "fae3");
/******/ })
/************************************************************************/
/******/ ({

/***/ "19dc":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE__19dc__;

/***/ }),

/***/ "8bbf":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE__8bbf__;

/***/ }),

/***/ "a5a2":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE_a5a2__;

/***/ }),

/***/ "f06f":
/***/ (function(module, exports) {

module.exports = __WEBPACK_EXTERNAL_MODULE_f06f__;

/***/ }),

/***/ "fae3":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "Manage", function() { return /* reexport */ Manage; });

// CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/setPublicPath.js
// This file is imported into lib/wc client bundles.

if (typeof window !== 'undefined') {
  var currentScript = window.document.currentScript
  if (false) { var getCurrentScript; }

  var src = currentScript && currentScript.src.match(/(.+\/)[^/]+\.js(\?.*)?$/)
  if (src) {
    __webpack_require__.p = src[1] // eslint-disable-line
  }
}

// Indicate to webpack that this file can be concatenated
/* harmony default export */ var setPublicPath = (null);

// EXTERNAL MODULE: external {"commonjs":"vue","commonjs2":"vue","root":"Vue"}
var external_commonjs_vue_commonjs2_vue_root_Vue_ = __webpack_require__("8bbf");

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Manage/Manage.vue?vue&type=template&id=8660e062

const _hoisted_1 = {
  class: "fwdvf-manage"
};
const _hoisted_2 = {
  class: "fwdvf-tabs"
};
const _hoisted_3 = ["onClick"];
function render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_Alert = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Alert");
  const _component_ContentBlock = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ContentBlock");
  const _component_RunningJobs = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("RunningJobs");
  const _component_DeleteTab = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("DeleteTab");
  const _component_HistoryTab = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("HistoryTab");
  const _component_FeedbackBar = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("FeedbackBar");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", _hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_ContentBlock, {
    "content-title": _ctx.translate('FeinwerkDeleteVisitsFilter_PageTitle')
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_PageDescription')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Alert, {
      severity: "warning"
    }, {
      default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("strong", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_Warning')) + ":", 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_WarningText')), 1)]),
      _: 1
    })]),
    _: 1
  }, 8, ["content-title"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_RunningJobs, {
    "site-names": _ctx.siteNames,
    "background-budget": _ctx.backgroundBudget
  }, null, 8, ["site-names", "background-budget"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", _hoisted_2, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.tabs, item => {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("button", {
      key: item.id,
      type: "button",
      class: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeClass"])(["fwdvf-tab", {
        'fwdvf-tab-active': _ctx.tab === item.id
      }]),
      onClick: $event => _ctx.setTab(item.id)
    }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(item.label), 11, _hoisted_3);
  }), 128))]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_DeleteTab, {
    "initial-site-id": _ctx.initialSiteId,
    "site-names": _ctx.siteNames,
    "confirm-threshold": _ctx.confirmThreshold,
    "default-batch-size": _ctx.defaultBatchSize,
    template: _ctx.template
  }, null, 8, ["initial-site-id", "site-names", "confirm-threshold", "default-batch-size", "template"]), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vShow"], _ctx.tab === 'delete']]), _ctx.tab === 'history' ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_HistoryTab, {
    key: 0,
    "site-names": _ctx.siteNames,
    onUseTemplate: _cache[0] || (_cache[0] = $event => _ctx.useTemplate($event))
  }, null, 8, ["site-names"])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_FeedbackBar, {
    topic: "delete_visits",
    question: _ctx.translate('FeinwerkDeleteVisitsFilter_FeedbackQuestion_delete_visits'),
    feedback: _ctx.feedback
  }, null, 8, ["question", "feedback"])]);
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Manage/Manage.vue?vue&type=template&id=8660e062

// EXTERNAL MODULE: external "CoreHome"
var external_CoreHome_ = __webpack_require__("19dc");

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/DeleteTab.vue?vue&type=template&id=7096274e

const DeleteTabvue_type_template_id_7096274e_hoisted_1 = {
  class: "fwdvf-delete"
};
const DeleteTabvue_type_template_id_7096274e_hoisted_2 = {
  class: "fwdvf-advanced"
};
const DeleteTabvue_type_template_id_7096274e_hoisted_3 = {
  key: 0,
  class: "fwdvf-error"
};
function DeleteTabvue_type_template_id_7096274e_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_SitePicker = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SitePicker");
  const _component_DateRangeSelect = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("DateRangeSelect");
  const _component_SegmentInput = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SegmentInput");
  const _component_Field = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Field");
  const _component_SaveButton = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SaveButton");
  const _component_Alert = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Alert");
  const _component_PreviewResult = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("PreviewResult");
  const _component_ContentBlock = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ContentBlock");
  const _component_ConfirmDeleteDialog = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ConfirmDeleteDialog");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", DeleteTabvue_type_template_id_7096274e_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_ContentBlock, {
    "content-title": _ctx.translate('FeinwerkDeleteVisitsFilter_DeleteTitle')
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SitePicker, {
      modelValue: _ctx.sites,
      "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => _ctx.sites = $event),
      "site-names": _ctx.siteNames
    }, null, 8, ["modelValue", "site-names"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_DateRangeSelect, {
      modelValue: _ctx.dates,
      "onUpdate:modelValue": _cache[1] || (_cache[1] = $event => _ctx.dates = $event)
    }, null, 8, ["modelValue"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SegmentInput, {
      modelValue: _ctx.segment,
      "onUpdate:modelValue": _cache[2] || (_cache[2] = $event => _ctx.segment = $event),
      "id-site": _ctx.segmentSiteId,
      "all-sites": _ctx.sites.mode === 'all'
    }, null, 8, ["modelValue", "id-site", "all-sites"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("details", DeleteTabvue_type_template_id_7096274e_hoisted_2, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("summary", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_Advanced')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      modelValue: _ctx.batchSize,
      "onUpdate:modelValue": _cache[3] || (_cache[3] = $event => _ctx.batchSize = $event),
      uicontrol: "text",
      name: "fwdvf-batch-size",
      title: _ctx.translate('FeinwerkDeleteVisitsFilter_BatchSize'),
      "inline-help": _ctx.batchSizeHelp
    }, null, 8, ["modelValue", "title", "inline-help"])]), _ctx.attempted && _ctx.inputError ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", DeleteTabvue_type_template_id_7096274e_hoisted_3, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.inputError), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SaveButton, {
      class: "fwdvf-check",
      value: _ctx.translate('FeinwerkDeleteVisitsFilter_CheckMatches'),
      saving: _ctx.previewing,
      onConfirm: _cache[4] || (_cache[4] = $event => _ctx.checkMatches())
    }, null, 8, ["value", "saving"]), _ctx.error ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_Alert, {
      key: 1,
      severity: "danger"
    }, {
      default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.error), 1)]),
      _: 1
    })) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), _ctx.currentPreview ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], {
      key: 2
    }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_PreviewResult, {
      preview: _ctx.currentPreview,
      "date-from": _ctx.dates.from,
      "date-to": _ctx.dates.to,
      segment: _ctx.segment.trim()
    }, null, 8, ["preview", "date-from", "date-to", "segment"]), _ctx.currentPreview.total === 0 ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_Alert, {
      key: 0,
      severity: "info"
    }, {
      default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_NoVisitsFound')), 1)]),
      _: 1
    })) : (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_SaveButton, {
      key: 1,
      class: "fwdvf-delete-button",
      value: _ctx.translate('FeinwerkDeleteVisitsFilter_DeleteMatches', _ctx.formatCount(_ctx.currentPreview.total)),
      saving: _ctx.creating,
      onConfirm: _cache[5] || (_cache[5] = $event => _ctx.confirmOpen = true)
    }, null, 8, ["value", "saving"]))], 64)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)]),
    _: 1
  }, 8, ["content-title"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_ConfirmDeleteDialog, {
    modelValue: _ctx.confirmOpen,
    "onUpdate:modelValue": _cache[6] || (_cache[6] = $event => _ctx.confirmOpen = $event),
    total: _ctx.currentPreview ? _ctx.currentPreview.total : 0,
    threshold: _ctx.confirmThreshold,
    "sites-text": _ctx.selectedSitesText,
    "date-range-text": _ctx.dateRangeText(_ctx.dates.from, _ctx.dates.to),
    segment: _ctx.segment.trim(),
    onConfirm: _cache[7] || (_cache[7] = $event => _ctx.startDeletion())
  }, null, 8, ["modelValue", "total", "threshold", "sites-text", "date-range-text", "segment"])]);
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/DeleteTab.vue?vue&type=template&id=7096274e

// EXTERNAL MODULE: external "CorePluginsAdmin"
var external_CorePluginsAdmin_ = __webpack_require__("a5a2");

// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/lib/errors.ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
// Fixed English texts with which Matomo's AjaxHelper rejects a request that never got an API
// answer (network error, gateway timeout, rate limit). They are not worth showing to the user.
const TRANSPORT_ERROR_MESSAGES = ['Something went wrong', 'Request was possibly aborted', 'Rate Limit was exceed'];
function isTransportErrorMessage(message) {
  return TRANSPORT_ERROR_MESSAGES.indexOf(message) !== -1;
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/api.ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */


// abortable: false – a hash change (tab switch, visitor profile) must not cancel a request: Matomo
// never settles aborted requests. createErrorNotification: false – the page shows errors itself.
const OPTIONS = {
  abortable: false,
  createErrorNotification: false
};
// All parameters go into the POST body. For `segment` this also stops AjaxHelper from adding the
// page's (empty) segment to the URL, and long segments are not limited by the URL length.
function call(method, postParams = {}) {
  return external_CoreHome_["AjaxHelper"].post({
    method: `FeinwerkDeleteVisitsFilter.${method}`
  }, postParams, OPTIONS);
}
// Matomo's API HTML-escapes exception messages; callers escape again where needed (Vue
// interpolation, NotificationsStore), so decode here to avoid double escaping. AjaxHelper's fixed
// English transport failure texts are replaced by a translated one.
function errorMessage(error) {
  if (error instanceof Error && error.message && !isTransportErrorMessage(error.message)) {
    return external_CoreHome_["Matomo"].helper.htmlDecode(error.message);
  }
  return Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_RequestFailed');
}
function previewDeletion(idSites, segment, dateFrom, dateTo) {
  return call('previewDeletion', {
    idSites,
    segment,
    dateFrom,
    dateTo,
    sampleSize: 20
  });
}
function createJob(idSites, segment, dateFrom, dateTo, batchSize) {
  return call('createJob', {
    idSites,
    segment,
    dateFrom,
    dateTo,
    batchSize,
    source: 'ui'
  });
}
function processJobs() {
  return call('processJobs');
}
function getJob(idJob) {
  return call('getJob', {
    idJob
  });
}
function getJobs(offset, limit, status, source) {
  return call('getJobs', {
    offset,
    limit,
    status,
    source
  });
}
function cancelJob(idJob) {
  return call('cancelJob', {
    idJob
  });
}
function retryJob(idJob) {
  return call('retryJob', {
    idJob
  });
}
function storedSegments(idSite) {
  return external_CoreHome_["AjaxHelper"].post({
    method: 'SegmentEditor.getAll'
  }, {
    idSite
  }, OPTIONS);
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/lib/datetime.ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
const pad = n => n < 10 ? `0${n}` : `${n}`;
/** Milliseconds of a "Y-m-d H:i:s" UTC value from the database, NaN when it cannot be read. */
function parseUtc(datetime) {
  return Date.parse(`${datetime.trim().replace(' ', 'T')}Z`);
}
/**
 * "2026-11-11 22:57" in the browser's time zone: the date form of the page's date ranges,
 * without seconds, so the column stays narrow.
 */
function formatLocalDateTime(ms) {
  const d = new Date(ms);
  const date = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
  return `${date} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/lib/sites.ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
function allSiteIds(names) {
  return Object.keys(names).map(Number).sort((a, b) => a - b);
}
function selectedSiteIds(choice, allIds) {
  if (choice.mode === 'all') {
    return allIds.slice();
  }
  if (choice.mode === 'multiple') {
    return choice.multiple.slice();
  }
  return choice.single === null ? [] : [choice.single];
}
/** Value of the API parameter idSites. */
function siteParam(choice, allIds) {
  return choice.mode === 'all' ? 'all' : selectedSiteIds(choice, allIds).join(',');
}
function addSites(current, add) {
  const result = current.slice();
  add.forEach(id => {
    if (result.indexOf(id) === -1) {
      result.push(id);
    }
  });
  return result;
}
/** Websites whose name contains the pattern (case-insensitive), in id order. */
function matchingSiteIds(names, pattern) {
  const needle = pattern.trim().toLowerCase();
  if (needle === '') {
    return [];
  }
  return allSiteIds(names).filter(id => names[id].toLowerCase().indexOf(needle) !== -1);
}
function coversAll(ids, allIds) {
  const sorted = ids.slice().sort((a, b) => a - b);
  return allIds.length > 1 && sorted.length === allIds.length && sorted.every((id, index) => id === allIds[index]);
}
/** Restores the website choice of a job (template from the history). */
function choiceFromIds(ids, allIds) {
  if (coversAll(ids, allIds)) {
    return {
      mode: 'all',
      single: allIds[0],
      multiple: []
    };
  }
  if (ids.length === 1) {
    return {
      mode: 'single',
      single: ids[0],
      multiple: []
    };
  }
  return {
    mode: 'multiple',
    single: ids.length ? ids[0] : null,
    multiple: ids.slice()
  };
}
function summarizeSites(ids, names, allIds, max = 3) {
  return {
    names: ids.slice(0, max).map(id => {
      var _names$id;
      return (_names$id = names[id]) !== null && _names$id !== void 0 ? _names$id : `#${id}`;
    }),
    more: Math.max(0, ids.length - max),
    all: coversAll(ids, allIds)
  };
}
/** The translation key of the hint after "Add all matching": singular for exactly one website. */
function addedHintKey(count) {
  return count === 1 ? 'FeinwerkDeleteVisitsFilter_WebsiteAdded' : 'FeinwerkDeleteVisitsFilter_WebsitesAdded';
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/labels.ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */



const STATUS_KEYS = {
  queued: 'FeinwerkDeleteVisitsFilter_StatusQueued',
  running: 'FeinwerkDeleteVisitsFilter_StatusRunning',
  held: 'FeinwerkDeleteVisitsFilter_StatusHeld',
  done: 'FeinwerkDeleteVisitsFilter_StatusDone',
  cancelled: 'FeinwerkDeleteVisitsFilter_StatusCancelled',
  failed: 'FeinwerkDeleteVisitsFilter_StatusFailed'
};
const SOURCE_KEYS = {
  ui: 'FeinwerkDeleteVisitsFilter_SourceUi',
  api: 'FeinwerkDeleteVisitsFilter_SourceApi',
  console: 'FeinwerkDeleteVisitsFilter_SourceConsole',
  rule: 'FeinwerkDeleteVisitsFilter_SourceRule'
};
function statusLabel(status) {
  return STATUS_KEYS[status] ? Object(external_CoreHome_["translate"])(STATUS_KEYS[status]) : status;
}
function sourceLabel(source) {
  return SOURCE_KEYS[source] ? Object(external_CoreHome_["translate"])(SOURCE_KEYS[source]) : source;
}
function formatCount(value) {
  return external_CoreHome_["NumberFormatter"].formatNumber(value, 0);
}
function formatRate(perSecond) {
  return external_CoreHome_["NumberFormatter"].formatNumber(perSecond, perSecond < 10 ? 1 : 0);
}
function sitesText(ids, names, allIds) {
  const summary = summarizeSites(ids, names, allIds);
  if (summary.all) {
    return Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_SiteModeAll');
  }
  const text = summary.names.join(', ');
  return summary.more > 0 ? `${text} ${Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_MoreWebsites', summary.more)}` : text;
}
function dateRangeText(from, to) {
  return from === to ? from : Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_DateRangeValue', from, to);
}
/** "Y-m-d H:i:s" in UTC → local date and time of the browser ("2026-11-11 22:57"). */
function formatUtc(datetime) {
  if (!datetime) {
    return '-';
  }
  const ms = parseUtc(datetime);
  return Number.isNaN(ms) ? datetime : formatLocalDateTime(ms);
}
function messageText(message) {
  return Object(external_CoreHome_["translate"])(message.key, ...message.params.map(param => `${param}`));
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/lib/dateRange.ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
const DATE_PRESETS = ['today', 'yesterday', 'last7', 'last30', 'lastMonth', 'thisYear', 'lastYear', 'all', 'custom'];
/** Matches the server's earliest allowed day (Input/DateRange). */
const EARLIEST_DAY = '1992-01-01';
function dateRange_pad(value) {
  return value < 10 ? `0${value}` : `${value}`;
}
function toIsoDay(date) {
  return `${date.getFullYear()}-${dateRange_pad(date.getMonth() + 1)}-${dateRange_pad(date.getDate())}`;
}
function shift(date, days) {
  return new Date(date.getFullYear(), date.getMonth(), date.getDate() + days);
}
/**
 * Concrete calendar days for a preset, based on the browser's "today".
 * Returns null for 'custom' – the caller keeps its own range.
 */
function rangeForPreset(preset, today) {
  const year = today.getFullYear();
  switch (preset) {
    case 'today':
      return {
        from: toIsoDay(today),
        to: toIsoDay(today)
      };
    case 'yesterday':
      {
        const day = toIsoDay(shift(today, -1));
        return {
          from: day,
          to: day
        };
      }
    case 'last7':
      return {
        from: toIsoDay(shift(today, -6)),
        to: toIsoDay(today)
      };
    case 'last30':
      return {
        from: toIsoDay(shift(today, -29)),
        to: toIsoDay(today)
      };
    case 'lastMonth':
      return {
        from: toIsoDay(new Date(year, today.getMonth() - 1, 1)),
        to: toIsoDay(new Date(year, today.getMonth(), 0))
      };
    case 'thisYear':
      return {
        from: `${year}-01-01`,
        to: toIsoDay(today)
      };
    case 'lastYear':
      return {
        from: `${year - 1}-01-01`,
        to: `${year - 1}-12-31`
      };
    case 'all':
      return {
        from: EARLIEST_DAY,
        to: toIsoDay(today)
      };
    default:
      return null;
  }
}
function isValidDay(value) {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
    return false;
  }
  const [year, month, day] = value.split('-').map(Number);
  const date = new Date(year, month - 1, day);
  return date.getFullYear() === year && date.getMonth() === month - 1 && date.getDate() === day;
}
/** First problem of a range as a translation key (+ value for the placeholder), or null. */
function rangeProblem(range) {
  const invalid = [range.from, range.to].filter(day => !isValidDay(day));
  if (invalid.length > 0) {
    return {
      key: 'FeinwerkDeleteVisitsFilter_ErrorInvalidDate',
      value: invalid[0]
    };
  }
  if (range.from > range.to) {
    return {
      key: 'FeinwerkDeleteVisitsFilter_ErrorDateOrder',
      value: ''
    };
  }
  return null;
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/lib/jobs.ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
const ACTIVE_STATUSES = ['queued', 'running', 'held'];
/** Same limits as the server (Input/BatchSize). */
const BATCH_SIZE_MIN = 100;
const BATCH_SIZE_MAX = 10000;
function isActive(status) {
  return ACTIVE_STATUSES.indexOf(status) !== -1;
}
/** PHP encodes an empty associative array as [] – the UI always wants an object. */
function normalizeRows(rows) {
  if (!rows || Array.isArray(rows) || typeof rows !== 'object') {
    return {};
  }
  return rows;
}
function jobs_parseUtc(datetime) {
  return Date.parse(`${datetime.replace(' ', 'T')}Z`);
}
/** Seconds between two "Y-m-d H:i:s" UTC timestamps (until now while the job runs). */
function durationSeconds(startedAt, finishedAt, nowMs) {
  if (!startedAt) {
    return null;
  }
  const start = jobs_parseUtc(startedAt);
  const end = finishedAt ? jobs_parseUtc(finishedAt) : nowMs;
  if (Number.isNaN(start) || Number.isNaN(end)) {
    return null;
  }
  return Math.max(0, Math.round((end - start) / 1000));
}
/**
 * Pause before the next processJobs call: none while this page holds the runner lock, 5 s while
 * another process (console, hourly task, other tab) works on the queue.
 */
function nextPollDelayMs(busy) {
  return busy ? 5000 : 0;
}
/** Empty means "use the default". */
function isValidBatchSize(value) {
  const trimmed = value.trim();
  if (trimmed === '') {
    return true;
  }
  if (!/^\d+$/.test(trimmed)) {
    return false;
  }
  const size = parseInt(trimmed, 10);
  return size >= BATCH_SIZE_MIN && size <= BATCH_SIZE_MAX;
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/lib/previewGate.ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
/**
 * Identifies the inputs a preview was made for. Deleting is only offered while the current inputs
 * still have the key of the last preview.
 */
function previewInputKey(idSites, from, to, segment) {
  return JSON.stringify([idSites, from, to, segment.trim()]);
}
function isPreviewFresh(previewKey, currentKey) {
  return previewKey !== '' && previewKey === currentKey;
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/lib/segment.ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
/**
 * Matomo's segment editor and stored segments encode condition values twice; the API expects them
 * encoded once. Plain text input is returned unchanged.
 */
function decodeOnce(definition) {
  try {
    return decodeURIComponent(definition);
  } catch (e) {
    return definition;
  }
}
function storedSegmentOptions(segments) {
  const seen = {};
  const options = [];
  segments.forEach(segment => {
    const definition = decodeOnce(segment.definition);
    if (!seen[definition]) {
      seen[definition] = true;
      options.push({
        key: definition,
        value: `${segment.name} (${definition})`
      });
    }
  });
  return options.sort((a, b) => a.value.localeCompare(b.value));
}
// Longest operators first, so that '<=' is not read as '<'.
const CONDITION = /^(\w+)(==|!=|<=|>=|=@|!@|=\^|=\$|<|>)([\s\S]*)$/;
function conditions(definition) {
  return definition.split(/[;,]/);
}
/**
 * True when a condition of the API-encoded definition has no value. Matomo reads "x==" as
 * "is NULL, empty or 0", which the editor produces on its own for a fresh condition.
 */
function hasEmptyCondition(definition) {
  return conditions(definition).some(condition => {
    const match = CONDITION.exec(condition.trim());
    return match !== null && match[3] === '';
  });
}
/**
 * Inverse of decodeOnce for definitions: the segment editor expects condition values encoded twice.
 */
function toEditorEncoding(definition) {
  let result = '';
  let position = 0;
  conditions(definition).forEach((condition, index) => {
    if (index > 0) {
      result += definition[position - 1];
    }
    const match = CONDITION.exec(condition);
    result += match ? `${match[1]}${match[2]}${encodeURIComponent(match[3])}` : condition;
    position += condition.length + 1;
  });
  return result;
}
/** Same limit as the server, which checks strlen() (UTF-8 bytes). */
const SEGMENT_MAX_LENGTH = 8192;
/** Length of the trimmed definition in UTF-8 bytes, as the server counts it. */
function segmentLength(definition) {
  const trimmed = definition.trim();
  if (typeof TextEncoder !== 'undefined') {
    return new TextEncoder().encode(trimmed).length;
  }
  return unescape(encodeURIComponent(trimmed)).length;
}
function isSegmentTooLong(definition) {
  return segmentLength(definition) > SEGMENT_MAX_LENGTH;
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/lib/progress.ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
function percent(deleted, estimated) {
  if (!estimated || estimated <= 0) {
    return 0;
  }
  return Math.max(0, Math.min(100, Math.round(deleted / estimated * 100)));
}
/** Deleted visits per second between the oldest and the newest sample. */
function rate(samples) {
  if (samples.length < 2) {
    return null;
  }
  const first = samples[0];
  const last = samples[samples.length - 1];
  const seconds = (last.at - first.at) / 1000;
  if (seconds <= 0 || last.deleted <= first.deleted) {
    return null;
  }
  return (last.deleted - first.deleted) / seconds;
}
function etaSeconds(deleted, estimated, visitsPerSecond) {
  if (!estimated || !visitsPerSecond) {
    return null;
  }
  return Math.max(0, Math.round((estimated - deleted) / visitsPerSecond));
}
function addSample(samples, sample, keep = 10) {
  return [...samples, sample].slice(-keep);
}
function formatDuration(seconds) {
  if (seconds < 60) {
    return `${seconds} s`;
  }
  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) {
    return `${minutes} min`;
  }
  return `${Math.floor(minutes / 60)} h ${minutes % 60} min`;
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/lib/jobsLoop.ts
function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */



const ERROR_RETRY_MS = 10000;
/**
 * Keeps the queue moving while the page is open: calls processJobs until no queued or running job
 * is listed and no started job is still watched (held jobs stay listed but do not keep it going),
 * pausing while another process works or after an error, and reports every job that finished.
 */
class jobsLoop_JobsLoop {
  constructor(deps) {
    _defineProperty(this, "state", Object(external_commonjs_vue_commonjs2_vue_root_Vue_["reactive"])({
      active: [],
      busy: false,
      running: false,
      error: null,
      samples: {},
      finishedCount: 0
    }));
    _defineProperty(this, "watched", []);
    _defineProperty(this, "reported", []);
    _defineProperty(this, "deps", void 0);
    this.deps = deps;
  }
  /** Jobs known from the page load, e.g. after a reload during a deletion. */
  setActive(jobs) {
    this.state.active = jobs.filter(job => isActive(job.status));
    this.recordSamples();
  }
  /**
   * Report this job when it finishes, even if it never shows up as active
   * (small deletions, retries).
   */
  watch(idJob) {
    this.reported = this.reported.filter(id => id !== idJob);
    if (this.watched.indexOf(idJob) === -1) {
      this.watched.push(idJob);
    }
  }
  /**
   * Show a freshly started job at once instead of with the first processJobs response. Never
   * replaces a job that is already listed: the response data is newer.
   */
  show(job) {
    if (!isActive(job.status) || this.reported.indexOf(job.idjob) !== -1 || this.state.active.some(active => active.idjob === job.idjob)) {
      return;
    }
    this.state.active = this.state.active.concat([job]);
    this.recordSamples();
  }
  async run() {
    if (this.state.running) {
      return;
    }
    this.state.running = true;
    try {
      let more = true;
      while (more) {
        // Sequential on purpose: each call works on the queue for up to 15 s.
        // eslint-disable-next-line no-await-in-loop
        more = await this.tick();
      }
    } finally {
      this.state.running = false;
    }
  }
  /** Errors are passed to the caller, which shows them next to the button. */
  async cancel(idJob) {
    const job = await this.deps.cancelJob(idJob);
    await this.update(this.state.active.filter(active => active.idjob !== idJob), [job]);
  }
  /**
   * One processJobs call plus the pause before the next one; false when the response lists no
   * queued or running job and nothing is watched.
   */
  async tick() {
    let delay;
    try {
      const result = await this.deps.processJobs();
      this.state.error = null;
      this.state.busy = result.busy;
      await this.update(result.jobs);
      const working = result.jobs.some(job => job.status === 'queued' || job.status === 'running');
      if (!working && this.watched.length === 0) {
        return false;
      }
      delay = nextPollDelayMs(result.busy);
    } catch (e) {
      this.state.error = this.deps.describeError(e);
      delay = ERROR_RETRY_MS;
    }
    if (delay > 0) {
      await this.deps.wait(delay);
    }
    return true;
  }
  async update(jobs, known = []) {
    const active = jobs.filter(job => isActive(job.status) && this.reported.indexOf(job.idjob) === -1);
    const activeIds = active.map(job => job.idjob);
    const candidates = this.state.active.map(job => job.idjob).concat(this.watched);
    const gone = candidates.filter((id, index) => candidates.indexOf(id) === index && activeIds.indexOf(id) === -1 && this.reported.indexOf(id) === -1);
    this.state.active = active;
    this.watched = this.watched.filter(id => activeIds.indexOf(id) !== -1);
    this.recordSamples();
    if (gone.length === 0) {
      return;
    }
    // Marked before awaiting so that overlapping calls (cancel and tick) report a job only once.
    this.reported = this.reported.concat(gone);
    const finalJobs = await Promise.all(gone.map(id => {
      const knownJob = known.find(candidate => candidate.idjob === id);
      return knownJob ? Promise.resolve(knownJob) : this.deps.getJob(id).catch(() => null);
    }));
    let finishedAny = false;
    finalJobs.forEach((job, index) => {
      if (job && isActive(job.status)) {
        // Not finished: the response predates the job (or its start). Keep following it.
        this.reported = this.reported.filter(id => id !== gone[index]);
        if (this.watched.indexOf(gone[index]) === -1) {
          this.watched.push(gone[index]);
        }
      } else if (job) {
        this.deps.finished(job);
        finishedAny = true;
      }
    });
    if (finishedAny) {
      this.state.finishedCount += 1;
    }
  }
  recordSamples() {
    const samples = {};
    this.state.active.filter(job => job.status === 'running').forEach(job => {
      samples[job.idjob] = addSample(this.state.samples[job.idjob] || [], {
        at: this.deps.now(),
        deleted: job.deleted_visits
      });
    });
    this.state.samples = samples;
  }
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/stores/Jobs.store.ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */




function notifyFinished(job) {
  let message;
  let context;
  if (job.status === 'done') {
    message = Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_JobFinished', job.idjob, formatCount(job.deleted_visits));
    context = 'success';
  } else if (job.status === 'failed') {
    message = Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_ErrorJobFailed', job.idjob, external_CoreHome_["Matomo"].helper.htmlEntities(job.error || ''));
    context = 'error';
  } else {
    message = Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_ErrorJobCancelled', job.idjob);
    context = 'info';
  }
  external_CoreHome_["NotificationsStore"].show({
    message,
    context,
    id: `fwdvf-job-${job.idjob}`,
    type: 'transient'
  });
}
/* harmony default export */ var Jobs_store = (new jobsLoop_JobsLoop({
  processJobs: processJobs,
  getJob: getJob,
  cancelJob: cancelJob,
  finished: notifyFinished,
  describeError: errorMessage,
  wait: ms => new Promise(resolve => {
    setTimeout(resolve, ms);
  }),
  now: () => Date.now()
}));
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/ConfirmDeleteDialog.vue?vue&type=template&id=31e09760

const ConfirmDeleteDialogvue_type_template_id_31e09760_hoisted_1 = {
  class: "ui-confirm fwdvf-confirm"
};
const ConfirmDeleteDialogvue_type_template_id_31e09760_hoisted_2 = {
  class: "fwdvf-code fwdvf-confirm-segment"
};
const ConfirmDeleteDialogvue_type_template_id_31e09760_hoisted_3 = {
  key: 0,
  class: "fwdvf-confirm-typing"
};
const _hoisted_4 = {
  for: "fwdvf-confirm-count"
};
const _hoisted_5 = {
  key: 0,
  class: "fwdvf-error"
};
const _hoisted_6 = ["value"];
const _hoisted_7 = ["value"];
function ConfirmDeleteDialogvue_type_template_id_31e09760_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_MatomoDialog = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("MatomoDialog");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_MatomoDialog, {
    "model-value": _ctx.modelValue,
    options: _ctx.dialogOptions,
    "onUpdate:modelValue": _cache[1] || (_cache[1] = $event => _ctx.$emit('update:modelValue', $event)),
    onValidation: _cache[2] || (_cache[2] = $event => _ctx.confirm())
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", ConfirmDeleteDialogvue_type_template_id_31e09760_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h2", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ConfirmTitle')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("ul", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("li", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ConfirmVisits', _ctx.formattedTotal)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("li", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ConfirmWebsites', _ctx.sitesText)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("li", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ConfirmDateRange', _ctx.dateRangeText)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("li", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ConfirmSegmentLabel')) + " ", 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("code", ConfirmDeleteDialogvue_type_template_id_31e09760_hoisted_2, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.segment), 1)])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ConfirmIrreversible')), 1), _ctx.needsTyping ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", ConfirmDeleteDialogvue_type_template_id_31e09760_hoisted_3, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("label", _hoisted_4, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ConfirmTypeCount', _ctx.formattedTotal)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
      id: "fwdvf-confirm-count",
      "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => _ctx.typed = $event),
      type: "text",
      class: "browser-default",
      autocomplete: "off"
    }, null, 512), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vModelText"], _ctx.typed]]), _ctx.mismatch ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", _hoisted_5, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ConfirmTypeMismatch')), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
      type: "button",
      role: "no",
      value: _ctx.translate('FeinwerkDeleteVisitsFilter_Cancel')
    }, null, 8, _hoisted_6), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
      type: "button",
      role: "validation",
      value: _ctx.translate('FeinwerkDeleteVisitsFilter_DeleteNow')
    }, null, 8, _hoisted_7)])]),
    _: 1
  }, 8, ["model-value", "options"]);
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/ConfirmDeleteDialog.vue?vue&type=template&id=31e09760

// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/lib/confirm.ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
const DEFAULT_CONFIRM_THRESHOLD = 10000;
function requiresTypedConfirmation(total, threshold) {
  return total >= threshold;
}
/** Accepts numbers with thousands separators: dot, comma, space, or typographic apostrophe. */
function typedCountMatches(input, total) {
  const digits = input.trim().replace(/(\d)[\s.',’](?=\d{3}(\D|$))/g, '$1');
  return /^\d+$/.test(digits) && parseInt(digits, 10) === total;
}
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/ConfirmDeleteDialog.vue?vue&type=script&lang=ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */




/* harmony default export */ var ConfirmDeleteDialogvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    modelValue: {
      type: Boolean,
      required: true
    },
    total: {
      type: Number,
      required: true
    },
    threshold: {
      type: Number,
      required: true
    },
    sitesText: {
      type: String,
      required: true
    },
    dateRangeText: {
      type: String,
      required: true
    },
    segment: {
      type: String,
      required: true
    }
  },
  emits: ['update:modelValue', 'confirm'],
  components: {
    MatomoDialog: external_CoreHome_["MatomoDialog"]
  },
  data() {
    return {
      typed: '',
      mismatch: false
    };
  },
  computed: {
    needsTyping() {
      return requiresTypedConfirmation(this.total, this.threshold);
    },
    // Read when the dialog opens: focus the typed-count input, else the cancel link (never the
    // destructive one, which Matomo would otherwise focus because it is the last footer link).
    dialogOptions() {
      return {
        focusSelector: this.needsTyping ? '#fwdvf-confirm-count' : '.modal.open .modal-footer a:first-child'
      };
    },
    formattedTotal() {
      return formatCount(this.total);
    }
  },
  watch: {
    modelValue(open) {
      if (open) {
        this.typed = '';
        this.mismatch = false;
      }
    }
  },
  methods: {
    // role="validation" does not close the dialog by itself:
    // it stays open until the number matches.
    confirm() {
      if (!this.modelValue) {
        return;
      }
      if (this.needsTyping && !typedCountMatches(this.typed, this.total)) {
        this.mismatch = true;
        return;
      }
      this.$emit('update:modelValue', false);
      this.$emit('confirm');
    }
  }
}));
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/ConfirmDeleteDialog.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/ConfirmDeleteDialog.vue



ConfirmDeleteDialogvue_type_script_lang_ts.render = ConfirmDeleteDialogvue_type_template_id_31e09760_render

/* harmony default export */ var ConfirmDeleteDialog = (ConfirmDeleteDialogvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/DateRangeSelect.vue?vue&type=template&id=b8a3316e

const DateRangeSelectvue_type_template_id_b8a3316e_hoisted_1 = {
  class: "fwdvf-section fwdvf-daterange"
};
const DateRangeSelectvue_type_template_id_b8a3316e_hoisted_2 = {
  key: 0,
  class: "fwdvf-daterange-picker"
};
const DateRangeSelectvue_type_template_id_b8a3316e_hoisted_3 = {
  class: "fwdvf-muted"
};
function DateRangeSelectvue_type_template_id_b8a3316e_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_Field = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Field");
  const _component_DateRangePicker = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("DateRangePicker");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", DateRangeSelectvue_type_template_id_b8a3316e_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h3", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SectionDateRange')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
    uicontrol: "select",
    name: "fwdvf-date-preset",
    "model-value": _ctx.modelValue.preset,
    options: _ctx.presetOptions,
    "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => _ctx.selectPreset($event))
  }, null, 8, ["model-value", "options"]), _ctx.modelValue.preset === 'custom' ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", DateRangeSelectvue_type_template_id_b8a3316e_hoisted_2, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_DateRangePicker, {
    "start-date": _ctx.modelValue.from,
    "end-date": _ctx.modelValue.to,
    onRangeChange: _cache[1] || (_cache[1] = $event => _ctx.changeRange($event))
  }, null, 8, ["start-date", "end-date"])])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", DateRangeSelectvue_type_template_id_b8a3316e_hoisted_3, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_DateRangeValue', _ctx.modelValue.from, _ctx.modelValue.to)) + ". " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_DateRangeTimezoneHint')), 1)]);
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/DateRangeSelect.vue?vue&type=template&id=b8a3316e

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/DateRangeSelect.vue?vue&type=script&lang=ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */




const PRESET_KEYS = {
  today: 'FeinwerkDeleteVisitsFilter_PresetToday',
  yesterday: 'FeinwerkDeleteVisitsFilter_PresetYesterday',
  last7: 'FeinwerkDeleteVisitsFilter_PresetLast7',
  last30: 'FeinwerkDeleteVisitsFilter_PresetLast30',
  lastMonth: 'FeinwerkDeleteVisitsFilter_PresetLastMonth',
  thisYear: 'FeinwerkDeleteVisitsFilter_PresetThisYear',
  lastYear: 'FeinwerkDeleteVisitsFilter_PresetLastYear',
  all: 'FeinwerkDeleteVisitsFilter_PresetAll',
  custom: 'FeinwerkDeleteVisitsFilter_PresetCustom'
};
/* harmony default export */ var DateRangeSelectvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    modelValue: {
      type: Object,
      required: true
    }
  },
  emits: ['update:modelValue'],
  components: {
    DateRangePicker: external_CoreHome_["DateRangePicker"],
    Field: external_CorePluginsAdmin_["Field"]
  },
  computed: {
    presetOptions() {
      return DATE_PRESETS.map(preset => ({
        key: preset,
        value: Object(external_CoreHome_["translate"])(PRESET_KEYS[preset])
      }));
    }
  },
  methods: {
    selectPreset(preset) {
      const range = rangeForPreset(preset, new Date());
      // 'custom' keeps the current days as the starting point for the calendar.
      this.$emit('update:modelValue', range ? Object.assign({
        preset
      }, range) : Object.assign(Object.assign({}, this.modelValue), {}, {
        preset
      }));
    },
    changeRange(range) {
      this.$emit('update:modelValue', {
        preset: 'custom',
        from: range.start || this.modelValue.from,
        to: range.end || this.modelValue.to
      });
    }
  }
}));
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/DateRangeSelect.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/DateRangeSelect.vue



DateRangeSelectvue_type_script_lang_ts.render = DateRangeSelectvue_type_template_id_b8a3316e_render

/* harmony default export */ var DateRangeSelect = (DateRangeSelectvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/PreviewResult.vue?vue&type=template&id=218f7a0e

const PreviewResultvue_type_template_id_218f7a0e_hoisted_1 = {
  class: "fwdvf-preview"
};
const PreviewResultvue_type_template_id_218f7a0e_hoisted_2 = {
  class: "entityTable dataTable fwdvf-table"
};
const PreviewResultvue_type_template_id_218f7a0e_hoisted_3 = {
  class: "fwdvf-num"
};
const PreviewResultvue_type_template_id_218f7a0e_hoisted_4 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, null, -1);
const PreviewResultvue_type_template_id_218f7a0e_hoisted_5 = {
  class: "fwdvf-muted"
};
const PreviewResultvue_type_template_id_218f7a0e_hoisted_6 = {
  class: "fwdvf-num"
};
const PreviewResultvue_type_template_id_218f7a0e_hoisted_7 = ["href"];
const _hoisted_8 = {
  key: 0
};
const _hoisted_9 = {
  class: "fwdvf-total"
};
const _hoisted_10 = {
  class: "fwdvf-num"
};
const _hoisted_11 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, null, -1);
const _hoisted_12 = {
  class: "fwdvf-table-scroll"
};
const _hoisted_13 = {
  class: "entityTable dataTable fwdvf-table fwdvf-sample"
};
const _hoisted_14 = {
  key: 0
};
const _hoisted_15 = {
  class: "fwdvf-num"
};
const _hoisted_16 = {
  key: 1
};
const _hoisted_17 = {
  class: "fwdvf-nowrap"
};
const _hoisted_18 = {
  key: 0
};
const _hoisted_19 = {
  class: "fwdvf-num"
};
const _hoisted_20 = ["title"];
const _hoisted_21 = {
  key: 1
};
const _hoisted_22 = ["onClick"];
function PreviewResultvue_type_template_id_218f7a0e_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_Alert = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Alert");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", PreviewResultvue_type_template_id_218f7a0e_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h3", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_PreviewTotal', _ctx.formatCount(_ctx.preview.total))), 1), _ctx.preview.action_level ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_Alert, {
    key: 0,
    severity: "info"
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ActionLevelHint')), 1)]),
    _: 1
  })) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("table", PreviewResultvue_type_template_id_218f7a0e_hoisted_2, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("thead", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tr", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_PreviewWebsite')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", PreviewResultvue_type_template_id_218f7a0e_hoisted_3, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_PreviewVisits')), 1), PreviewResultvue_type_template_id_218f7a0e_hoisted_4])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tbody", null, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.preview.sites, site => {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("tr", {
      key: site.idsite
    }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(site.name) + " ", 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", PreviewResultvue_type_template_id_218f7a0e_hoisted_5, "(" + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(site.idsite) + ")", 1)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", PreviewResultvue_type_template_id_218f7a0e_hoisted_6, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.formatCount(site.count)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, [site.count > 0 ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("a", {
      key: 0,
      href: _ctx.logUrl(site.idsite),
      target: "_blank",
      rel: "noreferrer noopener"
    }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ViewInVisitorLog')), 9, PreviewResultvue_type_template_id_218f7a0e_hoisted_7)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)])]);
  }), 128))]), _ctx.multipleSites ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("tfoot", _hoisted_8, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tr", _hoisted_9, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_PreviewSum')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_10, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.formatCount(_ctx.preview.total)), 1), _hoisted_11])])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)]), _ctx.preview.sample.length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], {
    key: 1
  }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h4", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SampleTitle')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", _hoisted_12, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("table", _hoisted_13, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("thead", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tr", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SampleTime')), 1), _ctx.multipleSites ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("th", _hoisted_14, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SampleWebsite')), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SampleIp')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SampleDevice')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", _hoisted_15, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SampleActions')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SampleEntry')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SampleReferrer')), 1), _ctx.profileEnabled ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("th", _hoisted_16)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tbody", null, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.preview.sample, visit => {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("tr", {
      key: `${visit.idsite}-${visit.idvisit}`
    }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_17, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(visit.time), 1), _ctx.multipleSites ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("td", _hoisted_18, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.siteName(visit.idsite)), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(visit.ip), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(visit.browser) + " / " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(visit.os), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", _hoisted_19, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(visit.actions), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", {
      class: "fwdvf-url",
      title: visit.entry_url
    }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(visit.entry_url), 9, _hoisted_20), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(visit.referrer_name || '-'), 1), _ctx.profileEnabled ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("td", _hoisted_21, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("a", {
      href: "",
      onClick: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withModifiers"])($event => _ctx.showProfile(visit), ["prevent"])
    }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SampleProfile')), 9, _hoisted_22)])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)]);
  }), 128))])])])], 64)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)]);
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/PreviewResult.vue?vue&type=template&id=218f7a0e

// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/lib/visitorLog.ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
/** Link to Matomo's visitor log for one website, date range and segment (relative to index.php). */
function visitorLogUrl(idSite, from, to, segment) {
  const context = `idSite=${idSite}&period=range&date=${from},${to}`;
  return `?module=CoreHome&action=index&${context}#?${context}` + `&category=General_Visitors&subcategory=Live_VisitorLog&segment=${encodeURIComponent(segment)}`;
}
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/PreviewResult.vue?vue&type=script&lang=ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */




/* harmony default export */ var PreviewResultvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    preview: {
      type: Object,
      required: true
    },
    dateFrom: {
      type: String,
      required: true
    },
    dateTo: {
      type: String,
      required: true
    },
    segment: {
      type: String,
      required: true
    }
  },
  components: {
    Alert: external_CoreHome_["Alert"]
  },
  computed: {
    profileEnabled() {
      return !!external_CoreHome_["Matomo"].visitorProfileEnabled;
    },
    multipleSites() {
      return this.preview.sites.length > 1;
    }
  },
  methods: {
    formatCount: formatCount,
    logUrl(idSite) {
      return visitorLogUrl(idSite, this.dateFrom, this.dateTo, this.segment);
    },
    siteName(idSite) {
      const site = this.preview.sites.find(candidate => candidate.idsite === idSite);
      return site ? site.name : `#${idSite}`;
    },
    showProfile(visit) {
      external_CoreHome_["Matomo"].helper.showVisitorProfilePopup(visit.visitor_id, visit.idsite);
    }
  }
}));
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/PreviewResult.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/PreviewResult.vue



PreviewResultvue_type_script_lang_ts.render = PreviewResultvue_type_template_id_218f7a0e_render

/* harmony default export */ var PreviewResult = (PreviewResultvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/SegmentInput.vue?vue&type=template&id=1f591f18

const SegmentInputvue_type_template_id_1f591f18_hoisted_1 = {
  class: "fwdvf-section fwdvf-segment"
};
const SegmentInputvue_type_template_id_1f591f18_hoisted_2 = {
  class: "fwdvf-segment-header"
};
const SegmentInputvue_type_template_id_1f591f18_hoisted_3 = ["aria-label"];
const SegmentInputvue_type_template_id_1f591f18_hoisted_4 = ["aria-pressed"];
const SegmentInputvue_type_template_id_1f591f18_hoisted_5 = ["aria-pressed"];
const SegmentInputvue_type_template_id_1f591f18_hoisted_6 = {
  key: 0,
  class: "fwdvf-stored-segment"
};
const SegmentInputvue_type_template_id_1f591f18_hoisted_7 = {
  key: 1,
  class: "fwdvf-segment-editor"
};
const SegmentInputvue_type_template_id_1f591f18_hoisted_8 = {
  key: 2,
  class: "fwdvf-segment-text-wrap"
};
const SegmentInputvue_type_template_id_1f591f18_hoisted_9 = ["aria-label", "aria-describedby", "aria-invalid", "value"];
const SegmentInputvue_type_template_id_1f591f18_hoisted_10 = {
  class: "fwdvf-segment-text-footer"
};
const SegmentInputvue_type_template_id_1f591f18_hoisted_11 = ["id"];
const SegmentInputvue_type_template_id_1f591f18_hoisted_12 = ["id"];
const SegmentInputvue_type_template_id_1f591f18_hoisted_13 = ["title"];
function SegmentInputvue_type_template_id_1f591f18_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_Field = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Field");
  const _component_SegmentGenerator = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SegmentGenerator");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", SegmentInputvue_type_template_id_1f591f18_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", SegmentInputvue_type_template_id_1f591f18_hoisted_2, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h3", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SectionSegment')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", {
    class: "fwdvf-segment-mode",
    role: "group",
    "aria-label": _ctx.translate('FeinwerkDeleteVisitsFilter_SegmentModeLabel')
  }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
    type: "button",
    "aria-pressed": !_ctx.asText ? 'true' : 'false',
    class: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeClass"])({
      'fwdvf-active': !_ctx.asText
    }),
    onClick: _cache[0] || (_cache[0] = $event => _ctx.setMode(false))
  }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SegmentModeEditor')), 11, SegmentInputvue_type_template_id_1f591f18_hoisted_4), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
    type: "button",
    "aria-pressed": _ctx.asText ? 'true' : 'false',
    class: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeClass"])({
      'fwdvf-active': _ctx.asText
    }),
    onClick: _cache[1] || (_cache[1] = $event => _ctx.setMode(true))
  }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SegmentModeText')), 11, SegmentInputvue_type_template_id_1f591f18_hoisted_5)], 8, SegmentInputvue_type_template_id_1f591f18_hoisted_3)]), _ctx.storedOptions.length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", SegmentInputvue_type_template_id_1f591f18_hoisted_6, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
    uicontrol: "select",
    name: "fwdvf-stored-segment",
    title: _ctx.translate('FeinwerkDeleteVisitsFilter_StoredSegment'),
    "model-value": _ctx.stored,
    options: _ctx.options,
    "onUpdate:modelValue": _cache[2] || (_cache[2] = $event => _ctx.chooseStored($event))
  }, null, 8, ["title", "model-value", "options"])])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), !_ctx.asText ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", SegmentInputvue_type_template_id_1f591f18_hoisted_7, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SegmentGenerator, {
    "model-value": _ctx.editorValue,
    idsite: _ctx.generatorSite,
    "add-initial-condition": true,
    "onUpdate:modelValue": _cache[3] || (_cache[3] = $event => _ctx.fromEditor($event))
  }, null, 8, ["model-value", "idsite"])])) : (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", SegmentInputvue_type_template_id_1f591f18_hoisted_8, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("textarea", {
    ref: "text",
    class: "browser-default fwdvf-segment-text",
    rows: "10",
    spellcheck: "false",
    autocomplete: "off",
    "aria-label": _ctx.translate('FeinwerkDeleteVisitsFilter_SegmentFilter'),
    "aria-describedby": `${_ctx.helpId} ${_ctx.countId}`,
    "aria-invalid": _ctx.tooLong ? 'true' : 'false',
    value: _ctx.modelValue,
    onInput: _cache[4] || (_cache[4] = $event => _ctx.fromText($event))
  }, null, 40, SegmentInputvue_type_template_id_1f591f18_hoisted_9), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", SegmentInputvue_type_template_id_1f591f18_hoisted_10, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
    id: _ctx.helpId,
    class: "fwdvf-segment-help"
  }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SegmentFilterHelp')), 9, SegmentInputvue_type_template_id_1f591f18_hoisted_11), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
    id: _ctx.countId,
    class: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeClass"])(["fwdvf-segment-count", {
      'fwdvf-over': _ctx.tooLong
    }])
  }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SegmentLength', _ctx.formatCount(_ctx.length), _ctx.formatCount(_ctx.maxLength))), 11, SegmentInputvue_type_template_id_1f591f18_hoisted_12)])])), !_ctx.asText && _ctx.modelValue.trim() ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", {
    key: 3,
    class: "fwdvf-code fwdvf-segment-definition",
    title: _ctx.modelValue.trim()
  }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.modelValue.trim()), 9, SegmentInputvue_type_template_id_1f591f18_hoisted_13)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)]);
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/SegmentInput.vue?vue&type=template&id=1f591f18

// EXTERNAL MODULE: external "SegmentEditor"
var external_SegmentEditor_ = __webpack_require__("f06f");

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/SegmentInput.vue?vue&type=script&lang=ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */







/** Gives every instance its own element ids. */
let instanceCount = 0;
/* harmony default export */ var SegmentInputvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    /** Segment in the API encoding (values encoded once). */
    modelValue: {
      type: String,
      required: true
    },
    idSite: {
      type: Number,
      default: null
    },
    allSites: {
      type: Boolean,
      default: false
    }
  },
  emits: ['update:modelValue'],
  components: {
    Field: external_CorePluginsAdmin_["Field"],
    SegmentGenerator: external_SegmentEditor_["SegmentGenerator"]
  },
  data() {
    instanceCount += 1;
    return {
      uid: instanceCount,
      asText: false,
      editorValue: toEditorEncoding(this.modelValue),
      stored: '',
      storedOptions: []
    };
  },
  computed: {
    helpId() {
      return `fwdvf-segment-help-${this.uid}`;
    },
    countId() {
      return `fwdvf-segment-count-${this.uid}`;
    },
    length() {
      return segmentLength(this.modelValue);
    },
    maxLength() {
      return SEGMENT_MAX_LENGTH;
    },
    tooLong() {
      return isSegmentTooLong(this.modelValue);
    },
    generatorSite() {
      return this.allSites || this.idSite === null ? 'all' : this.idSite;
    },
    options() {
      return [{
        key: '',
        value: Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_StoredSegmentNone')
      }, ...this.storedOptions];
    }
  },
  watch: {
    idSite: {
      immediate: true,
      handler(idSite) {
        this.loadStored(idSite);
      }
    },
    modelValue(value) {
      // Changed from outside (template from the history): show it in the editor as well.
      if (value !== decodeOnce(this.editorValue)) {
        this.editorValue = toEditorEncoding(value);
      }
      if (value !== this.stored) {
        this.stored = '';
      }
      // Typed or set from outside: fit the text field once the new value is rendered.
      if (this.asText) {
        Object(external_commonjs_vue_commonjs2_vue_root_Vue_["nextTick"])(() => this.fitHeight());
      }
    }
  },
  mounted() {
    window.addEventListener('resize', this.onResize);
  },
  beforeUnmount() {
    window.removeEventListener('resize', this.onResize);
  },
  methods: {
    formatCount: formatCount,
    async loadStored(idSite) {
      if (idSite === null) {
        this.storedOptions = [];
        return;
      }
      try {
        const options = storedSegmentOptions(await storedSegments(idSite));
        if (this.idSite === idSite) {
          this.storedOptions = options;
        }
      } catch (e) {
        // Saved segments are only a shortcut;
        // the editor and the text field still work without them.
        this.storedOptions = [];
      }
    },
    chooseStored(definition) {
      this.stored = definition;
      if (definition) {
        this.editorValue = toEditorEncoding(definition);
        this.$emit('update:modelValue', definition);
      }
    },
    fromEditor(value) {
      this.editorValue = value;
      this.$emit('update:modelValue', decodeOnce(value));
    },
    fromText(event) {
      // The modelValue watcher fits the height.
      this.$emit('update:modelValue', event.target.value || '');
    },
    onResize() {
      if (this.asText) {
        this.fitHeight();
      }
    },
    /** Grows the text field with its content; CSS min-height and max-height clamp it. */
    fitHeight() {
      const el = this.$refs.text;
      if (!el) {
        return;
      }
      // The page gets shorter for a moment while the height is 'auto'; keep it from scrolling.
      const scroller = document.scrollingElement;
      const top = scroller ? scroller.scrollTop : 0;
      el.style.height = 'auto';
      el.style.height = `${el.scrollHeight + 2}px`;
      if (scroller) {
        scroller.scrollTop = top;
      }
    },
    setMode(text) {
      if (text === this.asText) {
        return;
      }
      if (this.asText) {
        this.editorValue = toEditorEncoding(this.modelValue);
      }
      this.asText = text;
      if (text) {
        Object(external_commonjs_vue_commonjs2_vue_root_Vue_["nextTick"])(() => this.fitHeight());
      }
    }
  }
}));
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/SegmentInput.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/SegmentInput.vue



SegmentInputvue_type_script_lang_ts.render = SegmentInputvue_type_template_id_1f591f18_render

/* harmony default export */ var SegmentInput = (SegmentInputvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/SitePicker.vue?vue&type=template&id=88002fca

const SitePickervue_type_template_id_88002fca_hoisted_1 = {
  class: "fwdvf-section fwdvf-sites"
};
const SitePickervue_type_template_id_88002fca_hoisted_2 = {
  class: "fwdvf-radios"
};
const SitePickervue_type_template_id_88002fca_hoisted_3 = ["value", "checked", "onChange"];
const SitePickervue_type_template_id_88002fca_hoisted_4 = {
  key: 0,
  class: "sites_autocomplete"
};
const SitePickervue_type_template_id_88002fca_hoisted_5 = {
  key: 1
};
const SitePickervue_type_template_id_88002fca_hoisted_6 = {
  class: "sites_autocomplete"
};
const SitePickervue_type_template_id_88002fca_hoisted_7 = {
  class: "fwdvf-sites-pattern"
};
const SitePickervue_type_template_id_88002fca_hoisted_8 = ["aria-label", "placeholder"];
const SitePickervue_type_template_id_88002fca_hoisted_9 = ["disabled"];
const SitePickervue_type_template_id_88002fca_hoisted_10 = {
  key: 0,
  class: "fwdvf-hint"
};
const SitePickervue_type_template_id_88002fca_hoisted_11 = {
  key: 0,
  class: "fwdvf-hint"
};
const SitePickervue_type_template_id_88002fca_hoisted_12 = {
  key: 1,
  class: "fwdvf-site-list"
};
const SitePickervue_type_template_id_88002fca_hoisted_13 = {
  class: "fwdvf-muted"
};
const SitePickervue_type_template_id_88002fca_hoisted_14 = ["title", "onClick"];
const SitePickervue_type_template_id_88002fca_hoisted_15 = {
  key: 2,
  class: "fwdvf-hint"
};
function SitePickervue_type_template_id_88002fca_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_SiteSelector = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("SiteSelector");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", SitePickervue_type_template_id_88002fca_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h3", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_SectionWebsites')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", SitePickervue_type_template_id_88002fca_hoisted_2, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.modes, option => {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("label", {
      key: option.key
    }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
      type: "radio",
      class: "with-gap",
      name: "fwdvf-site-mode",
      value: option.key,
      checked: _ctx.modelValue.mode === option.key,
      onChange: $event => _ctx.setMode(option.key)
    }, null, 40, SitePickervue_type_template_id_88002fca_hoisted_3), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(option.label), 1)]);
  }), 128))]), _ctx.modelValue.mode === 'single' ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", SitePickervue_type_template_id_88002fca_hoisted_4, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SiteSelector, {
    "model-value": _ctx.singleRef,
    "show-all-sites-item": false,
    "switch-site-on-select": false,
    "show-selected-site": true,
    "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => _ctx.setSingle($event))
  }, null, 8, ["model-value"])])) : _ctx.modelValue.mode === 'multiple' ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", SitePickervue_type_template_id_88002fca_hoisted_5, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", SitePickervue_type_template_id_88002fca_hoisted_6, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_SiteSelector, {
    "model-value": _ctx.addRef,
    placeholder: _ctx.translate('FeinwerkDeleteVisitsFilter_AddWebsite'),
    "show-all-sites-item": false,
    "switch-site-on-select": false,
    "show-selected-site": true,
    "onUpdate:modelValue": _cache[1] || (_cache[1] = $event => _ctx.addSite($event))
  }, null, 8, ["model-value", "placeholder"])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", SitePickervue_type_template_id_88002fca_hoisted_7, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withDirectives"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
    "onUpdate:modelValue": _cache[2] || (_cache[2] = $event => _ctx.pattern = $event),
    type: "text",
    class: "browser-default",
    "aria-label": _ctx.translate('FeinwerkDeleteVisitsFilter_WebsitePattern'),
    placeholder: _ctx.translate('FeinwerkDeleteVisitsFilter_WebsitePattern'),
    onKeydown: _cache[3] || (_cache[3] = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withKeys"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withModifiers"])($event => _ctx.addMatching(), ["prevent"]), ["enter"]))
  }, null, 40, SitePickervue_type_template_id_88002fca_hoisted_8), [[external_commonjs_vue_commonjs2_vue_root_Vue_["vModelText"], _ctx.pattern]]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
    type: "button",
    class: "btn-flat",
    disabled: !_ctx.pattern.trim(),
    onClick: _cache[4] || (_cache[4] = $event => _ctx.addMatching())
  }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_AddMatchingWebsites')), 9, SitePickervue_type_template_id_88002fca_hoisted_9), _ctx.addedCount !== null ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("span", SitePickervue_type_template_id_88002fca_hoisted_10, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate(_ctx.addedHintKey(_ctx.addedCount), _ctx.addedCount)), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)]), !_ctx.modelValue.multiple.length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", SitePickervue_type_template_id_88002fca_hoisted_11, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_NoWebsitesSelected')), 1)) : (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("ul", SitePickervue_type_template_id_88002fca_hoisted_12, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.modelValue.multiple, id => {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("li", {
      key: id
    }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.nameOf(id)) + " ", 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", SitePickervue_type_template_id_88002fca_hoisted_13, "(" + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(id) + ")", 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
      type: "button",
      class: "btn-flat fwdvf-remove",
      title: _ctx.translate('FeinwerkDeleteVisitsFilter_RemoveWebsite'),
      onClick: $event => _ctx.remove(id)
    }, "×", 8, SitePickervue_type_template_id_88002fca_hoisted_14)]);
  }), 128))]))])) : (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", SitePickervue_type_template_id_88002fca_hoisted_15, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_AllWebsitesHint', _ctx.allIds.length)), 1))]);
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/SitePicker.vue?vue&type=template&id=88002fca

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/SitePicker.vue?vue&type=script&lang=ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */



/* harmony default export */ var SitePickervue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    modelValue: {
      type: Object,
      required: true
    },
    siteNames: {
      type: Object,
      required: true
    }
  },
  emits: ['update:modelValue'],
  components: {
    SiteSelector: external_CoreHome_["SiteSelector"]
  },
  data() {
    return {
      pattern: '',
      addedCount: null
    };
  },
  computed: {
    allIds() {
      return allSiteIds(this.siteNames);
    },
    modes() {
      return [{
        key: 'single',
        label: Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_SiteModeSingle')
      }, {
        key: 'multiple',
        label: Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_SiteModeMultiple')
      }, {
        key: 'all',
        label: Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_SiteModeAll')
      }];
    },
    singleRef() {
      const id = this.modelValue.single;
      return id === null ? undefined : {
        id,
        name: this.nameOf(id)
      };
    },
    addRef() {
      // An empty choice shows the placeholder and stops SiteSelector
      // from pre-selecting the current site.
      return {
        id: '',
        name: ''
      };
    }
  },
  methods: {
    addedHintKey: addedHintKey,
    nameOf(id) {
      var _this$siteNames$id;
      return (_this$siteNames$id = this.siteNames[id]) !== null && _this$siteNames$id !== void 0 ? _this$siteNames$id : `#${id}`;
    },
    update(changes) {
      this.$emit('update:modelValue', Object.assign(Object.assign({}, this.modelValue), changes));
    },
    setMode(mode) {
      const {
        single,
        multiple
      } = this.modelValue;
      // Start the list with the chosen website so switching modes keeps the selection.
      const prefill = mode === 'multiple' && multiple.length === 0 && single !== null ? [single] : multiple;
      this.update({
        mode,
        multiple: prefill
      });
    },
    toId(site) {
      if (!site || site.id === '' || site.id === 'all') {
        return null;
      }
      const id = Number(site.id);
      return Number.isNaN(id) ? null : id;
    },
    setSingle(site) {
      const id = this.toId(site);
      if (id !== null) {
        this.update({
          single: id
        });
      }
    },
    addSite(site) {
      const id = this.toId(site);
      if (id !== null) {
        this.addedCount = null;
        this.update({
          multiple: addSites(this.modelValue.multiple, [id])
        });
      }
    },
    addMatching() {
      const before = this.modelValue.multiple;
      const after = addSites(before, matchingSiteIds(this.siteNames, this.pattern));
      this.addedCount = after.length - before.length;
      this.update({
        multiple: after
      });
    },
    remove(id) {
      this.update({
        multiple: this.modelValue.multiple.filter(other => other !== id)
      });
    }
  }
}));
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/SitePicker.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/SitePicker.vue



SitePickervue_type_script_lang_ts.render = SitePickervue_type_template_id_88002fca_render

/* harmony default export */ var SitePicker = (SitePickervue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/DeleteTab.vue?vue&type=script&lang=ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
















/* harmony default export */ var DeleteTabvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    initialSiteId: {
      type: Number,
      required: true
    },
    siteNames: {
      type: Object,
      required: true
    },
    confirmThreshold: {
      type: Number,
      required: true
    },
    defaultBatchSize: {
      type: Number,
      required: true
    },
    template: {
      type: Object,
      default: null
    }
  },
  components: {
    Alert: external_CoreHome_["Alert"],
    ConfirmDeleteDialog: ConfirmDeleteDialog,
    ContentBlock: external_CoreHome_["ContentBlock"],
    DateRangeSelect: DateRangeSelect,
    Field: external_CorePluginsAdmin_["Field"],
    PreviewResult: PreviewResult,
    SaveButton: external_CorePluginsAdmin_["SaveButton"],
    SegmentInput: SegmentInput,
    SitePicker: SitePicker
  },
  data() {
    return {
      sites: {
        mode: 'single',
        single: this.initialSiteId || null,
        multiple: []
      },
      dates: Object.assign({
        preset: 'last7'
      }, rangeForPreset('last7', new Date())),
      segment: '',
      batchSize: '',
      attempted: false,
      previewing: false,
      creating: false,
      confirmOpen: false,
      preview: null,
      previewKey: '',
      error: null
    };
  },
  computed: {
    allIds() {
      return allSiteIds(this.siteNames);
    },
    selectedIds() {
      return selectedSiteIds(this.sites, this.allIds);
    },
    idSitesParam() {
      return siteParam(this.sites, this.allIds);
    },
    segmentSiteId() {
      if (this.selectedIds.length) {
        return this.selectedIds[0];
      }
      return this.allIds.length ? this.allIds[0] : null;
    },
    selectedSitesText() {
      return sitesText(this.selectedIds, this.siteNames, this.allIds);
    },
    inputKey() {
      return previewInputKey(this.idSitesParam, this.dates.from, this.dates.to, this.segment);
    },
    currentPreview() {
      return this.preview && isPreviewFresh(this.previewKey, this.inputKey) ? this.preview : null;
    },
    batchSizeHelp() {
      return `${Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_BatchSizeHelp')} ${Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_BatchSizeDefault', this.defaultBatchSize)}`;
    },
    inputError() {
      if (!this.selectedIds.length) {
        return Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_ErrorNoSite');
      }
      const problem = rangeProblem(this.dates);
      if (problem) {
        return Object(external_CoreHome_["translate"])(problem.key, problem.value);
      }
      if (!this.segment.trim()) {
        return Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_ErrorNoSegment');
      }
      if (isSegmentTooLong(this.segment)) {
        return Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_ErrorSegmentTooLong', formatCount(SEGMENT_MAX_LENGTH));
      }
      if (hasEmptyCondition(this.segment.trim())) {
        return Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_ErrorSegmentEmptyValue');
      }
      if (!isValidBatchSize(this.batchSize)) {
        return Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_SettingDefaultBatchSizeInvalid');
      }
      return null;
    }
  },
  watch: {
    template(template) {
      if (template) {
        this.applyTemplate(template);
      }
    }
  },
  methods: {
    formatCount: formatCount,
    dateRangeText: dateRangeText,
    applyTemplate(template) {
      this.sites = choiceFromIds(template.idSites, this.allIds);
      this.dates = {
        preset: 'custom',
        from: template.dateFrom,
        to: template.dateTo
      };
      this.segment = template.segment;
      this.preview = null;
      this.error = null;
      this.attempted = false;
    },
    async checkMatches() {
      this.attempted = true;
      if (this.inputError) {
        return;
      }
      const key = this.inputKey;
      this.error = null;
      this.previewing = true;
      try {
        this.preview = await previewDeletion(this.idSitesParam, this.segment.trim(), this.dates.from, this.dates.to);
        this.previewKey = key;
      } catch (e) {
        // A failure for inputs that were changed in the meantime is stale.
        if (key === this.inputKey) {
          this.error = errorMessage(e);
        }
      } finally {
        this.previewing = false;
      }
    },
    async startDeletion() {
      if (this.creating || !this.currentPreview) {
        return;
      }
      // The batch size is not part of the preview key and may have changed since.
      if (this.inputError) {
        this.attempted = true;
        return;
      }
      this.creating = true;
      this.error = null;
      try {
        const {
          idjob
        } = await createJob(this.idSitesParam, this.segment.trim(), this.dates.from, this.dates.to, this.batchSize.trim());
        Jobs_store.watch(idjob);
        Jobs_store.run();
        // Show the job right away. Errors are ignored: the loop's next response shows it anyway.
        getJob(idjob).then(job => Jobs_store.show(job)).catch(() => undefined);
        this.preview = null;
        external_CoreHome_["NotificationsStore"].show({
          message: external_CoreHome_["Matomo"].helper.htmlEntities(Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_JobCreated', idjob)),
          context: 'success',
          id: 'fwdvf-created',
          type: 'transient'
        });
      } catch (e) {
        this.error = errorMessage(e);
      } finally {
        this.creating = false;
      }
    }
  }
}));
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/DeleteTab.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Delete/DeleteTab.vue



DeleteTabvue_type_script_lang_ts.render = DeleteTabvue_type_template_id_7096274e_render

/* harmony default export */ var DeleteTab = (DeleteTabvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Manage/FeedbackBar.vue?vue&type=template&id=a98ed3cc

const FeedbackBarvue_type_template_id_a98ed3cc_hoisted_1 = ["title"];
const FeedbackBarvue_type_template_id_a98ed3cc_hoisted_2 = ["title", "aria-label", "aria-pressed", "disabled", "onClick"];
const FeedbackBarvue_type_template_id_a98ed3cc_hoisted_3 = ["d"];
const FeedbackBarvue_type_template_id_a98ed3cc_hoisted_4 = {
  class: "fwdvf-feedback__thanks",
  role: "status"
};
const FeedbackBarvue_type_template_id_a98ed3cc_hoisted_5 = ["href", "title"];
const FeedbackBarvue_type_template_id_a98ed3cc_hoisted_6 = {
  viewBox: "0 0 24 24",
  width: "14",
  height: "14",
  "aria-hidden": "true",
  focusable: "false"
};
const FeedbackBarvue_type_template_id_a98ed3cc_hoisted_7 = /*#__PURE__*/Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("circle", {
  cx: "12",
  cy: "12",
  r: "9"
}, null, -1);
const FeedbackBarvue_type_template_id_a98ed3cc_hoisted_8 = ["d"];
function FeedbackBarvue_type_template_id_a98ed3cc_render(_ctx, _cache, $props, $setup, $data, $options) {
  return _ctx.feedback.enabled || _ctx.feedback.supportUrl ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", {
    key: 0,
    class: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeClass"])(["fwdvf-feedback", {
      'is-voted': _ctx.vote !== ''
    }])
  }, [_ctx.feedback.enabled ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], {
    key: 0
  }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
    class: "fwdvf-feedback__question",
    title: _ctx.question
  }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.question), 9, FeedbackBarvue_type_template_id_a98ed3cc_hoisted_1), (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.choices, choice => {
    return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("button", {
      key: choice,
      type: "button",
      class: "fwdvf-feedback__thumb",
      title: _ctx.label(choice),
      "aria-label": _ctx.label(choice),
      "aria-pressed": _ctx.vote === choice ? 'true' : 'false',
      disabled: _ctx.vote !== '',
      onClick: $event => _ctx.send(choice)
    }, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("svg", {
      viewBox: "0 0 24 24",
      width: "14",
      height: "14",
      "aria-hidden": "true",
      focusable: "false",
      class: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeClass"])(`fwdvf-feedback__icon--${choice}`)
    }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("path", {
      d: _ctx.thumbPath
    }, null, 8, FeedbackBarvue_type_template_id_a98ed3cc_hoisted_3)], 2))], 8, FeedbackBarvue_type_template_id_a98ed3cc_hoisted_2);
  }), 128)), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", FeedbackBarvue_type_template_id_a98ed3cc_hoisted_4, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.thanks), 1)], 64)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), _ctx.feedback.supportUrl ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("a", {
    key: 1,
    class: "fwdvf-feedback__support",
    href: _ctx.feedback.supportUrl,
    target: "_blank",
    rel: "noopener noreferrer",
    title: _ctx.tr('SupportTitle')
  }, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("svg", FeedbackBarvue_type_template_id_a98ed3cc_hoisted_6, [FeedbackBarvue_type_template_id_a98ed3cc_hoisted_7, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("path", {
    d: _ctx.helpPath
  }, null, 8, FeedbackBarvue_type_template_id_a98ed3cc_hoisted_8)])), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.tr('Support')), 1)], 8, FeedbackBarvue_type_template_id_a98ed3cc_hoisted_5)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)], 2)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true);
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Manage/FeedbackBar.vue?vue&type=template&id=a98ed3cc

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Manage/FeedbackBar.vue?vue&type=script&lang=ts


const PLUGIN = 'FeinwerkDeleteVisitsFilter';
/** a thumb outline (24 x 24); the down thumb is the same, rotated */
const THUMB_PATH = 'M3 10h4v10H3zM7 10l4-7c1.3 0 2.2 1.1 2 2.4L12.4 9H19a2 2 0 0 1 2 2.3l-1.2 7A2 2 0 0 1 17.8 20H7';
// question mark of the help icon in the support link (footer variant A)
const HELP_PATH = 'M9.6 9.3a2.5 2.5 0 0 1 4.8.9c0 1.7-2.4 2.2-2.4 3.6M12 17h.01';
/** localStorage: this browser's vote per question and plugin version (a convenience only) */
function storageKey(topic) {
  return `${PLUGIN}.feedback.${topic}`;
}
function storedVote(topic, version) {
  try {
    const stored = JSON.parse(window.localStorage.getItem(storageKey(topic)) || '{}');
    return stored.version === version && (stored.vote === 'up' || stored.vote === 'down') ? stored.vote : '';
  } catch (e) {
    return '';
  }
}
/* harmony default export */ var FeedbackBarvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    /** the store "topic": one of the plugin's PluginFeedback::TOPICS */
    topic: {
      type: String,
      required: true
    },
    /** the question, already translated */
    question: {
      type: String,
      required: true
    },
    feedback: {
      type: Object,
      default: () => ({
        enabled: false,
        supportUrl: '',
        version: ''
      })
    }
  },
  data() {
    return {
      vote: storedVote(this.topic, this.feedback.version)
    };
  },
  computed: {
    choices() {
      return ['up', 'down'];
    },
    thumbPath() {
      return THUMB_PATH;
    },
    helpPath() {
      return HELP_PATH;
    },
    thanks() {
      if (!this.vote) {
        return '';
      }
      return this.vote === 'up' ? this.tr('FeedbackThanks') : this.tr('FeedbackThanksDown');
    }
  },
  methods: {
    tr(key) {
      return Object(external_CoreHome_["translate"])(`${PLUGIN}_${key}`);
    },
    label(choice) {
      return choice === 'up' ? this.tr('FeedbackYes') : this.tr('FeedbackNo');
    },
    send(vote) {
      this.vote = vote;
      try {
        const stored = {
          version: this.feedback.version,
          vote
        };
        window.localStorage.setItem(storageKey(this.topic), JSON.stringify(stored));
      } catch (e) {
        // private mode or blocked storage: the thanks is shown anyway
      }
      // Best effort: the vote is a hint for Feinwerk, a failed request needs no error message.
      external_CoreHome_["AjaxHelper"].post({
        method: `${PLUGIN}.sendFeedback`
      }, {
        topic: this.topic,
        vote
      }, {
        createErrorNotification: false,
        abortable: false
      }).catch(() => undefined);
    }
  }
}));
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Manage/FeedbackBar.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Manage/FeedbackBar.vue



FeedbackBarvue_type_script_lang_ts.render = FeedbackBarvue_type_template_id_a98ed3cc_render

/* harmony default export */ var FeedbackBar = (FeedbackBarvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/History/HistoryTab.vue?vue&type=template&id=b2db0cd0

const HistoryTabvue_type_template_id_b2db0cd0_hoisted_1 = {
  class: "fwdvf-history"
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_2 = {
  class: "fwdvf-history-filters"
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_3 = {
  key: 1
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_4 = {
  key: 2,
  class: "fwdvf-table-scroll"
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_5 = {
  class: "entityTable dataTable fwdvf-table"
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_6 = {
  class: "fwdvf-num"
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_7 = {
  class: "fwdvf-nowrap"
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_8 = {
  class: "fwdvf-sites-cell"
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_9 = ["title"];
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_10 = {
  class: "fwdvf-range-cell"
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_11 = {
  class: "fwdvf-num"
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_12 = {
  class: "fwdvf-nowrap"
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_13 = {
  class: "fwdvf-actions"
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_14 = ["onClick"];
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_15 = ["onClick"];
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_16 = ["onClick"];
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_17 = {
  key: 0,
  class: "fwdvf-details"
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_18 = {
  colspan: "9"
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_19 = {
  key: 0
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_20 = {
  class: "fwdvf-code"
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_21 = {
  key: 3,
  class: "fwdvf-error"
};
const HistoryTabvue_type_template_id_b2db0cd0_hoisted_22 = {
  key: 3,
  class: "fwdvf-paging"
};
const _hoisted_23 = ["disabled"];
const _hoisted_24 = ["disabled"];
const _hoisted_25 = {
  class: "ui-confirm"
};
const _hoisted_26 = ["value"];
const _hoisted_27 = ["value"];
function HistoryTabvue_type_template_id_b2db0cd0_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_Field = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Field");
  const _component_ActivityIndicator = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ActivityIndicator");
  const _component_Alert = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Alert");
  const _component_ContentBlock = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ContentBlock");
  const _component_MatomoDialog = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("MatomoDialog");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", HistoryTabvue_type_template_id_b2db0cd0_hoisted_1, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_ContentBlock, {
    "content-title": _ctx.translate('FeinwerkDeleteVisitsFilter_HistoryTitle')
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", HistoryTabvue_type_template_id_b2db0cd0_hoisted_2, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "select",
      name: "fwdvf-filter-status",
      title: _ctx.translate('FeinwerkDeleteVisitsFilter_FilterStatus'),
      "model-value": _ctx.status,
      options: _ctx.statusOptions,
      "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => {
        _ctx.status = $event;
        _ctx.reload(0);
      })
    }, null, 8, ["title", "model-value", "options"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Field, {
      uicontrol: "select",
      name: "fwdvf-filter-source",
      title: _ctx.translate('FeinwerkDeleteVisitsFilter_FilterSource'),
      "model-value": _ctx.source,
      options: _ctx.sourceOptions,
      "onUpdate:modelValue": _cache[1] || (_cache[1] = $event => {
        _ctx.source = $event;
        _ctx.reload(0);
      })
    }, null, 8, ["title", "model-value", "options"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
      type: "button",
      class: "btn-flat",
      onClick: _cache[2] || (_cache[2] = $event => _ctx.reload(_ctx.offset))
    }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_Refresh')), 1)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_ActivityIndicator, {
      loading: _ctx.loading
    }, null, 8, ["loading"]), _ctx.error ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_Alert, {
      key: 0,
      severity: "danger"
    }, {
      default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.error), 1)]),
      _: 1
    })) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), !_ctx.loading && !_ctx.error && _ctx.jobs.length === 0 ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", HistoryTabvue_type_template_id_b2db0cd0_hoisted_3, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_HistoryEmpty')), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), _ctx.jobs.length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", HistoryTabvue_type_template_id_b2db0cd0_hoisted_4, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("table", HistoryTabvue_type_template_id_b2db0cd0_hoisted_5, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("thead", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tr", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ColumnCreated')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ColumnBy')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ColumnWebsites')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ColumnSegment')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ColumnDateRange')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ColumnStatus')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", HistoryTabvue_type_template_id_b2db0cd0_hoisted_6, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ColumnDeleted')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ColumnDuration')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("th", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ColumnActions')), 1)])]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tbody", null, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.jobs, job => {
      return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], {
        key: job.idjob
      }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("tr", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", HistoryTabvue_type_template_id_b2db0cd0_hoisted_7, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.formatUtc(job.created_at)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.byText(job)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", HistoryTabvue_type_template_id_b2db0cd0_hoisted_8, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.sitesText(job.idsites, _ctx.siteNames, _ctx.allIds)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("code", {
        class: "fwdvf-code fwdvf-segment-cell",
        title: job.segment
      }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(job.segment), 9, HistoryTabvue_type_template_id_b2db0cd0_hoisted_9)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", HistoryTabvue_type_template_id_b2db0cd0_hoisted_10, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.dateRangeText(job.date_from, job.date_to)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", null, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
        class: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeClass"])(`fwdvf-status fwdvf-status-${job.status}`)
      }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.statusLabel(job.status)), 3)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", HistoryTabvue_type_template_id_b2db0cd0_hoisted_11, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.formatCount(job.deleted_visits)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", HistoryTabvue_type_template_id_b2db0cd0_hoisted_12, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.durationText(job)), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", HistoryTabvue_type_template_id_b2db0cd0_hoisted_13, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("a", {
        href: "",
        onClick: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withModifiers"])($event => _ctx.toggle(job.idjob), ["prevent"])
      }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_Details')), 9, HistoryTabvue_type_template_id_b2db0cd0_hoisted_14), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("a", {
        href: "",
        onClick: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withModifiers"])($event => _ctx.useTemplate(job), ["prevent"])
      }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_UseAsTemplate')), 9, HistoryTabvue_type_template_id_b2db0cd0_hoisted_15), job.status === 'failed' && _ctx.retrying.indexOf(job.idjob) === -1 ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("a", {
        key: 0,
        href: "",
        onClick: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withModifiers"])($event => _ctx.askRetry(job), ["prevent"])
      }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_RetryJob')), 9, HistoryTabvue_type_template_id_b2db0cd0_hoisted_16)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)])]), _ctx.expanded.indexOf(job.idjob) !== -1 ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("tr", HistoryTabvue_type_template_id_b2db0cd0_hoisted_17, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("td", HistoryTabvue_type_template_id_b2db0cd0_hoisted_18, [job.segment ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", HistoryTabvue_type_template_id_b2db0cd0_hoisted_19, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("strong", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ColumnSegment')) + ":", 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("code", HistoryTabvue_type_template_id_b2db0cd0_hoisted_20, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(job.segment), 1)])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_DetailsSteps', job.batches, job.invalidated_dates, job.batch_size)), 1), _ctx.rowsOf(job).length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], {
        key: 1
      }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("strong", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_DeletedRows')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("ul", null, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.rowsOf(job), row => {
        return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("li", {
          key: row.table
        }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(row.table) + ": " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.formatCount(row.count)), 1);
      }), 128))])], 64)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), job.messages.length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], {
        key: 2
      }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("strong", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_DetailsMessages')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("ul", null, [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(job.messages, (message, index) => {
        return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("li", {
          key: index
        }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.messageText(message)), 1);
      }), 128))])], 64)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), job.error ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", HistoryTabvue_type_template_id_b2db0cd0_hoisted_21, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("strong", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_DetailsError')) + ":", 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(" " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(job.error), 1)])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)])])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)], 64);
    }), 128))])])])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), _ctx.offset > 0 || _ctx.hasMore ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", HistoryTabvue_type_template_id_b2db0cd0_hoisted_22, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
      type: "button",
      class: "btn-flat",
      disabled: _ctx.offset === 0,
      onClick: _cache[3] || (_cache[3] = $event => _ctx.reload(_ctx.offset - _ctx.limit))
    }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_Previous')), 9, _hoisted_23), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
      type: "button",
      class: "btn-flat",
      disabled: !_ctx.hasMore,
      onClick: _cache[4] || (_cache[4] = $event => _ctx.reload(_ctx.offset + _ctx.limit))
    }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_Next')), 9, _hoisted_24)])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true)]),
    _: 1
  }, 8, ["content-title"]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_MatomoDialog, {
    modelValue: _ctx.retryOpen,
    "onUpdate:modelValue": _cache[5] || (_cache[5] = $event => _ctx.retryOpen = $event),
    options: {
      focusSelector: '.modal.open .modal-footer a:first-child'
    },
    onYes: _cache[6] || (_cache[6] = $event => _ctx.retry())
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", _hoisted_25, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h2", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_RetryJobConfirm', _ctx.retryTarget ? _ctx.retryTarget.idjob : '', _ctx.retryTarget ? _ctx.formatCount(_ctx.retryTarget.deleted_visits) : '')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
      type: "button",
      role: "no",
      value: _ctx.translate('FeinwerkDeleteVisitsFilter_Cancel')
    }, null, 8, _hoisted_26), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
      type: "button",
      role: "yes",
      value: _ctx.translate('FeinwerkDeleteVisitsFilter_RetryJobConfirmYes')
    }, null, 8, _hoisted_27)])]),
    _: 1
  }, 8, ["modelValue", "options"])]);
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/History/HistoryTab.vue?vue&type=template&id=b2db0cd0

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/History/HistoryTab.vue?vue&type=script&lang=ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */









const STATUSES = ['queued', 'running', 'held', 'done', 'cancelled', 'failed'];
/* harmony default export */ var HistoryTabvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    siteNames: {
      type: Object,
      required: true
    }
  },
  emits: ['use-template'],
  components: {
    ActivityIndicator: external_CoreHome_["ActivityIndicator"],
    Alert: external_CoreHome_["Alert"],
    ContentBlock: external_CoreHome_["ContentBlock"],
    Field: external_CorePluginsAdmin_["Field"],
    MatomoDialog: external_CoreHome_["MatomoDialog"]
  },
  data() {
    return {
      jobs: [],
      offset: 0,
      hasMore: false,
      status: '',
      source: '',
      loading: false,
      error: null,
      expanded: [],
      request: 0,
      pendingOffset: 0,
      retrying: [],
      retryOpen: false,
      retryTarget: null
    };
  },
  computed: {
    limit() {
      return 20;
    },
    allIds() {
      return allSiteIds(this.siteNames);
    },
    statusOptions() {
      return [{
        key: '',
        value: Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_FilterAll')
      }].concat(STATUSES.map(status => ({
        key: status,
        value: statusLabel(status)
      })));
    },
    sourceOptions() {
      return [{
        key: '',
        value: Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_FilterAll')
      }, {
        key: 'ui',
        value: sourceLabel('ui')
      }, {
        key: 'console',
        value: sourceLabel('console')
      }, {
        key: 'api',
        value: sourceLabel('api')
      }];
    },
    /** Changes when a deletion starts or finishes, so the list follows the running jobs. */
    queueVersion() {
      const active = Jobs_store.state.active.map(job => `${job.idjob}:${job.status}`).join(',');
      return `${Jobs_store.state.finishedCount}|${active}`;
    }
  },
  watch: {
    queueVersion() {
      this.reload(this.pendingOffset);
    }
  },
  created() {
    this.reload(0);
  },
  methods: {
    formatCount: formatCount,
    formatUtc: formatUtc,
    dateRangeText: dateRangeText,
    messageText: messageText,
    sitesText: sitesText,
    statusLabel: statusLabel,
    async reload(offset) {
      this.request += 1;
      this.pendingOffset = Math.max(0, offset);
      const {
        request
      } = this;
      this.loading = true;
      this.error = null;
      try {
        // One more than shown tells whether there is a next page.
        const rows = await getJobs(Math.max(0, offset), this.limit + 1, this.status, this.source);
        if (request !== this.request) {
          return;
        }
        this.offset = Math.max(0, offset);
        this.hasMore = rows.length > this.limit;
        this.jobs = rows.slice(0, this.limit);
      } catch (e) {
        if (request === this.request) {
          this.error = errorMessage(e);
        }
      } finally {
        if (request === this.request) {
          this.loading = false;
        }
      }
    },
    byText(job) {
      const source = sourceLabel(job.source);
      if (!job.created_by) {
        return source;
      }
      return job.source === 'ui' ? job.created_by : `${source} (${job.created_by})`;
    },
    durationText(job) {
      const seconds = durationSeconds(job.started_at, job.finished_at, Date.now());
      return seconds === null ? '-' : formatDuration(seconds);
    },
    rowsOf(job) {
      const rows = normalizeRows(job.deleted_rows);
      return Object.keys(rows).sort().map(table => ({
        table,
        count: rows[table]
      }));
    },
    toggle(idJob) {
      this.expanded = this.expanded.indexOf(idJob) === -1 ? this.expanded.concat([idJob]) : this.expanded.filter(id => id !== idJob);
    },
    useTemplate(job) {
      const template = {
        idJob: job.idjob,
        idSites: job.idsites.slice(),
        segment: job.segment,
        dateFrom: job.date_from,
        dateTo: job.date_to
      };
      this.$emit('use-template', template);
    },
    askRetry(job) {
      if (this.retrying.indexOf(job.idjob) !== -1) {
        return;
      }
      this.retryTarget = job;
      this.retryOpen = true;
    },
    async retry() {
      const job = this.retryTarget;
      if (job === null || this.retrying.indexOf(job.idjob) !== -1) {
        return;
      }
      this.retrying = this.retrying.concat([job.idjob]);
      this.error = null;
      try {
        const retried = await retryJob(job.idjob);
        Jobs_store.watch(job.idjob);
        Jobs_store.show(retried);
        Jobs_store.run();
        await this.reload(this.pendingOffset);
      } catch (e) {
        this.error = errorMessage(e);
      } finally {
        this.retrying = this.retrying.filter(id => id !== job.idjob);
      }
    }
  }
}));
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/History/HistoryTab.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/History/HistoryTab.vue



HistoryTabvue_type_script_lang_ts.render = HistoryTabvue_type_template_id_b2db0cd0_render

/* harmony default export */ var HistoryTab = (HistoryTabvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-babel/node_modules/cache-loader/dist/cjs.js??ref--13-0!./node_modules/@vue/cli-plugin-babel/node_modules/thread-loader/dist/cjs.js!./node_modules/babel-loader/lib!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist/templateLoader.js??ref--6!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Running/RunningJobs.vue?vue&type=template&id=cb8626c8

const RunningJobsvue_type_template_id_cb8626c8_hoisted_1 = {
  class: "fwdvf-running"
};
const RunningJobsvue_type_template_id_cb8626c8_hoisted_2 = {
  class: "fwdvf-running-heading"
};
const RunningJobsvue_type_template_id_cb8626c8_hoisted_3 = {
  class: "fwdvf-muted"
};
const RunningJobsvue_type_template_id_cb8626c8_hoisted_4 = {
  key: 0,
  class: "fwdvf-muted"
};
const RunningJobsvue_type_template_id_cb8626c8_hoisted_5 = ["title"];
const RunningJobsvue_type_template_id_cb8626c8_hoisted_6 = {
  key: 1,
  class: "fwdvf-muted"
};
const RunningJobsvue_type_template_id_cb8626c8_hoisted_7 = ["disabled", "onClick"];
const RunningJobsvue_type_template_id_cb8626c8_hoisted_8 = {
  key: 1,
  class: "fwdvf-hint"
};
const RunningJobsvue_type_template_id_cb8626c8_hoisted_9 = {
  class: "fwdvf-hint"
};
const RunningJobsvue_type_template_id_cb8626c8_hoisted_10 = {
  class: "ui-confirm"
};
const RunningJobsvue_type_template_id_cb8626c8_hoisted_11 = ["value"];
const RunningJobsvue_type_template_id_cb8626c8_hoisted_12 = ["value"];
function RunningJobsvue_type_template_id_cb8626c8_render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_Progressbar = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Progressbar");
  const _component_Alert = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("Alert");
  const _component_ContentBlock = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("ContentBlock");
  const _component_MatomoDialog = Object(external_commonjs_vue_commonjs2_vue_root_Vue_["resolveComponent"])("MatomoDialog");
  return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", RunningJobsvue_type_template_id_cb8626c8_hoisted_1, [_ctx.jobs.length ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_ContentBlock, {
    key: 0,
    "content-title": _ctx.translate('FeinwerkDeleteVisitsFilter_RunningTitle')
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])(external_commonjs_vue_commonjs2_vue_root_Vue_["Fragment"], null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["renderList"])(_ctx.jobs, job => {
      return Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("div", {
        key: job.idjob,
        class: "fwdvf-running-job"
      }, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h3", RunningJobsvue_type_template_id_cb8626c8_hoisted_2, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_RunningJob', job.idjob)) + " ", 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("span", {
        class: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["normalizeClass"])(`fwdvf-status fwdvf-status-${job.status}`)
      }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.statusLabel(job.status)), 3)]), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", RunningJobsvue_type_template_id_cb8626c8_hoisted_3, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.sitesText(job.idsites, _ctx.siteNames, _ctx.allIds)) + " · " + Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.dateRangeText(job.date_from, job.date_to)), 1), job.segment ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", RunningJobsvue_type_template_id_cb8626c8_hoisted_4, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("code", {
        class: "fwdvf-code fwdvf-segment-cell",
        title: job.segment
      }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(job.segment), 9, RunningJobsvue_type_template_id_cb8626c8_hoisted_5)])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_Progressbar, {
        progress: _ctx.progressOf(job),
        label: _ctx.labelOf(job)
      }, null, 8, ["progress", "label"]), _ctx.detailsOf(job) ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", RunningJobsvue_type_template_id_cb8626c8_hoisted_6, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.detailsOf(job)), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), job.status === 'held' ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_Alert, {
        key: 2,
        severity: "warning"
      }, {
        default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_HeldHint')), 1)]),
        _: 1
      })) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("button", {
        type: "button",
        class: "btn-flat fwdvf-cancel",
        disabled: _ctx.cancelling.indexOf(job.idjob) !== -1,
        onClick: $event => _ctx.askCancel(job.idjob)
      }, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_CancelJob')), 9, RunningJobsvue_type_template_id_cb8626c8_hoisted_7)]);
    }), 128)), _ctx.state.error ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createBlock"])(_component_Alert, {
      key: 0,
      severity: "danger"
    }, {
      default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createTextVNode"])(Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_ConnectionError', _ctx.state.error)), 1)]),
      _: 1
    })) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), _ctx.state.busy ? (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["openBlock"])(), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementBlock"])("p", RunningJobsvue_type_template_id_cb8626c8_hoisted_8, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_BusyHint')), 1)) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("p", RunningJobsvue_type_template_id_cb8626c8_hoisted_9, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.backgroundHint), 1)]),
    _: 1
  }, 8, ["content-title"])) : Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createCommentVNode"])("", true), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createVNode"])(_component_MatomoDialog, {
    modelValue: _ctx.confirmOpen,
    "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => _ctx.confirmOpen = $event),
    onYes: _cache[1] || (_cache[1] = $event => _ctx.cancel())
  }, {
    default: Object(external_commonjs_vue_commonjs2_vue_root_Vue_["withCtx"])(() => [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("div", RunningJobsvue_type_template_id_cb8626c8_hoisted_10, [Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("h2", null, Object(external_commonjs_vue_commonjs2_vue_root_Vue_["toDisplayString"])(_ctx.translate('FeinwerkDeleteVisitsFilter_CancelJobConfirm')), 1), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
      type: "button",
      role: "yes",
      value: _ctx.translate('FeinwerkDeleteVisitsFilter_ConfirmCancelYes')
    }, null, 8, RunningJobsvue_type_template_id_cb8626c8_hoisted_11), Object(external_commonjs_vue_commonjs2_vue_root_Vue_["createElementVNode"])("input", {
      type: "button",
      role: "no",
      value: _ctx.translate('FeinwerkDeleteVisitsFilter_ConfirmCancelNo')
    }, null, 8, RunningJobsvue_type_template_id_cb8626c8_hoisted_12)])]),
    _: 1
  }, 8, ["modelValue"])]);
}
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Running/RunningJobs.vue?vue&type=template&id=cb8626c8

// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Running/RunningJobs.vue?vue&type=script&lang=ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */







/* harmony default export */ var RunningJobsvue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    siteNames: {
      type: Object,
      required: true
    },
    backgroundBudget: {
      type: Number,
      required: true
    }
  },
  components: {
    Alert: external_CoreHome_["Alert"],
    ContentBlock: external_CoreHome_["ContentBlock"],
    MatomoDialog: external_CoreHome_["MatomoDialog"],
    Progressbar: external_CoreHome_["Progressbar"]
  },
  data() {
    return {
      confirmOpen: false,
      cancelTarget: null,
      cancelling: []
    };
  },
  computed: {
    state() {
      return Jobs_store.state;
    },
    jobs() {
      return Jobs_store.state.active;
    },
    allIds() {
      return allSiteIds(this.siteNames);
    },
    backgroundHint() {
      return this.backgroundBudget > 0 ? Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_BackgroundHint') : Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_BackgroundOffHint', './console feinwerkdeletevisitsfilter:work');
    }
  },
  methods: {
    statusLabel: statusLabel,
    sitesText: sitesText,
    dateRangeText: dateRangeText,
    progressOf(job) {
      return percent(job.deleted_visits, job.estimated_visits);
    },
    labelOf(job) {
      if (job.status === 'queued') {
        return Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_StatusQueued');
      }
      if (job.estimated_visits === null) {
        return job.deleted_visits > 0 ? Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_ProgressLabelNoEstimate', formatCount(job.deleted_visits)) : Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_CountingVisits');
      }
      return Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_ProgressLabel', formatCount(job.deleted_visits), formatCount(job.estimated_visits));
    },
    detailsOf(job) {
      const parts = [];
      if (job.sites_total > 1) {
        var _this$siteNames$idSit;
        const index = Math.min(job.site_index, job.sites_total - 1);
        const idSite = job.idsites[index];
        parts.push(Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_ProgressSiteName', index + 1, job.sites_total, (_this$siteNames$idSit = this.siteNames[idSite]) !== null && _this$siteNames$idSit !== void 0 ? _this$siteNames$idSit : `#${idSite}`));
      }
      const perSecond = rate(Jobs_store.state.samples[job.idjob] || []);
      if (perSecond !== null) {
        parts.push(Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_ProgressRate', formatRate(perSecond)));
        const eta = etaSeconds(job.deleted_visits, job.estimated_visits, perSecond);
        if (eta !== null) {
          parts.push(Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_ProgressRemaining', formatDuration(eta)));
        }
      }
      return parts.join(' · ');
    },
    askCancel(idJob) {
      this.cancelTarget = idJob;
      this.confirmOpen = true;
    },
    async cancel() {
      const idJob = this.cancelTarget;
      if (idJob === null) {
        return;
      }
      this.cancelling.push(idJob);
      try {
        await Jobs_store.cancel(idJob);
      } catch (e) {
        external_CoreHome_["NotificationsStore"].show({
          message: external_CoreHome_["Matomo"].helper.htmlEntities(errorMessage(e)),
          context: 'error',
          id: 'fwdvf-cancel',
          type: 'transient'
        });
      } finally {
        this.cancelling = this.cancelling.filter(id => id !== idJob);
      }
    }
  }
}));
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Running/RunningJobs.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Running/RunningJobs.vue



RunningJobsvue_type_script_lang_ts.render = RunningJobsvue_type_template_id_cb8626c8_render

/* harmony default export */ var RunningJobs = (RunningJobsvue_type_script_lang_ts);
// CONCATENATED MODULE: ./node_modules/@vue/cli-plugin-typescript/node_modules/cache-loader/dist/cjs.js??ref--15-0!./node_modules/babel-loader/lib!./node_modules/@vue/cli-plugin-typescript/node_modules/ts-loader??ref--15-2!./node_modules/@vue/cli-service/node_modules/cache-loader/dist/cjs.js??ref--1-0!./node_modules/@vue/cli-service/node_modules/vue-loader-v16/dist??ref--1-1!./plugins/FeinwerkDeleteVisitsFilter/vue/src/Manage/Manage.vue?vue&type=script&lang=ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */







/* harmony default export */ var Managevue_type_script_lang_ts = (Object(external_commonjs_vue_commonjs2_vue_root_Vue_["defineComponent"])({
  props: {
    initialSiteId: {
      type: Number,
      required: true
    },
    siteNames: {
      type: Object,
      required: true
    },
    activeJobs: {
      type: Array,
      required: true
    },
    confirmThreshold: {
      type: Number,
      required: true
    },
    backgroundBudget: {
      type: Number,
      required: true
    },
    defaultBatchSize: {
      type: Number,
      required: true
    },
    feedback: {
      type: Object,
      default: () => ({
        enabled: false,
        supportUrl: '',
        version: ''
      })
    }
  },
  components: {
    Alert: external_CoreHome_["Alert"],
    ContentBlock: external_CoreHome_["ContentBlock"],
    DeleteTab: DeleteTab,
    FeedbackBar: FeedbackBar,
    HistoryTab: HistoryTab,
    RunningJobs: RunningJobs
  },
  data() {
    return {
      template: null
    };
  },
  computed: {
    tab() {
      return external_CoreHome_["MatomoUrl"].hashParsed.value.tab === 'history' ? 'history' : 'delete';
    },
    tabs() {
      return [{
        id: 'delete',
        label: Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_TabDelete')
      }, {
        id: 'history',
        label: Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_TabHistory')
      }];
    }
  },
  created() {
    // After a reload during a deletion the page continues where it was.
    Jobs_store.setActive(this.activeJobs);
    if (Jobs_store.state.active.length > 0) {
      Jobs_store.run();
    }
  },
  methods: {
    setTab(tab) {
      external_CoreHome_["MatomoUrl"].updateHash(Object.assign(Object.assign({}, external_CoreHome_["MatomoUrl"].hashParsed.value), {}, {
        tab
      }));
    },
    useTemplate(template) {
      this.template = template;
      this.setTab('delete');
      external_CoreHome_["NotificationsStore"].show({
        message: external_CoreHome_["Matomo"].helper.htmlEntities(Object(external_CoreHome_["translate"])('FeinwerkDeleteVisitsFilter_TemplateApplied', template.idJob)),
        context: 'info',
        id: 'fwdvf-template',
        type: 'transient'
      });
    }
  }
}));
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Manage/Manage.vue?vue&type=script&lang=ts
 
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/Manage/Manage.vue



Managevue_type_script_lang_ts.render = render

/* harmony default export */ var Manage = (Managevue_type_script_lang_ts);
// CONCATENATED MODULE: ./plugins/FeinwerkDeleteVisitsFilter/vue/src/index.ts
/*!
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

// CONCATENATED MODULE: ./node_modules/@vue/cli-service/lib/commands/build/entry-lib-no-default.js




/***/ })

/******/ });
});
//# sourceMappingURL=FeinwerkDeleteVisitsFilter.umd.js.map