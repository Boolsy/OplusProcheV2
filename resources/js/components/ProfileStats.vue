<template>
    <div class="profile-stats">
        <h2>Statistiques des parties</h2>
        <div v-if="stats" class="stats-container">
            <p><strong>Niveau :</strong> {{ user.level }}</p>
            <p><strong>Expérience :</strong> {{ user.experience }}</p>
            <p><strong>Parties jouées :</strong> {{ stats.games_played }}</p>
            <p><strong>Parties gagnées :</strong> {{ stats.games_won }}</p>
            <p><strong>Réponses correctes :</strong> {{ stats.correct_answers }}</p>
            <p><strong>Dernière partie :</strong> {{ formatDate(stats.last_game_date) }}</p>
        </div>
        <p v-else>Aucune statistique disponible pour le moment.</p>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "ProfileStats",
    data() {
        return {
            user: null, // Stocke les informations utilisateur
            stats: null, // Stocke les statistiques de l'utilisateur
        };
    },
    mounted() {
        this.fetchProfile(); // Appel API pour récupérer les informations utilisateur et statistiques
    },
    methods: {
        async fetchProfile() {
            try {
                const response = await axios.get("/api/profile", {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("authToken")}`,
                    },
                });

                // Stocker les données utilisateur et statistiques
                this.user = response.data.user;
                this.stats = response.data.stats;

                console.log("Données utilisateur :", this.user);
                console.log("Statistiques utilisateur :", this.stats);
            } catch (error) {
                console.error("Erreur lors de l'appel à /api/profile :", error.response?.data || error.message);
            }
        },
        formatDate(date) {
            if (!date) return "Non spécifiée";
            const d = new Date(date);
            return `${d.getDate().toString().padStart(2, "0")}/${(d.getMonth() + 1)
                .toString()
                .padStart(2, "0")}/${d.getFullYear()}`;
        },
    },
};
</script>

<style scoped>
.profile-stats {
    background: rgba(0, 0, 0, 0.7);
    padding: 20px;
    border-radius: 10px;
    color: white;
    text-align: left;
    max-width: 500px;
    margin: 20px auto;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.profile-stats h2 {
    text-align: center;
    margin-bottom: 15px;
    color: #ff9800;
}

.stats-container p {
    margin: 10px 0;
    font-size: 1.1rem;
}

p strong {
    color: #ff5722;
}
</style>
