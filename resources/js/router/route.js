// import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
// import postDetail from '../components/PostDetail.vue'




export const routes = [
    {
        path: '/post',
        name: 'post',
        component: () => import('../components/Post.vue')
    },
    {
        path: '/postDetail',
        name: 'postDetail',
        component: ()=>import('../components/PostDetail.vue')
    }

]
