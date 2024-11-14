<template>
    <div>
        
    </div>
</template>

<script>
    export default {
        data() {
            return {
                posts: [],
            }
        },
        methods: {
            fetchData() {
                fetch('http://127.0.0.1:8000/api/post')
            .then(res=>res.json())
            .then(json=>{
              console.log(json);
             this.posts = json;
            this.loadingStatus = false;
            });
                
            }
        },
    }
</script>

<style lang="scss" scoped>

</style>