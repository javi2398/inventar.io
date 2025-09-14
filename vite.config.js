import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";
import dotenv from "dotenv";

dotenv.config();

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
        manifest: true,
        rollupOptions: {
            input: "resources/js/app.jsx",
        },
    },
    server: {
        host: process.env.VITE_HOST || "localhost",
        port: process.env.VITE_PORT || 5173,
        strictPort: false,
        cors: {
            origin: process.env.VITE_CORS_ORIGIN || "http://inventar.io.local",
            credentials: true,
        },
    },
});
