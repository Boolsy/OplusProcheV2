import { defineStore } from "pinia";
import axios from "axios";
import { showSuccess } from '@/sweetalert';
export const useAuthStore = defineStore("auth", {


    state: () => ({
        user: (() => {
            try {
                const user = localStorage.getItem("user");
                return user ? JSON.parse(user) : null;
            } catch (error) {
                console.error("Erreur lors du parsing de l'utilisateur depuis localStorage :", error);
                return null;
            }
        })(),

        token: localStorage.getItem("authToken") || null, // Récupère le token
    }),

    updateUser(user) {
        this.user = { ...this.user, ...user };
        localStorage.setItem("user", JSON.stringify(this.user));
    },

    getters: {
        isLoggedIn: (state) => !!state.user,
        isAdmin: (state) => state.user?.role === "Admin", // Simplifie l'accès au rôle
        userPseudo: (state) => state.user?.pseudo || "Invité", // Exemple : pseudo par défaut
    },


    mounted() {


        const authStore = useAuthStore();

       //  Si un token est présent, tente de charger l'utilisateur
       if (authStore.token && !authStore.isLoggedIn) {
           authStore.fetchUser()
               .then(() => console.log("Utilisateur chargé :", authStore.user))
                .catch((error) => console.error("Erreur de chargement utilisateur :", error));
       }
   },

    actions: {
        setToken(token) {
            if (token) {
                localStorage.setItem("authToken", token);
                axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
            } else {
                localStorage.removeItem("authToken");
                delete axios.defaults.headers.common["Authorization"];
            }
            this.token = token; // Met à jour le state
        },



        setUser(user) {
            this.user = user;

            if (user.stats) {
                this.user.stats = user.stats; // Assure-toi de mettre à jour les stats
            }

            localStorage.setItem("user", JSON.stringify(this.user));
        },

        async login(credentials) {
            try {
                const response = await axios.post("/api/login", credentials);
                const { access_token, user } = response.data;

                this.setToken(access_token);
                this.setUser(user);
            } catch (error) {
                console.error("Erreur lors de la connexion :", error.response?.data || error.message);
                throw error; // Relance l'erreur pour gestion côté composant
            }
        },

        async logout() {
            try {
                await axios.post("/api/logout"); // Appelle le backend pour invalider le token

            } catch (error) {
                console.warn("Erreur lors de la déconnexion :", error.response?.data || error.message);
            } finally {
                // Réinitialiser le state avant tout autre accès
                this.user = null;
                this.token = null;
                localStorage.removeItem("authToken");
                localStorage.removeItem("user");

                showSuccess("Vous êtes déconnecté(e) avec succès");

                delete axios.defaults.headers.common["Authorization"];


            }
        },

        async register(formData) {
            try {
                const response = await axios.post("/api/register", formData);
                console.log("Inscription réussie :", response.data);

                // Affiche le message de succès
                showSuccess("Inscription réussie ! Vous pouvez maintenant vous connecter.");
                return response.data; // Retourne les données pour que le composant puisse en bénéficier
            } catch (error) {
                console.error("Erreur lors de l'inscription :", error.response?.data || error.message);

                // Affiche le message d'erreur
                const errorMessage = error.response?.data?.message || "Une erreur est survenue lors de l'inscription.";
                showError(errorMessage);
                throw error; // Rejette l'erreur pour que le composant la gère si nécessaire
            }
        },


        async fetchUser() {
            if (!this.token) {
                console.warn("Aucun token disponible, déconnexion...");
                this.logout();
                return;
            }

            try {
                const response = await axios.get("/api/profile", {
                    headers: {
                        Authorization: `Bearer ${this.token}`,
                    },
                });

                // Vérifiez et mettez à jour l'utilisateur
                if (response.data.user) {
                    this.setUser(response.data.user); // Utilisez une méthode réactive
                } else {
                    console.warn("Aucun utilisateur retourné par l'API.");
                }

                // Vérifiez et mettez à jour les stats
                if (response.data.stats) {
                    this.user = {
                        ...this.user, // Conservez les autres propriétés utilisateur
                        stats: response.data.stats, // Ajoutez ou mettez à jour les stats
                    };
                } else {
                    console.warn("Aucune statistique disponible pour l'utilisateur.");
                }

                // Vérification finale
                console.log("Données utilisateur après mise à jour :", this.user);
            } catch (error) {
                console.error("Erreur lors du chargement des données utilisateur :", error.response?.data || error.message);
                this.logout();
            }
        },


        updateUser(user) {
            this.user = user;
            localStorage.setItem("user", JSON.stringify(user));
        },
    },
});
