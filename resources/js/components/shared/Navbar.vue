<template>
    <nav class="navbar">
        <router-link to="/" class="logo-link">
            <!-- Le contenu peut rester vide ou inclure un texte -->
        </router-link>
        <div class="navbar-right">

        <template v-if="!isLoggedIn">
                <a href="/login">Connexion</a>
                <a href="/register">Inscription</a>
            </template>
            <template v-else>
                <div class="profile-menu"
                     v-show="isLoggedIn"
                     @click="toggleDropdown"
                     ref="profileMenu">
                    <div class="profile-infoz">
                        <img
                            :src="profileImageUrl"
                            alt="Avatar"
                            class="avatar"
                            @error="handleImageError"
                        />
                        <div class="profile-details">
                            <span class="profile-pseudo">{{ user?.pseudo }}</span>
                            <div class="level-info">
                                <span class="level">NIV {{ user.level }}</span>
                                <div class="xp-bar">
                                    <div
                                        class="xp-bar-progress"
                                        :style="{ width: `${xpProgress}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-show="dropdownVisible" class="dropdown-menu">
                        <router-link to="/profile">Profil</router-link>
                        <a v-if="isAdmin" href="/admin">Administration</a>
                        <a @click="logout">Déconnexion</a>
                    </div>
                </div>
            </template>
        </div>
    </nav>

</template>

<script>
import { useAuthStore } from "@/stores/auth.js";


export default {
    name: "Navbar",
    data() {
        return {
            dropdownVisible: false,
        };
    },
    computed: {
        user() {
            return useAuthStore().user;

        },
        isLoggedIn() {
            return useAuthStore().isLoggedIn;
        },
        isAdmin() {
            return this.user?.role === "Admin";
        },
        xpProgress() {
            return this.user?.experience ? this.user.experience % 100 : 0;
        },
        profileImageUrl() {
            if (!this.user || !this.user.profile_image) {
                return '/images/default-profile.png';
            }
            return this.user.profile_image.includes('default-profile.png')
                ? '/images/default-profile.png'
                : `/storage/${this.user.profile_image}`;
        },

    },
    methods: {
        // Affiche le menu déroulant
        showDropdown() {
            this.dropdownVisible = true;
        },
        // Masque le menu déroulant
        hideDropdown() {
            this.dropdownVisible = false;
        },
        logout() {
            const authStore = useAuthStore();
            authStore.logout();
            this.$router.push("/login");
        },
        handleImageError(event) {
            event.target.src = '/images/default-profile.png';
        },
    },
    mounted() {
        const profileMenu = this.$refs.profileMenu;

        if (profileMenu) {
            profileMenu.addEventListener("mouseenter", this.showDropdown);
            profileMenu.addEventListener("mouseleave", this.hideDropdown);
        }
    },
    beforeUnmount() {
        const profileMenu = this.$refs.profileMenu;

        if (profileMenu) {
            profileMenu.removeEventListener("mouseenter", this.showDropdown);
            profileMenu.removeEventListener("mouseleave", this.hideDropdown);
        }
    },



};
</script>


<style>
.logo-link {
    display: block;
    width: 100px; /* Ajustez selon vos besoins */
    height: 50px; /* Ajustez selon vos besoins */
    background-image: url('/images/pointoplusproche.png');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
}


.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    height: 70px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}



.navbar-right {
    display: flex;
    align-items: center;
    gap: 15px;
}

.profile-menu {
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
}

.profile-menu:hover .dropdown-menu {
    display: block; /* Affiche le menu déroulant lors du survol */
}

.avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 2.5px solid rgba(255, 255, 255, 0.5);
    transition: border 0.3s ease;
}

.avatar:hover {
    border-color: #ff9800;
}

.profile-details {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.profile-pseudo {
    font-size: 16px;
    font-weight: bold;
    color: white;
}

.level-info {
    display: flex;
    flex-direction: column;
    margin-top: 5px;
}

.level {
    font-size: 12px;
    font-weight: bold;
    color: #ff9800;
}

.profile-infoz {
    display: flex;
    align-items: center;
    gap: 10px;
}

.xp-bar {
    width: 100px;
    height: 6px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
    overflow: hidden;
    margin-top: 2px;
}

.xp-bar-progress {
    height: 100%;
    background: linear-gradient(90deg, #ff9800, #ff5722);
    transition: width 0.3s ease;
}

.dropdown-menu {
    display: none; /* Le menu est masqué par défaut */
    position: absolute;
    top: 100%;
    right: 0;
    background: rgba(0, 0, 0, 0.9);
    color: white;
    border-radius: 4px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    padding: 10px;
    z-index: 1000;
    transition: opacity 0.3s ease;
}

.dropdown-menu a {
    display: block;
    padding: 8px 15px;
    text-decoration: none;
    color: white;
    transition: background 0.3s ease;
    cursor: pointer;
}

.dropdown-menu a:hover {
    background: rgba(255, 255, 255, 0.1);
}

</style>
