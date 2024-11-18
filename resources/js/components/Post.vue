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
              v-on:keyup.enter="search"
              placeholder="Search posts..."
            />
          </div>
          <button class="btn btn-dark text-white btn-sm mx-3 mb-3" @click="search">Search</button>
        </div>

        <!-- Form and Table Section (side by side) -->
        <div class="col-12 d-flex">
          <!-- Form Section (left side) -->
          <div class="card shadow mb-4 col-md-5 mr-5">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">{{ editId ? 'Update New Post' : 'Create New Post' }}</h5>
            </div>
            <div class="card-body">
              <form @submit.prevent="editId ? updatePost() : createPost()">
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
                    <small class="text-danger" v-if="validation.titleStatus">Title Is Required</small>
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
                    <small class="text-danger" v-if="validation.descriptionStatus">Description Is Required</small>
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
                    <small class="text-danger" v-if="validation.imageStatus">Image Is Required</small>

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
                      <button class="btn btn-success ml-3 " @click="editPost(post)">Edit</button>
                      <button class="btn btn-danger ml-3" @click="confirmDelete(post.id)">Delete</button>
                      <!-- <router-link :to="{ name: 'postDetail', params: { postId: post.id } }">
                        <button class="btn btn-primary">View Post</button>
                        </router-link> -->
                        <router-link :to="{ name: 'postDetail', params: { postId: post.id } }">
                            <button class="btn btn-primary ml-3">View </button>
                        </router-link>
                        <!-- <router-link to="/post">View</router-link> -->

                        <!-- <button class="btn btn-primary ml-3" @click="postDetail(post.id)">View</button> -->

                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- <router-view></router-view> -->
      </div>
    </div>
    <!-- <router-view/> -->
  </template>
    <router-link></router-link>
  <script>

  import axios from 'axios';

  export default {
    name: 'Post',

    data() {
      return {
        title: '',
        description: '',
        image: null,
        imagePreview: null,
        posts: [],
        editId: null,
        validation: {
          titleStatus: false,
          descriptionStatus: false,
          imageStatus: false,
        },
        searchKey: '',
      };
    },
    methods: {
      fileChange(event) {
        this.image = event.target.files[0];
        if (this.image) {
          this.imagePreview = URL.createObjectURL(this.image);
        }
      },
      onFileChange(event) {
        const file = event.target.files[0];
        if (file) {
          this.selectedFile = file;
          const reader = new FileReader();
          reader.readAsDataURL(file);

          reader.onload = () => {
            this.imagePreview = reader.result;
            this.image = reader.result;
          };

          reader.onerror = (error) => {
            console.error('Error reading file:', error);
          };
        }
      },

      async createPost() {
        this.validation.titleStatus = !this.title;
        this.validation.descriptionStatus = !this.description;
        this.validation.imageStatus = !this.image && !this.imagePreview;

        if (this.validation.titleStatus || this.validation.descriptionStatus || this.validation.imageStatus) {
          return;
        }

        try {
          const formData = new FormData();
          formData.append('title', this.title);
          formData.append('description', this.description);

          if (this.image) {
            formData.append('image', this.image);
          }

          await axios.post('http://127.0.0.1:8000/api/posts', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });

          this.title = '';
          this.description = '';
          this.image = null;
          this.imagePreview = null;

          this.validation.titleStatus = false;
          this.validation.descriptionStatus = false;
          this.validation.imageStatus = false;

          await this.fetchPosts();
        } catch (error) {
          console.error(error.response.data);
        }
      },

      async updatePost() {
        if (!this.editId) return;

        try {
          const postData = {
            title: this.title,
            description: this.description,
            image: this.image ? this.image : null,
          };

          await axios.put(`http://127.0.0.1:8000/api/posts/${this.editId}`, postData, {
            headers: {
              'Content-Type': 'application/json',
            },
          });

          this.title = '';
          this.description = '';
          this.selectedFile = null;
          this.imagePreview = null;
          this.image = null;
          this.editId = null;

          this.validation.titleStatus = false;
          this.validation.descriptionStatus = false;
          this.validation.imageStatus = false;

          await this.fetchPosts();
        } catch (error) {
          console.error('Error updating post:', error);
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
        if (confirm('Are you sure you want to delete this post?')) {
          this.deletePost(id);
        }
      },
      async search() {
        const search = {
          key: this.searchKey,
        };

        try {
          const response = await axios.post('http://127.0.0.1:8000/api/posts/search', search);
          console.log(response.data);
          this.posts = response.data.posts; // Update posts with search results
        } catch (error) {
          console.error(error.response.data);
        }
      },
    //   postDetail(id){
    //     // alert(id);
    //     this.$router.push({
    //         name: 'postDetail',
    //         params: {
    //             postId: id,
    //         }
    //     })
    //   }
    // postDetail(id) {

    //     // alert("hello");
    //   this.$router.push({ name: 'postDetail', params: { postId: id } });
    // },

    postDetail(id) {
    // console.log("Navigating to post detail with ID:", id);
    // alert(id);
    this.$router.push({ name: 'postDetail', params: { postId: id } });
}


    },

    mounted() {
      this.fetchPosts();

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
