<template>
    <div id="app">
        <BackgroundEffect />
        <Navbar />
        <div id="page-content">
            <router-view />
        </div>
    </div>
</template>

<script>
import Navbar from './components/shared/Navbar.vue';
import { useAuthStore } from './stores/auth';
import BackgroundEffect from "./components/BackgroundEffect.vue";

export default {
    name: "App",
    components: {
        Navbar,
        BackgroundEffect,
    },
    async mounted() {
        const authStore = useAuthStore();

        // Vérifie si un token existe dans localStorage
        const token = localStorage.getItem("authToken");

        if (token && !authStore.isLoggedIn) {
            // Configure le header d'authentification
            authStore.setToken(token);

            try {
                // Tente de récupérer les informations utilisateur
                await authStore.fetchUser();
            } catch (error) {
                console.error("Erreur lors de la récupération de l'utilisateur :", error);
                authStore.logout(); // Déconnecte l'utilisateur en cas d'échec
            }
        }
    },
};
</script>

<style>
#app {
    position: relative;
    display: flex; /* Utilise flexbox pour structurer les enfants */
    flex-direction: column; /* Aligne les enfants verticalement */
    height: 100%; /* S'assure que #app occupe toute la hauteur */
    overflow: hidden; /* Empêche tout débordement */
}

#page-content {
    position: relative;
    z-index: 1; /* Garantit que le contenu reste au-dessus du background */
    overflow-y: auto;
    overflow: hidden; /* Empêche tout débordement */
}
</style>
