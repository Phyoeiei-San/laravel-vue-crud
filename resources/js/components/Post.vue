<template>
    <!-- <router-view></router-view> -->
    <div class="container my-5">
      <div class="row">
        <!-- Search Section -->
        <div class="d-flex col-12 justify-content-end mb-4">
          <div class="col-3">
            <input
              type="text"
              class="form-control mb-3"
              v-model="searchKey"
              v-on:keyup.enter="searchPost"
              placeholder="Search posts..."
            />
          </div>
          <button class="btn btn-dark text-white btn-sm mx-3 mb-3" @click="searchPost">Search</button>
        </div>

        <!-- Form and Table Section (side by side) -->
        <div class="col-12 d-flex">
          <!-- Form Section (left side) -->
          <div class="card shadow mb-4 col-md-5 mr-5">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">{{ editId ? 'Update New Post' : 'Create New Post' }}</h5>
            </div>
            <div class="card-body">
            <form @submit.prevent="handleSubmit">

              <!-- <form @submit.prevent="editId ? updatePost() : createPost()"> -->
                <div class="mb-3 row">
                  <label for="title" class="col-sm-2 col-form-label fw-bold">Title</label>
                  <div class="col-sm-10">
                    <input
                      type="text"
                      class="form-control"
                      id="title"
                      placeholder="Enter Title"
                      v-model="title"
                    />
                    <!-- <small class="text-danger" v-if="validation.titleStatus">Title Is Required</small> -->
                  </div>
                </div>
                <div class="mb-3 row">
                  <label for="description" class="col-sm-2 col-form-label fw-bold">Description</label>
                  <div class="col-sm-10">
                    <textarea
                      class="form-control"
                      id="description"
                      rows="3"
                      placeholder="Enter Description"
                      v-model="description"
                    ></textarea>
                    <!-- <small class="text-danger" v-if="validation.descriptionStatus">Description Is Required</small> -->
                  </div>
                </div>
                <div class="mb-3 row">
                  <label for="fileInput" class="col-sm-2 col-form-label fw-bold">Image</label>
                  <div class="col-sm-10">
                    <input
                      type="file"
                      class="form-control"
                      id="fileInput"
                      v-if="editId"
                      @change="onFileChange"
                    />
                    <input
                      type="file"
                      class="form-control"
                      id="fileInput"
                      v-else
                      @change="fileChange"
                    />
                    <!-- <small class="text-danger" v-if="validation.imageStatus">Image Is Required</small> -->

                    <div v-if="imagePreview" class="mt-3">
                      <p>Current Image:</p>
                      <img :src="imagePreview" width="100" height="100" alt="Current Image Preview" />
                    </div>
                  </div>
                </div>
                <div class="text-center">

                  <button type="submit" class="btn btn-primary">{{ editId ? 'Update' : 'Create' }}</button>
                </div>
              </form>
            </div>
          </div>

          <!-- Table Section (right side) -->
          <div class="card shadow mb-4 col-md-7">
            <div class="card-header bg-secondary text-white">
              <h5 class="mb-0">Posts List</h5>
            </div>
            <div class="card-body p-0">
              <table class="table table-striped mb-0">
                <thead class="table-dark">
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Title</th>
                    <th scope="col">Description</th>
                    <th scope="col">Image</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="post in posts" :key="post.id">
                    <td>{{ post.id }}</td>
                    <td>{{ post.title }}</td>
                    <td>{{ post.description }}</td>
                    <td>
                      <img v-if="post.image" :src="`/storage/${post.image}`" width="100" height="100" />
                      <span v-else>No Image</span> <!-- Fallback if no image is uploaded -->
                    </td>
                    <td>
                        <button class="btn btn-success ml-3" @click="editPost(post)">Edit</button>

                      <button class="btn btn-danger ml-3" @click="confirmDelete(post.id)">Delete</button>
                        <router-link :to="{ name: 'postDetail', params: { postId: post.id } }">
                            <button class="btn btn-primary ml-3">View </button>
                        </router-link>

                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </template>
    <router-link></router-link>
  <script>

//   import axios from 'axios';
import { mapState, mapActions, mapGetters } from 'vuex';

export default {
    data() {
  return {
        editId: null,
        title: '',
        description: '',
        file: null,
        imagePreview: null,
        searchKey: '',
  };
},

  computed: {
    ...mapState(['posts']),
    ...mapGetters(['getPosts']),
  },
  methods: {
    ...mapActions(['fetchPosts', 'createPost', 'updatePost', 'deletePost', 'searchPost']),


handleSubmit() {
    // console.log('this is updating',id);
  if (this.editId) {
    const formData = {
        'id': this.editId,
        'title': this.title,
        'description': this.description,
        'image': this.file,


    }

    this.updatePost(formData).then(() => {
      this.fetchPosts();
      this.resetForm();
    });
  } else {
      const formData = new FormData();
      formData.append('title', this.title);
      formData.append('description', this.description);
      if (this.file) formData.append('image', this.file);

      this.createPost(formData).then(() => {
        this.fetchPosts();
        this.resetForm();
      });
    }
},


  resetForm() {
    this.editId = null;
    this.title = '';
    this.description = '';
    this.file = null;
    this.imagePreview = null;
  },
  editPost(post) {
  this.editId = post.id;
  this.title = post.title;
  this.description = post.description;
  this.imagePreview = post.image ? `/storage/${post.image}` : null;
},


fileChange(event) {
  const file = event.target.files[0];
  this.file = file;

  const reader = new FileReader();
  reader.onload = (e) => {
    this.imagePreview = e.target.result; // Base64 preview
  };
  reader.readAsDataURL(file);
},

onFileChange(event) {
        const file = event.target.files[0];
        if (file) {
          this.selectedFile = file;
          const reader = new FileReader();
          reader.readAsDataURL(file);

          reader.onload = () => {
            this.imagePreview = reader.result;
            this.file = reader.result;
          };

          reader.onerror = (error) => {
            console.error('Error reading file:', error);
          };
        }
      },
      confirmDelete(id) {
        if (confirm('Are you sure you want to delete this post?')) {
          this.deletePost(id);
        }
      },

      searchPost() {
      this.$store.dispatch('searchPost', this.searchKey);
    },


  },
  mounted() {
    this.fetchPosts(); // Fetch posts on mount
  },
  watch: {
    searchKey: 'searchPost',
  },
};

  </script>

  <style scoped>
  .container {
    max-width: 1200px;
  }

  .card-header h5 {
    margin: 0;
  }

  .table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.05);
  }

  .text-end {
    display: flex;
    justify-content: flex-end;
  }
  </style>
