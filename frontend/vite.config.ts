import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import { viteStaticCopy } from "vite-plugin-static-copy";
export default defineConfig({
  plugins: [
    vue(),
    viteStaticCopy({
      targets: ["Workers", "ThirdParty", "Assets", "Widgets"].map((name) => ({
        src: `node_modules/cesium/Build/Cesium/${name}`,
        dest: "cesium",
      })),
    }),
  ],
  define: { CESIUM_BASE_URL: JSON.stringify("/cesium") },
  server: {
    proxy: Object.fromEntries(
      [
        "/api",
        "/admin",
        "/livewire",
        "/css/filament",
        "/js/filament",
        "/fonts/filament",
      ].map((path) => [
        path,
        {
          target: process.env.API_PROXY_TARGET || "http://127.0.0.1:8000",
          changeOrigin: false,
        },
      ]),
    ),
  },
});
