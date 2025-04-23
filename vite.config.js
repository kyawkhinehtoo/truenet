import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.js",
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js"),
        },
        extensions: [".js", ".json", ".vue"],
    },
    server: {
        host: "localhost", // or "0.0.0.0" if needed
        port: 5173,
        cors: {
            origin: "http://truenet.test", // Allow your Laravel domain
        },
    },
});