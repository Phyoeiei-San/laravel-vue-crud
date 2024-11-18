<template>
    <div id="app">

        <div class="container">
          <router-link class="btn btn-danger" to="/">My Blog</router-link>
        </div>

      <router-view></router-view>
    </div>
  </template>

  <script>
  export default {
    name: 'App',
  };
  </script>

  <style>
  body {
    font-family: Arial, sans-serif;
  }
  .navbar {
    margin-bottom: 20px;
  }
  </style>
