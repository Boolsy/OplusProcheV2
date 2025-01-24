<template>
    <div class="admin-page">
        <AdminDashboard />
    </div>
</template>

<script>
import AdminDashboard from "../components/AdminDashboard.vue";

export default {
    components: {
        AdminDashboard,
    },
};
</script>

<style scoped>
.admin-page {
    text-align: center;
    height: 800px;
    margin: 20px;
    padding: 20px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    border-radius: 10px;
}
</style>
