<template>
    <div>
        <div v-if="user" class="profile-info">
            <div class="profile-header">
                <h2>Profil de {{ user.pseudo }}</h2>
                <div class="avatar-container">
                    <img
                        :src="profileImageUrl"
                        alt="Avatar"
                        class="avatar"
                        @error="handleImageError"
                    />
                </div>
            </div>

            <!-- Messages de succès et d'erreur -->
            <div v-if="successMessage" class="success-message">
                {{ successMessage }}
            </div>
            <div v-if="errorMessage" class="error-message">
                {{ errorMessage }}
            </div>

            <!-- Mode édition -->
            <div v-if="editMode" class="profile-edit-form">
                <form @submit.prevent="submitEdit" class="edit-form">
                  <div class="form-row">
                    <!-- Champ Pseudo -->
                    <div class="form-group">
                        <label for="pseudo">Pseudo :</label>
                        <input
                            type="text"
                            id="pseudo"
                            v-model="editedUser.pseudo"
                            class="dark-input"
                            placeholder="Entrez votre pseudo"
                            required
                        />
                    </div>

                    <!-- Champ Email -->
                    <div class="form-group">
                        <label for="email">Email :</label>
                        <input
                            type="email"
                            id="email"
                            v-model="editedUser.email"
                            class="dark-input"
                            placeholder="Entrez votre email"
                            required
                        />
                    </div>

                    <!-- Champ Date de naissance -->
                    <div class="form-group">
                        <label for="birth_date">Date de naissance :</label>
                        <input
                            type="date"
                            id="birth_date"
                            v-model="editedUser.birth_date"
                            class="dark-input"
                            :max="today"
                        />
                    </div>
                      <div class="form-group">
                          <label for="profile_image">Photo de profil :</label>
                          <input
                              type="file"
                              id="profile_image"
                              @change="handleFileChange"
                              class="dark-input"
                              accept="image/*"
                          />

                      </div>
                    </div>
                   <div class="form-row">
                    <!-- Mot de passe -->
                       <div class="form-group password-field">
                           <label for="old_password">Mot de passe actuel :</label>
                           <div class="password-container">
                               <input
                                   :type="showOldPassword ? 'text' : 'password'"
                                   id="old_password"
                                   v-model="oldPassword"
                                   class="dark-input with-icon"
                                   placeholder="Entrez votre mot de passe actuel"
                                   @blur="validateOldPasswordRequired"
                               />
                               <span
                                   class="toggle-password"
                                   @click="togglePasswordVisibility('oldPassword')"
                               >
            <i :class="showOldPassword ? 'fas fa-eye' : 'fas fa-eye-slash'"></i>
        </span>
                           </div>
                       </div>



                    <!-- Nouveau mot de passe -->
                    <div class="form-group password-field">
                        <label for="new_password">Nouveau mot de passe :</label>
                        <div class="password-container">
                            <input
                                :type="showNewPassword ? 'text' : 'password'"
                                id="new_password"
                                v-model="newPassword"
                                class="dark-input with-icon"
                                placeholder="Optionnel"
                                autocomplete="new-password"
                            />
                            <span
                                class="toggle-password"
                                @click="togglePasswordVisibility('newPassword')"
                            >
    <i :class="showNewPassword ? 'fas fa-eye' : 'fas fa-eye-slash'"></i>
</span>
                        </div>
                    </div>

                    <!-- Confirmer le mot de passe -->
                    <div class="form-group password-field">
                        <label for="confirm_password">Confirmer le mot de passe :</label>
                        <div class="password-container">
                            <input
                                :type="showConfirmPassword ? 'text' : 'password'"
                                id="confirm_password"
                                v-model="confirmPassword"
                                class="dark-input with-icon"
                                placeholder="Optionnel"
                                autocomplete="new-password"
                            />
                            <span
                                class="toggle-password"
                                @click="togglePasswordVisibility('confirmPassword')"
                            >
    <i :class="showConfirmPassword ? 'fas fa-eye' : 'fas fa-eye-slash'"></i>
</span>
                        </div>
                    </div>
                    </div>
                    <!-- Boutons d'action -->
                    <button
                        type="submit"
                        class="gradient-button"
                        :disabled="!oldPassword.trim() && (newPassword.trim() || confirmPassword.trim())"
                    >
                        Enregistrer
                    </button>
                    <button type="button" class="gradient-button" @click="toggleEditMode">Annuler</button>
                </form>
            </div>

            <!-- Mode affichage -->
            <div v-else class="profile-display">
                <div class="profile-details">
                    <p><strong>Pseudo :</strong> {{ user.pseudo }}</p>
                    <p><strong>Email :</strong> {{ user.email }}</p>
                    <p><strong>Rôle :</strong> {{ user.role }}</p>
                    <p><strong>Vip :</strong> {{ user.vip_status }}</p>
                    <p><strong>Date de naissance :</strong> {{ formatDate(user.birth_date) || "Non spécifiée" }}</p>
                    <p><strong>Date d'inscription :</strong> {{ formatDate(user.created_at) }}</p>
                </div>
                <button @click="toggleEditMode" class="gradient-button">Modifier le profil</button>
            </div>
        </div>

        <div v-else class="loading-message">
            Chargement des informations utilisateur...
        </div>
    </div>
</template>


<script>
import axios from "axios";
import { useAuthStore } from "@/stores/auth.js";

export default {
    name: "ProfileInfo",

    data() {
        return {
            user: null,
            editMode: false,
            oldPassword: "",
            oldPasswordRequired: false,
            newPassword: "",
            confirmPassword: "",
            previewImage: null,
            profile_image: null,
            successMessage: "",
            errorMessage: "",
            isPasswordChangeAttempted: false,
            editedUser: null,
            showOldPassword: false,
            showNewPassword: false,
            showConfirmPassword: false,
            today: new Date().toISOString().split("T")[0], // Date du jour pour la validation

        };
    },

    mounted() {
        this.fetchProfile();
    },

    methods: {
        async fetchProfile() {
            try {
                const response = await axios.get("/api/profile", {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("authToken")}`,
                    },
                });

                const user = response.data.user;

                if (user.birth_date) {
                    const birthDate = new Date(user.birth_date);
                    user.birth_date = birthDate.toISOString().split("T")[0]; // Format yyyy-MM-dd
                }


                const authStore = useAuthStore();
                authStore.updateUser(user);
                this.user = user;

                if (this.editMode) {
                    this.editedUser = { ...user };
                }
            } catch (error) {
                this.errorMessage = "Impossible de charger les informations du profil.";
            }
        },

        validateOldPasswordRequired() {
            // Vérifier si le champ mot de passe actuel est vide
            if (!this.oldPassword.trim()) {
                this.errorMessage = "Le mot de passe actuel est requis.";
            } else {
                this.errorMessage = "";
            }
        },

        async validateOldPassword() {
            try {
                const response = await axios.post("/api/validate-password", {
                    old_password: this.oldPassword.trim(),
                });

                // Si l'API retourne que le mot de passe est valide
                return response.data.valid === true;
            } catch (error) {
                // Capture l'erreur et affiche un message si nécessaire
                this.errorMessage =
                    error.response?.data?.message || "Erreur lors de la validation de l'ancien mot de passe.";
                return false;
            }
        },


        handleImageError(event) {
            event.target.src = "/images/default-profile.png";
        },

        toggleEditMode() {
            if (this.editMode) {
                this.editedUser = null;
                this.previewImage = null;
                this.resetPasswordFields(); // Réinitialise tous les champs de mot de passe
            } else {
                this.editedUser = { ...this.user };
                this.resetPasswordFields(); // Réinitialise ici aussi pour éviter tout auto-remplissage
            }
            this.editMode = !this.editMode;
        },

        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.profile_image = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewImage = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        async submitEdit() {
            // Réinitialiser les messages d'erreur et de succès
            this.errorMessage = "";
            this.successMessage = "";

            // Vérifier si le mot de passe actuel est vide
            if (!this.oldPassword.trim()) {
                this.errorMessage = "Le mot de passe actuel est requis.";
                return;
            }

            // Vérification de la validité du mot de passe actuel
            const isValid = await this.validateOldPassword();
            if (!isValid) {
                this.errorMessage = "Le mot de passe actuel est incorrect.";
                return;
            }

            // Vérifier les nouveaux mots de passe s'ils sont fournis
            if (this.newPassword.trim() || this.confirmPassword.trim()) {
                if (!this.newPassword.trim()) {
                    this.errorMessage = "Veuillez entrer un nouveau mot de passe.";
                    return;
                }

                if (this.newPassword.trim() !== this.confirmPassword.trim()) {
                    this.errorMessage = "Les mots de passe ne correspondent pas.";
                    return;
                }

                if (this.newPassword.trim().length < 8) {
                    this.errorMessage = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
                    return;
                }
            }

            // Préparer les données pour la mise à jour
            const formData = new FormData();
            formData.append("pseudo", this.editedUser.pseudo);
            formData.append("email", this.editedUser.email);
            formData.append("birth_date", this.editedUser.birth_date);

            if (this.oldPassword.trim() && this.newPassword.trim()) {
                formData.append("old_password", this.oldPassword.trim());
                formData.append("new_password", this.newPassword.trim());
                formData.append("new_password_confirmation", this.confirmPassword.trim());
            }

            if (this.profile_image) {
                formData.append("profile_image", this.profile_image);
            }

            // Envoyer les données au serveur
            try {
                const response = await axios.post("/api/profile/update", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                        Authorization: `Bearer ${localStorage.getItem("authToken")}`,
                    },
                });

                // Mise à jour réussie
                this.successMessage = "Profil mis à jour avec succès !";
                this.resetPasswordFields();
                this.editMode = false;
                this.user = response.data.user;
                useAuthStore().updateUser(this.user);
            } catch (error) {
                this.errorMessage = error.response?.data?.message || "Une erreur est survenue lors de la mise à jour.";
            }

        },


        resetPasswordFields() {
            this.oldPassword = "";
            this.newPassword = "";
            this.confirmPassword = "";
        },

        formatDate(date) {
            if (!date) return "Non spécifiée";
            const d = new Date(date);
            return `${d.getDate().toString().padStart(2, "0")}/${(d.getMonth() + 1)
                .toString()
                .padStart(2, "0")}/${d.getFullYear()}`;
        },

        togglePasswordVisibility(field) {
            if (field === "oldPassword") {
                this.showOldPassword = !this.showOldPassword;
            } else if (field === "newPassword") {
                this.showNewPassword = !this.showNewPassword;
            } else if (field === "confirmPassword") {
                this.showConfirmPassword = !this.showConfirmPassword;
            }
        },
    },

    computed: {
        user() {
            return useAuthStore().user;
        },

        profileImageUrl() {
            if (!this.user || !this.user.profile_image) {
                return "/images/default-profile.png";
            }
            return this.user.profile_image.includes("default-profile.png")
                ? "/images/default-profile.png"
                : `/storage/${this.user.profile_image}`;
        },
    },
};


</script>


<style scoped>

.profile-header {
    text-align: center;
    margin-bottom: 20px;
}

.avatar-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
    width: 130px;
    height: 130px;
    overflow: hidden;
    border-radius: 50%;
}

.gradient-button {
    background: linear-gradient(to right, #ff007a, #ff7a18);
    border: none;
    color: white;
    font-size: 1rem;
    font-weight: bold;
    padding: 15px 30px;
    border-radius: 50px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    transition: transform 0.3s, box-shadow 0.3s;
}

.gradient-button:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
}

.error-message {
    color: #ff4d4d; /* Rouge vif */
    font-weight: bold;
    margin-top: 10px;
    text-align: left; /* Aligné avec les champs */
}

.success-message {
    color: #4caf50; /* Vert */
    font-weight: bold;
    margin-top: 10px;
    text-align: left; /* Aligné avec les champs */
}


.avatar {

    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
}

.image-preview img {
    margin-top: 15px;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

input {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
}


.profile-info {
    background: rgba(0, 0, 0, 0.7);
    padding: 20px;
    border-radius: 10px;
    color: white;
    text-align: left;
    max-width: 500px;
    margin: 20px auto;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.profile-info h2 {
    text-align: center;
    margin-bottom: 15px;
    color: #ff9800;
}


.profile-header {
    text-align: center;
    margin-bottom: 15px;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
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

.profile-details p {
    margin: 10px 0;
    font-size: 1.1rem;
}

.profile-details p strong {
    color: #ff5722;
}

.password-container {
    position: relative;
    display: flex;
    align-items: center;
}

.with-icon {
    padding-right: 40px;
}

.toggle-password {
    position: absolute;
    right: 10px;
    cursor: pointer;
    font-size: 1.2rem;
    color: #888;
    transition: color 0.3s;
}

.toggle-password:hover {
    color: #ff9800;
}

.profile-info {
    background: rgba(0, 0, 0, 0.7);
    padding: 20px;
    border-radius: 10px;
    color: white;
    text-align: left;
    max-width: 500px;
    margin: 20px auto;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.profile-header {
    text-align: center;
    margin-bottom: 15px;
}

.profile-details p {
    margin: 10px 0;
    font-size: 1.1rem;
}

.gradient-button {
    margin-top: 15px;
    margin-right: 20px;
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.gradient-button:hover {
    background-color: #0056b3;
}

.edit-profile-form label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.edit-profile-form input {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.edit-form {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 20px;
}

.form-row {
    flex: 1 1 45%; /* 45% de la largeur pour chaque colonne */
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.form-group {
    display: flex;
    flex-direction: column;
}


.password-container {
    position: relative;
    display: flex;
    align-items: center;
}


</style>
