<template>
    <button class="play-button" @click="$emit('play')">JOUER</button>
</template>

<style>
.play-button {
    background: linear-gradient(to right, #ff007a, #ff7a18);
    border: none;
    color: white;
    font-size: 1.5rem;
    font-weight: bold;
    padding: 15px 30px;
    border-radius: 50px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    transition: transform 0.3s, box-shadow 0.3s;
}

.play-button:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
}
</style>
