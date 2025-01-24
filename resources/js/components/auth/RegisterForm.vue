<template>
    <div class="register-form">
        <h2>Inscription</h2>
        <form @submit.prevent="handleRegister">
            <div class="form-group">
                <label for="pseudo">Pseudo</label>
                <input
                    type="text"
                    id="pseudo"
                    v-model="form.pseudo"
                    class="dark-input"
                    placeholder="Entrez votre pseudo"
                    required
                />
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    v-model="form.email"
                    class="dark-input"
                    placeholder="Entrez votre email"
                    required
                />
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input
                    type="password"
                    id="password"
                    v-model="form.password"
                    class="dark-input"
                    placeholder="Entrez votre mot de passe"
                    required
                />
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input
                    type="password"
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    class="dark-input"
                    placeholder="Confirmez votre mot de passe"
                    required
                />
            </div>
            <button class="gradient-button" type="submit" :disabled="loading">
                <span v-if="loading">Inscription en cours...</span>
                <span v-else>S'inscrire</span>
            </button>
        </form>
        <p v-if="successMessage" class="success">{{ successMessage }}</p>
        <p v-if="error" class="error">{{ error }}</p>
    </div>
</template>

<script>
import { useAuthStore } from "@/stores/auth";

export default {
    name: "RegisterForm",
    data() {
        return {
            form: {
                pseudo: "",
                email: "",
                password: "",
                password_confirmation: "",
            },
            successMessage: null,
            error: null,
            loading: false,
        };
    },
    methods: {
        async handleRegister() {
            const authStore = useAuthStore();

            this.loading = true;
            this.error = null;

            try {
                // Appelle la méthode register du store
                await authStore.register(this.form);

                // Redirection après succès
                window.location.href = "/login";
            } catch (error) {
                console.error("Erreur dans le composant :", error);
                this.error = error.response?.data?.message || "Une erreur est survenue.";
            } finally {
                this.loading = false;
            }
        },
    },
    created() {
        const authStore = useAuthStore();
        if (authStore.isLoggedIn) {
            this.$router.push("/");
        }
    },
};
</script>

<style scoped>

.gradient-button {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 30px;
    font-size: 1rem;
    font-weight: bold;
    color: white;
    background: linear-gradient(90deg, #ff4a57, #ff884b);
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.gradient-button:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 10px rgba(255, 72, 87, 0.5);
}

.gradient-button:disabled {
    cursor: not-allowed;
    box-shadow: none;
}


.register-form {
    background: rgba(0, 0, 0, 0.8);
    padding: 30px;
    border-radius: 10px;
    color: white;
    text-align: left;
    max-width: 400px;
    margin: 50px auto;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.register-form h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #ff9800;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #fff;
}

.dark-input {
    width: 100%;
    padding: 10px;
    border: 1px solid #444;
    border-radius: 5px;
    background: #2c2c2c; /* Fond sombre */
    color: #fff; /* Texte blanc */
    font-size: 1rem;
}

.dark-input::placeholder {
    color: #888; /* Couleur pour le placeholder */
}

.dark-input:focus {
    border-color: #ff9800; /* Couleur de bordure au focus */
    outline: none; /* Supprime le contour par défaut */
}

button {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
}

button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.success {
    color: #4caf50;
    text-align: center;
    margin-top: 10px;
}

/* Désactive le style natif pour autofill */
input:-webkit-autofill {
    -webkit-box-shadow: 0 0 0 1000px #2c2c2c inset; /* Couleur de fond pour autofill */
    -webkit-text-fill-color: #fff; /* Couleur du texte pour autofill */
    transition: background-color 5000s ease-in-out 0s; /* Pour éviter un changement brusque */
}



/* Normalisation de la couleur de validation (si utilisée par le navigateur) */
input:invalid {
    background-color: #2c2c2c;
    color: #fff;
}


.error {
    color: #ff5722;
    text-align: center;
    margin-top: 10px;
}
</style>
