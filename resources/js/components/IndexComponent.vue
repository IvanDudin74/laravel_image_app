<script>
    import { Dropzone } from "dropzone";
    import { VueEditor } from "vue3-editor";

    export default {
        name: 'IndexComponent',

        data() {
            return {
                dropzone: null,
                title: null,
                post: null,
                content: null,
            }
        },

        components: {
            VueEditor
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
            handleImageAdded(file, Editor, cursorLocation, resetUploader) {
                var formData = new FormData();
                formData.append("image", file);

                axios.post("/api/posts/images", formData)
                    .then(result => {
                        const url = result.data.url; // Get url from response
                        Editor.insertEmbed(cursorLocation, "image", url);
                        resetUploader();
                    })
                    .catch(err => {
                        console.log(err);
                    });
            },

            store() {
                const data = new FormData()
                const files = this.dropzone.getAcceptedFiles()

                files.forEach(file => {
                    data.append('images[]', file)
                    this.dropzone.removeFile(file)
                })

                data.append('title', this.title)
                data.append('content', this.content)
                this.title = null
                this.content = null

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
        <vue-editor useCustomImageHandler @image-added="handleImageAdded" v-model="content" class="mb-3"/>
        <input @click.prevent="store()" type="submit" value="submit" class="btn btn-primary mb-3">
    </div>
    <div class="mt-5" v-if="post">
        <h1>{{ post.title }}</h1>
        <div v-for="image in post.images">
            <img :src="image.preview_url" class="mb-3"><br>
            <img :src="image.url">
        </div>
        <div v-html="post.content">
        </div>
    </div>
</template>

<style scoped>

</style>
