<template>
    <div class="container my-5">
      <!-- Form Section -->
      <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">Create New Post</h5>
        </div>
        <div class="card-body">
          <form  >
            <div class="mb-3 row">
              <label for="title" class="col-sm-2 col-form-label fw-bold">Title</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="title" placeholder="Enter Title" v-model="title">
                <small class="text-danger" v-if="validation.titleStatus">Title Is Required</small>
              </div>
            </div>
            <div class="mb-3 row">
              <label for="description" class="col-sm-2 col-form-label fw-bold">Description</label>
              <div class="col-sm-10">
                <textarea class="form-control" id="description" rows="3" placeholder="Enter Description" v-model="description"></textarea>
                <small class="text-danger" v-if="validation.descriptionStatus">Description Is Required</small>
              </div>
            </div>
            <div class="mb-3 row">
              <label for="fileInput" class="col-sm-2 col-form-label fw-bold">Image</label>
              <div class="col-sm-10">
                <!-- <input type="file" class="form-control" id="fileInput"> -->
                <input type="file" class="form-control" id="fileInput" @change="onFileChange">
                <small class="text-danger" v-if="validation.imageStatus">Image Is Required</small>

                <div v-if="imagePreview" class="mt-3">
        <p>Current Image:</p>
        <img :src="imagePreview" width="100" height="100" alt="Current Image Preview">
      </div>

              </div>
            </div>
            <div class="text-center">
             <!-- <button type="submit" class="btn btn-primary">
                {{ editId ? 'Update' : 'Create' }}
             </button> -->
             <button type="submit" class="btn btn-primary" @click="updatePosts" v-if="editId">Update</button>
             <button type="submit" class="btn btn-primary" @click="createPost" v-else>Create</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Table Section -->
      <div class="card shadow">
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

                <img v-if="post.image" :src="`/storage/${post.image}`"  width="100" height="1000">
                <span v-else>No Image</span> <!-- Fallback if no image is uploaded -->

                </td>

                  <td>
                    <button class="btn btn-success mx-3" @click="editPost(post)">Edit</button>
                    <button class="btn btn-danger" @click="confirmDelete(post.id)">Delete</button>
                  </td>
                  </tr>
            </tbody>
          </table>
        </div>
      </div>

      <router-view></router-view>
    </div>
  </template>

  <script>
  import Post from './components/Post.vue'
  import axios from "axios";
  export default {
    name: 'App',
    components: {
      Post
    },
    data() {
      return {
        title: "",
        description:"",
        image: null,
        imagePreview: null,
        posts: [],
        editId: null ,
        validation:{
            titleStatus: false,
            descriptionStatus: false,
            imageStatus: false,
          }
      }

    },
    methods: {
      onFileChange(event) {
      this.image = event.target.files[0];
      },

  async createPost() {
    // Validate inputs
    this.validation.titleStatus = !this.title;
    this.validation.descriptionStatus = !this.description;
    this.validation.imageStatus = !this.image && !this.imagePreview;

    if (this.validation.titleStatus || this.validation.descriptionStatus || this.validation.imageStatus) {
      return;
    }

    try {
      const formData = new FormData();
      formData.append("title", this.title);
      formData.append("description", this.description);

      if (this.image) {
        formData.append("image", this.image);
      }

      await axios.post("http://127.0.0.1:8000/api/posts", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      // if (this.editId) {
      //   // If we're updating an existing post, use PUT
      //   await axios.put(`http://127.0.0.1:8000/api/posts/${this.editId}`, formData, {
      //     headers: {
      //       "Content-Type": "multipart/form-data",
      //     },
      //   });
      //   this.editId = null; // Clear editId after updating
      // } else {
        // If we're creating a new post, use POST
      // }

      // Reset form data after successful save
      this.title = '';
      this.description = '';
      this.image = null;
      this.imagePreview = null;

      // Clear validation flags
      this.validation.titleStatus = false;
      this.validation.descriptionStatus = false;
      this.validation.imageStatus = false;

      // Fetch updated posts list
      await this.fetchPosts();
    } catch (error) {
      console.error(error.response.data);
    }
  },


  async updatePosts(){
      try{

      const formData = new FormData();
      formData.append("title", this.title);
      formData.append("description", this.description);

      if (this.image) {
        formData.append("image", this.image);
      }
          await axios.put(`http://127.0.0.1:8000/api/posts/${this.editId}`, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });
          }catch (error) {
        console.error(error.response.data);
      }
      },
      async fetchPosts() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/posts');
        this.posts = response.data.posts;
      } catch (error) {
        console.error(error.response.data);
      }
    },
    editPost(post) {
        this.title = post.title;
        this.description = post.description;
        this.editId = post.id;
        console.log(this.editId);
      this.imagePreview = `/storage/${post.image}`;
    },
   async deletePost(id) {
        try {
          await axios.delete(`http://127.0.0.1:8000/api/posts/${id}`);
          await this.fetchPosts();
        } catch (error) {
          console.error(error.response.data);
        }
      },
      confirmDelete(id) {

        if (confirm("Are you sure you want to delete this post?")) {
          this.deletePost(id);
        }
      },
    },
    mounted() {
    this.fetchPosts();
  }
  }
  </script>

  <style scoped>
  .container {
    max-width: 800px;
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
