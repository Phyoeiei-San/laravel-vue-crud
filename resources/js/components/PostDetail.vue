<template>
    <div class="container my-5">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h5>{{ post.title }}</h5>
        </div>
        <div class="card-body">
          <p><strong>Description:</strong> {{ post.description }}</p>
          <div v-if="post.image">
            <img :src="`/storage/${post.image}`" width="300" height="300" alt="Post Image" />
          </div>
          <p v-else>No image available.</p>
        </div>
      </div>
    </div>
  </template>

  <script>
  import axios from 'axios';

  export default {
    name: 'PostDetail',
    props: ['postId'],  // Accept the postId as a prop from the route
    data() {
      return {
        post: null,
      };
    },
    async created() {
      this.fetchPostDetail();
    },
    methods: {
      async fetchPostDetail() {
        try {
          const response = await axios.get(`http://127.0.0.1:8000/api/posts/${this.postId}`);
          this.post = response.data;
        } catch (error) {
          console.error("Error fetching post details:", error);
        }
      }
    }
  };
  </script>

  <style scoped>
  .card-header h5 {
    margin: 0;
  }

  .card-body {
    padding: 20px;
  }

  img {
    max-width: 100%;
    height: auto;
  }
  </style>
