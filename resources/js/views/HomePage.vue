<template>
    <div class="home-page">
        <!-- Conteneur principal avec une grille -->
        <div class="main-grid">
            <!-- Contenu principal au centre -->
            <div class="main-content">
                <h1>Bienvenue sur</h1>
                <h1>O plus proche !</h1>
                <div class="button-wrapper">
                    <PlayButton @play="goToGame" />
                </div>
            </div>

            <!-- Leaderboard : uniquement affiché si activé -->
            <Leaderboard v-if="leaderboard.length" :players="leaderboard" />
        </div>
    </div>
</template>

<script>
import axios from "axios";
import PlayButton from "../components/shared/PlayButton.vue";
import Leaderboard from "../components/Leaderboard.vue";

export default {
    name: "HomePage",

    components: {
        PlayButton,
        Leaderboard,
    },
    data() {
        return {
            leaderboard: [],
        };
    },
    methods: {
        async fetchLeaderboard() {
            try {
                const response = await axios.get("/api/leaderboard");
                this.leaderboard = response.data.players;
            } catch (error) {
                console.error("Erreur lors du chargement du classement :", error);
            }
        },
        goToGame() {
            this.$router.push("/game");
        },
    },
    mounted() {
        this.fetchLeaderboard(); // Charge les données du leaderboard au montage
    },
};
</script>

<style scoped>
/* Conteneur principal */
.home-page {
    height: 100vh; /* Prend toute la hauteur de l'écran */
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    overflow: hidden; /* Empêche le défilement sur la page */
}

/* Grille principale */
.main-grid {
    display: grid;
    grid-template-columns: 2fr 1fr; /* 2/3 pour le contenu principal, 1/3 pour le leaderboard */
    align-items: center;
    gap: 20px; /* Espacement entre les colonnes */
    width: 90%; /* Ajustez selon la largeur désirée */
    max-width: 1200px;
}

/* Contenu principal */
.main-content {
    text-align: center;
    margin-right: 20px;
}

h1 {
    color: white;
    font-size: 3rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
    margin-bottom: 20px;
}

.button-wrapper {
    margin-top: 20px;
}
</style>

