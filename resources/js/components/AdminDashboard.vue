<template>
    <div class="admin-dashboard">
        <h1>Gestion des utilisateurs</h1>
        <div v-if="loading" class="loading">Chargement en cours...</div>
        <div v-else>
            <div class="custom-table-container">
                <table class="user-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pseudo</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut VIP</th>
                        <th>Banni</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="user in users" :key="user.id" :class="{ banned: user.banned }">
                        <td>{{ user.id }}</td>
                        <td>{{ user.pseudo }}</td>
                        <td>{{ user.email }}</td>
                        <td>
                            <select v-model="user.role_id" @change="updateUserRole(user)">
                                <option value="1">Joueur</option>
                                <option value="2">Joueur Solide</option>
                                <option value="3">Admin</option>
                            </select>
                        </td>
                        <td>
                            <input
                                type="checkbox"
                                v-model="user.vip_status"
                                @change="updateUserVIP(user)"
                            />
                        </td>
                        <td>{{ user.banned ? "Oui" : "Non" }}</td>
                        <td>
                            <button
                                @click="toggleBanUser(user)"
                                :class="[user.banned ? 'btn-unban' : 'btn-ban']"
                            >
                                {{ user.banned ? "Débannir" : "Bannir" }}
                            </button>
                            <button @click="deleteUser(user)" class="btn-delete">Supprimer</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
    </div>
</template>

<script>
import axios from "axios";
import Swal from "sweetalert2";

export default {
    name: "AdminDashboard",
    data() {
        return {
            users: [],
            loading: true,
            errorMessage: null,
        };
    },
    mounted() {
        this.fetchUsers();
    },
    methods: {
        async fetchUsers() {
            try {
                const response = await axios.get("/api/admin/users", {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem("authToken")}`,
                    },
                });
                this.users = response.data.users;
            } catch (error) {
                console.error("Erreur lors de la récupération des utilisateurs :", error);
                this.errorMessage = "Impossible de charger les utilisateurs.";
            } finally {
                this.loading = false;
            }
        },
        async updateUserRole(user) {
            if (user.role_id === 3) {
                Swal.fire({
                    icon: "warning",
                    title: "Rôle Administrateur",
                    text: "Vous ne pouvez pas modifier directement un administrateur !",
                });
                return;
            }

            try {
                await axios.post(
                    `/api/admin/users/${user.id}/update-role`,
                    { role_id: user.role_id },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem("authToken")}`,
                        },
                    }
                );
                Swal.fire("Succès", `Le rôle de ${user.pseudo} a été mis à jour !`, "success");
            } catch (error) {
                Swal.fire("Erreur", "Impossible de mettre à jour le rôle.", "error");
            }
        },
        async updateUserVIP(user) {
            try {
                await axios.post(
                    `/api/admin/users/${user.id}/update-vip`,
                    { vip_status: user.vip_status },
                    {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem("authToken")}`,
                        },
                    }
                );
                Swal.fire("Succès", `Le statut VIP de ${user.pseudo} a été mis à jour !`, "success");
            } catch (error) {
                Swal.fire("Erreur", "Impossible de mettre à jour le statut VIP.", "error");
            }
        },
        async toggleBanUser(user) {
            if (user.role_id === 3) {
                Swal.fire({
                    icon: "error",
                    title: "Action interdite",
                    text: "Vous ne pouvez pas bannir un administrateur.",
                });
                return;
            }

            const result = await Swal.fire({
                title: `Êtes-vous sûr de vouloir ${user.banned ? "débannir" : "bannir"} ${user.pseudo} ?`,
                text: user.banned ? "Cet utilisateur pourra à nouveau accéder au système." : "Cette action est réversible.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: user.banned ? "Oui, débannir" : "Oui, bannir",
                cancelButtonText: "Annuler",
            });

            if (result.isConfirmed) {
                try {
                    await axios.post(
                        `/api/admin/users/${user.id}/toggle-ban`,
                        {},
                        {
                            headers: {
                                Authorization: `Bearer ${localStorage.getItem("authToken")}`,
                            },
                        }
                    );
                    user.banned = !user.banned;
                    Swal.fire(
                        "Succès",
                        `${user.pseudo} a été ${user.banned ? "banni" : "débanni"} avec succès.`,
                        "success"
                    );
                } catch (error) {
                    Swal.fire("Erreur", "Impossible de modifier le statut de bannissement.", "error");
                }
            }
        },
        async deleteUser(user) {
            if (user.role_id === 3) {
                Swal.fire({
                    icon: "error",
                    title: "Action interdite",
                    text: "Vous ne pouvez pas supprimer un administrateur.",
                });
                return;
            }

            const result = await Swal.fire({
                title: `Êtes-vous sûr de vouloir supprimer ${user.pseudo} ?`,
                text: "Cette action est irréversible.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Oui, supprimer",
                cancelButtonText: "Annuler",
            });

            if (result.isConfirmed) {
                try {
                    await axios.delete(`/api/admin/users/${user.id}`, {
                        headers: {
                            Authorization: `Bearer ${localStorage.getItem("authToken")}`,
                        },
                    });
                    this.users = this.users.filter((u) => u.id !== user.id);
                    Swal.fire("Succès", `${user.pseudo} a été supprimé avec succès.`, "success");
                } catch (error) {
                    Swal.fire("Erreur", "Impossible de supprimer cet utilisateur.", "error");
                }
            }
        },
    },
};
</script>
<style scoped>
.admin-dashboard {
    max-width: 1100px;
    margin: 0 auto;
    padding: 20px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

h1 {
    text-align: center;
    margin-bottom: 20px;
    color: #ff9800;
}

.user-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.user-table th,
.user-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}

.user-table th {
    background-color: #333;
    color: #fff;
}

.user-table .banned {
    background-color: rgba(255, 0, 0, 0.2);
}

.user-table select {
    background: #444;
    color: white;
    border: 1px solid #666;
    padding: 5px;
    border-radius: 5px;
}

.custom-table-container {
    max-height: 600px; /* Ajustez cette hauteur comme vous le souhaitez */
    overflow-y: auto; /* Active le défilement vertical */
    border: 1px solid #ddd; /* Optionnel : pour encadrer le tableau */
    border-radius: 5px; /* Ajout de coins arrondis */
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3); /* Optionnel : ombre pour le style */
    margin: 0 auto; /* Centrage horizontal */
}

.btn-ban,
.btn-unban,
.btn-delete {
    padding: 6px 12px; /* Uniformise la taille des boutons */
    border-radius: 5px;
    border: none;
    color: white;
    cursor: pointer;
    font-size: 14px; /* Harmonisation des tailles de texte */
    display: inline-block;
    min-width: 80px; /* Largeur minimale pour uniformiser */
    text-align: center;
}

.btn-ban {
    background-color: #f44336;
    margin-right: 7px;
}

.btn-ban:hover {
    background-color: #d32f2f;
}

.btn-unban {
    background-color: #4caf50;
    margin-right: 7px;
}

.btn-unban:hover {
    background-color: #388e3c;
}

.btn-delete {
    background-color: #2196f3;
}

.btn-delete:hover {
    background-color: #1976d2;
}

.error-message {
    color: #f44336;
    text-align: center;
    margin-top: 20px;
}
</style>

