import { createRouter, createWebHistory } from "vue-router";
import HomePage from "../views/HomePage.vue";
import ProfilePage from "../views/ProfilePage.vue";
import AdminPage from "../views/AdminPage.vue";

import TestPage from "../components/TestPage.vue";
import LoginForm from "../components/auth/LoginForm.vue";
import RegisterForm from "../components/auth/RegisterForm.vue";




const routes = [
    { path: "/", component: HomePage }, // HomePage

    { path: "/test", component: TestPage },
    {path: "/profile", component: ProfilePage},
    {path: "/login", component: LoginForm},
    {path: "/register", component: RegisterForm},
    {path: "/admin", component: AdminPage},




];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
