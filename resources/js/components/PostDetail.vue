<template>
    <div class="container my-5">
      <h1>Post Details</h1>
      <div v-if="post">
        <h2>{{ post.title }}</h2>
        <p>{{ post.description }}</p>
        <div v-if="post.image">
          <img :src="`/storage/${post.image}`" alt="Post Image" width="300" />
        </div>
      </div>
      <div v-else>
        <p>Loading post details...</p>
      </div>
    </div>
  </template>

  <script>
  import axios from 'axios';

  export default {
    name: 'PostDetail',
    data() {
      return {
        post: null,
        loadingStatus: false,
      };
    },
    async created() {
      await this.fetchPost();
    },
    methods: {
      async fetchPost() {
        const postId = this.$route.params.postId; // Access postId from route params
        this.loadingStatus = true;
        try {
          const response = await axios.get(`http://127.0.0.1:8000/api/posts/${postId}`);
          this.post = response.data.post; // Set the fetched post data
        } catch (error) {
          console.error('Error fetching post details:', error.response.data);
        } finally {
          this.loadingStatus = false;
        }
      },
    },
    watch: {
      // Watch for changes in the route parameter
      '$route.params.postId': function () {
        this.fetchPost();
      },
    },
  };
  </script>
