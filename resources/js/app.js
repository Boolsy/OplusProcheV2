import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import { createPinia } from 'pinia';
import '@fortawesome/fontawesome-free/css/all.css';
import Swal from 'sweetalert2';
window.Swal = Swal;


const app = createApp(App);


app.use(router);
app.use(createPinia());
app.mount('#app');
