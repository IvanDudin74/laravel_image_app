<script>
    import { Dropzone } from "dropzone";
    import { VueEditor } from "vue3-editor";

    export default {
        name: 'IndexComponent',

        data() {
            return {
                dropzone: null,
                title: null,
                content: null,
                post: null,
                imagesIdsForDelete: [],
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

            this.dropzone.on('removedfile', (file) => {
                this.imagesIdsForDelete.push(file.id)
            })
        },

        methods: {
            handleImageAdded(file, Editor, cursorLocation, resetUploader) {
                var formData = new FormData();
                formData.append("image", file);

                axios.post("/api/posts/images", formData)
                    .then(result => {
                        const url = result.data.url;
                        Editor.insertEmbed(cursorLocation, "image", url);
                        resetUploader();
                    })
                    .catch(err => {
                        console.log(err);
                    });
            },

            update() {
                const data = new FormData()
                const files = this.dropzone.getAcceptedFiles()

                files.forEach(file => {
                    data.append('images[]', file)
                    this.dropzone.removeFile(file)
                })

                this.imagesIdsForDelete.forEach(imageIdForDelete => {
                    data.append('images_ids_for_delete[]', imageIdForDelete)
                })

                data.append('title', this.title)
                data.append('content', this.content)
                data.append('_method', 'PATCH')

                axios.post(`/api/posts/${this.post.id}`, data)
                    .then(res => {
                        this.getLatestPost()
                    })
            },

            getLatestPost() {
                axios.get('/api/posts')
                    .then(res => {
                        this.post = res.data.data;
                        this.title = this.post.title
                        this.content = this.post.content

                        this.post.images.forEach(image => {
                            let file = { id: image.id, name: image.name, size: image.size };
                            this.dropzone.displayExistingFile(file, image.url);
                        })
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
        <input @click.prevent="update()" type="submit" value="update" class="btn btn-primary mb-3">
    </div>
</template>

<style scoped>

</style>
