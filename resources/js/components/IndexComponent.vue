<script>
    import {Dropzone} from "dropzone";

    export default {
        name: 'IndexComponent',

        data() {
            return {
                dropzone: null,
                title: null,
                post: null,
            }
        },

        mounted() {
            this.dropzone = new Dropzone(this.$refs.dropzone,
                {
                    url: '/api/posts',
                    autoProcessQueue: false,
                    addRemoveLinks: true,
                })
            this.getLatestPost()
        },

        methods: {
            store() {
                const data = new FormData()
                const files = this.dropzone.getAcceptedFiles()

                files.forEach(file => {
                    data.append('images[]', file)
                    this.dropzone.removeFile(file)
                })

                data.append('title', this.title)
                this.title = null

                axios.post('/api/posts', data)
                    .then(res => {
                        this.getLatestPost()
                    })
            },

            getLatestPost() {
                axios.get('/api/posts')
                    .then(res => {
                        this.post = res.data.data;
                    })
            }
        }
    }
</script>

<template>
    <div class="w-25">
        This is IndexComponent<br><br>
        <input type="text" v-model="title" placeholder="title" class="form-control mb-3">
        <div ref="dropzone" class="p-5 bg-dark text-center text-light btn d-block mb-3">
            Upload
        </div>
        <input @click.prevent="store()" type="submit" value="submit" class="btn btn-primary mb-3">
    </div>
    <div class="mt-5" v-if="post">
        <h1>{{ post.title }}</h1>
        <div v-for="image in post.images">
            <img :src="image.url">
        </div>
    </div>
</template>

<style scoped>

</style>
