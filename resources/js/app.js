import './bootstrap';
import {createApp} from "vue";
//import store from './store'
//import router from "./router"
import IndexComponent from "@/components/IndexComponent.vue";


const app = createApp({})
//app.use(router)
//app.use(store)
app.component("index-component", IndexComponent)
app.mount('#app')

