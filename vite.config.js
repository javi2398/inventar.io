import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";

export default defineConfig({
    plugins: [
        laravel({
            input: "resources/js/app.jsx",
            refresh: true,
        }),
        react(),
    ],
    build: {
        outDir: "public/build",
        emptyOutDir: true,
        manifest: "manifest.json",
        rollupOptions: {
            input: {
                app: "resources/js/app.jsx",
            },
        },
    },
    server: {
        host: "localhost",
        port: 5173,
        strictPort: false,
        cors: {
            origin: "http://localhost",
            credentials: true,
        },
    },
});
