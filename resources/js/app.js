
// import "./bootstrap";

import { createApp } from "vue";
import "bootstrap/dist/css/bootstrap.min.css";
import App from "./App.vue";
import { store } from './store';
import { createRouter, createWebHistory } from 'vue-router';
import { routes } from './router/route';

// import PostDetail from './components/PostDetail.vue';

// Vue.config.productionTip = false;




const router = createRouter({
    history: createWebHistory(),
    routes: routes,

})


createApp(App).use(store).use(router).mount("#app");



