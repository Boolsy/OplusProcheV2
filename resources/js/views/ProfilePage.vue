<template>
    <div class="profile-page">
        <div class="profile-container">
            <ProfileInfo :user="user" @updateUser="handleUserUpdate" />
            <ProfileStats :stats="stats" />
        </div>
    </div>
</template>

<script>
import { useAuthStore } from "../stores/auth";
import ProfileInfo from "../components/ProfileInfo.vue";
import ProfileStats from "../components/ProfileStats.vue";

export default {
    name: "ProfilePage",
    components: {
        ProfileInfo,
        ProfileStats,
    },
    computed: {
        user() {
            const authStore = useAuthStore();
            return authStore.user;
        },
        stats() {
            const authStore = useAuthStore();
            return authStore.user?.stats || null;
        },
    },
    methods: {
        handleUserUpdate(updatedUser) {
            const authStore = useAuthStore();
            authStore.updateUser(updatedUser);
        },
    },
};
</script>

<style scoped>
.profile-page {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    text-align: center;
}

.profile-container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    margin-top: 20px;
}
</style>
