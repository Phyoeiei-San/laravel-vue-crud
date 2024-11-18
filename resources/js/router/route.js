// import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import PostDetail from '../components/PostDetail.vue'
import Post from '../components/Post.vue'
export const routes = [
    {
        path: '/',
        name: 'post',
        component: Post,
    },
    {
        path: '/postDetail/:postId',  // <-- This is the change
        name: 'postDetail',
        component: PostDetail,
        // props: true, // Allows passing postId as a prop
    }
]
// const router = createRouter({
//     history: createWebHistory(process.env.BASE_URL),
//     routes
//   })

//   export default router
