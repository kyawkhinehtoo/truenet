import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path"; // Import path for aliasing

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
            "@": path.resolve(__dirname, "resources/js"), // Set alias for '@/'
        },
        extensions: [".js", ".json", ".vue"], // Automatically resolve these extensions
    },
});
