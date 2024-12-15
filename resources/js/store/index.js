import { createStore } from 'vuex';


import axios from "axios";
export const store = createStore({
  state: {
    posts: [],
  },
  mutations: {
    fetchPosts(state, posts) {
      state.posts = posts;
    },


    addPost(state, post) {
      state.posts.push(post);
    },
    updatePost(state, updatedPost) {
      const index = state.posts.findIndex((post) => post.id === updatedPost.id);
      if (index !== -1) {
        state.posts.splice(index, 1, { ...updatedPost });
      }
    },
    deletePost(state, postId) {
      state.posts = state.posts.filter((post) => post.id !== postId);
    },

    searchPost(state, posts) {
        state.posts = posts;
      },
  },
  actions: {
    async fetchPosts({commit}) {
        try {
          const response = await axios.get('http://127.0.0.1:8000/api/posts');
        //   console.log(response);
          commit('fetchPosts', response.data.posts);
        } catch (error) {
          console.error('Error fetching posts:', error);
        }
      },

    async createPost({ commit }, postData) {
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/posts', postData);

        commit('addPost', response.data.post);
      } catch (error) {
        console.error(error.response.data);
      }
    },
    async updatePost({ commit }, postData) {
      try {
        const response = await axios.put(`http://127.0.0.1:8000/api/posts/${postData.id}`, postData);
        //  console.log('Response',postData);

        commit('updatePost', postData);
      } catch (error) {
        console.error(error.response.data);
      }
    },

    async deletePost({ commit }, postId) {
      try {
        await axios.delete(`http://127.0.0.1:8000/api/posts/${postId}`);
        commit('deletePost', postId);
      } catch (error) {
        console.error(error.response.data);
      }
    },

    async searchPost({ commit }, searchKey) {
        try {

          const response = await axios.post('http://127.0.0.1:8000/api/posts/search', {
            key: searchKey,
          });


          commit('searchPost', response.data.posts);
        } catch (error) {
          console.error(error.response.data);
        }
      },
  },
  getters: {
    allPosts: (state) => state.posts,

    getPosts(state) {
        return state.posts;
      },
  },
});
