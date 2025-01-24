<template>
    <div class="login-form">
        <h2>Connexion</h2>
        <form @submit.prevent="handleLogin">
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
            <button class="gradient-button" type="submit" :disabled="loading">Se connecter</button>
        </form>
        <p v-if="error" class="error">{{ error }}</p>
    </div>
</template>

<script>
import { useAuthStore } from "@/stores/auth";
import { showSuccess, showError } from '@/sweetalert';

export default {
    name: "LoginForm",
    data() {
        return {
            form: {
                email: "",
                password: "",
            },
            error: null,
            loading: false, // Indique si la requête est en cours
        };
    },
    methods: {
        async handleLogin() {
            const authStore = useAuthStore();

            this.loading = true;
            this.error = null;

            try {
                // Appelle la méthode de connexion du store
                await authStore.login(this.form);

                // Affiche SweetAlert et attends que l'utilisateur appuie sur "OK"
                await Swal.fire({
                    title: 'Succès',
                    text: `Bonjour, ${authStore.user.pseudo} ! Heureux de vous revoir.`,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });

                // Redirection après que l'utilisateur ait confirmé l'alerte
                this.$router.push("/");
                setTimeout(() => {
                    window.location.reload();
                }, 100);
            } catch (error) {
                const errorMessage = error.response?.data.message || "Erreur de connexion. Veuillez réessayer.";
                this.error = errorMessage;

                // Message d'erreur avec SweetAlert
                showError(errorMessage);
            } finally {
                this.loading = false;
            }
        },
    },
};
</script>

<style scoped>
.login-form {
    background: rgba(0, 0, 0, 0.7);
    padding: 20px 30px;
    border-radius: 10px;
    color: white;
    text-align: left;
    max-width: 400px;
    margin: 50px auto;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    animation: fadeIn 0.5s ease-in-out;
}



.login-form h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #ff9800;
}

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


.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #ff5722;
    font-weight: bold;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

/* Désactive le style natif pour autofill */
input:-webkit-autofill {
    -webkit-box-shadow: 0 0 0px 1000px #2c2c2c inset; /* Couleur de fond pour autofill */
    -webkit-text-fill-color: #fff; /* Couleur du texte pour autofill */
    transition: background-color 5000s ease-in-out 0s; /* Pour éviter un changement brusque */
}



/* Normalisation de la couleur de validation (si utilisée par le navigateur) */
input:invalid {
    background-color: #2c2c2c;
    color: #fff;
}


button {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s ease-in-out;
}

button:hover {
    background-color: #0056b3;
}

button:disabled {
    background-color: #555;
    cursor: not-allowed;
}

.error {
    color: red;
    text-align: center;
    margin-top: 10px;
    font-size: 0.9rem;
}

/* Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
