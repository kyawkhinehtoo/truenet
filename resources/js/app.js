import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import "@fortawesome/fontawesome-free/css/all.min.css";
import '../../node_modules/@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';
const appName = import.meta.env.VITE_APP_NAME || "Laravel";
import jQuery from "jquery";
window.$ = jQuery;
import Swal from "sweetalert2";

window.Toast = Swal.mixin({
    toast: true,
    position: "top-right",
    timer: 3500,
    timerProgressBar: true,
});
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
