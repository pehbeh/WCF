var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __importStar = (this && this.__importStar) || (function () {
    var ownKeys = function(o) {
        ownKeys = Object.getOwnPropertyNames || function (o) {
            var ar = [];
            for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
            return ar;
        };
        return ownKeys(o);
    };
    return function (mod) {
        if (mod && mod.__esModule) return mod;
        var result = {};
        if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
        __setModuleDefault(result, mod);
        return result;
    };
})();
define(["require", "exports", "@fancyapps/ui", "WoltLabSuite/Core/Helper/PageOverlay"], function (require, exports, ui_1, PageOverlay_1) {
    "use strict";
    Object.defineProperty(exports, "__esModule", { value: true });
    exports.setup = setup;
    exports.setupLegacy = setupLegacy;
    exports.createFancybox = createFancybox;
    exports.getLocalization = getLocalization;
    const LOCALES = ["cs", "de", "en", "es", "fr", "it", "lv", "pl", "sk"];
    function setup() {
        void getDefaultConfig().then((config) => {
            ui_1.Fancybox.bind("[data-fancybox]", config);
        });
    }
    function setupLegacy() {
        void getDefaultConfig().then((config) => {
            ui_1.Fancybox.bind(".jsImageViewer", {
                ...config,
                groupAll: true,
            });
        });
    }
    async function createFancybox(userSlides) {
        return new ui_1.Fancybox(userSlides, await getDefaultConfig());
    }
    async function getDefaultConfig() {
        return {
            l10n: await getLocalization(),
            parentEl: (0, PageOverlay_1.getPageOverlayContainer)(),
            Html: {
                videoAutoplay: false,
            },
        };
    }
    async function getLocalization() {
        let locale = document.documentElement.lang;
        if (!LOCALES.includes(locale)) {
            locale = "en";
        }
        return (await new Promise((resolve_1, reject_1) => { require([`@fancyapps/ui/l10n/${locale}`], resolve_1, reject_1); }).then(__importStar))[locale];
    }
});
