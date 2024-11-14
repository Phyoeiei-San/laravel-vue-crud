
// import "./bootstrap";
import { createApp } from "vue";
import "bootstrap/dist/css/bootstrap.min.css";
import App from "./App.vue";
import { createRouter, createWebHistory } from 'vue-router';
import { routes } from './router/route';

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
})


createApp(App).use(router).mount("#app");