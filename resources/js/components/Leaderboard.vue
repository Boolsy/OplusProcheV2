<template>
    <div class="leaderboard">
        <h2>Classement des joueurs</h2>
        <ul v-if="players.length">
            <li v-for="(player, index) in sortedPlayers" :key="player.id">
                <span>{{ index + 1 }}. {{ player.pseudo }}</span>
                <span>{{ player.points }} pts</span>
            </li>
        </ul>
        <p v-else>Aucun joueur classé pour l'instant.</p>
    </div>
</template>

<script>
export default {
    name: "Leaderboard",
    props: {
        players: {
            type: Array,
            required: true,
            validator(players) {
                return players.every(
                    (player) =>
                        typeof player.pseudo === "string" &&
                        typeof player.points === "number" &&
                        player.id !== undefined
                );
            },
        },
    },
    computed: {
        sortedPlayers() {
            return [...this.players].sort((a, b) => b.points - a.points);
        },
    },
};
</script>

<style>
.leaderboard {
    background: rgba(0, 0, 0, 0.7);
    padding: 20px;
    border-radius: 10px;
    color: white;
    text-align: left;
    font-size: 1.2rem;
    width: 100%;
    max-width: 300px;
    position: fixed;
    top: 50%;
    right: 5%;
    transform: translateY(-50%);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
}

.leaderboard h2 {
    text-align: center;
    margin-bottom: 15px;
}

.leaderboard ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.leaderboard li {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.leaderboard li:last-child {
    border-bottom: none;
}

.leaderboard p {
    text-align: center;
    font-style: italic;
    color: rgba(255, 255, 255, 0.7);
}
</style>
