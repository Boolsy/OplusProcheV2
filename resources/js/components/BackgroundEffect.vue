<template>
    <div id="large-header" class="large-header">
        <canvas id="demo-canvas"></canvas>
    </div>
    <div class="waveWrapper waveAnimation">
        <div class="waveWrapperInner bgTop">
            <div class="wave waveTop"></div>
        </div>
        <div class="waveWrapperInner bgMiddle">
            <div class="wave waveMiddle"></div>
        </div>
        <div class="waveWrapperInner bgBottom">
            <div class="wave waveBottom"></div>
        </div>
    </div>

</template>

<script>

import { gsap } from "gsap";
export default {
    name: "BackgroundEffect",
    mounted() {
        this.initBackgroundEffect();
    },
    methods: {
        initBackgroundEffect() {
            // Insérez ici votre code JavaScript.
            (function () {
                var width, height, largeHeader, canvas, ctx, points, target, animateHeader = true;

                // Main
                initHeader();
                initAnimation();
                addListeners();

                function initHeader() {
                    width = window.innerWidth;
                    height = window.innerHeight;
                    target = {x: width / 2, y: height / 2};

                    largeHeader = document.getElementById("large-header");
                    largeHeader.style.height = height + "px";

                    canvas = document.getElementById("demo-canvas");
                    canvas.width = width;
                    canvas.height = height;
                    ctx = canvas.getContext("2d");

                    points = [];
                    for (var x = 0; x < width; x = x + width / 20) {
                        for (var y = 0; y < height; y = y + height / 20) {
                            var px = x + Math.random() * width / 20;
                            var py = y + Math.random() * height / 20;
                            var p = {x: px, originX: px, y: py, originY: py};
                            points.push(p);
                        }
                    }

                    for (var i = 0; i < points.length; i++) {
                        var closest = [];
                        var p1 = points[i];
                        for (var j = 0; j < points.length; j++) {
                            var p2 = points[j];
                            if (!(p1 == p2)) {
                                var placed = false;
                                for (var k = 0; k < 5; k++) {
                                    if (!placed) {
                                        if (closest[k] == undefined) {
                                            closest[k] = p2;
                                            placed = true;
                                        }
                                    }
                                }

                                for (var k = 0; k < 5; k++) {
                                    if (!placed) {
                                        if (getDistance(p1, p2) < getDistance(p1, closest[k])) {
                                            closest[k] = p2;
                                            placed = true;
                                        }
                                    }
                                }
                            }
                        }
                        p1.closest = closest;
                    }

                    for (var i in points) {
                        var c = new Circle(points[i], 2 + Math.random() * 2, "rgba(255,255,255,0.3)");
                        points[i].circle = c;
                    }
                }

                function addListeners() {
                    if (!("ontouchstart" in window)) {
                        window.addEventListener("mousemove", mouseMove);
                    }
                    window.addEventListener("scroll", scrollCheck);
                    window.addEventListener("resize", resize);
                }

                function mouseMove(e) {
                    var posx = 0;
                    var posy = 0; // Ajout de la déclaration pour posy
                    if (e.pageX || e.pageY) {
                        posx = e.pageX;
                        posy = e.pageY;
                    } else if (e.clientX || e.clientY) {
                        posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
                        posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
                    }
                    target.x = posx;
                    target.y = posy;
                }

                function scrollCheck() {
                    animateHeader = !(document.body.scrollTop > height);
                }

                function resize() {
                    width = window.innerWidth;
                    height = window.innerHeight;
                    largeHeader.style.height = height + "px";
                    canvas.width = width;
                    canvas.height = height;
                }

                function initAnimation() {
                    animate();
                    for (var i in points) {
                        shiftPoint(points[i]);
                    }
                }

                function animate() {
                    if (animateHeader) {
                        ctx.clearRect(0, 0, width, height);
                        for (var i in points) {
                            if (Math.abs(getDistance(target, points[i])) < 4000) {
                                points[i].active = 0.3;
                                points[i].circle.active = 0.6;
                            } else if (Math.abs(getDistance(target, points[i])) < 20000) {
                                points[i].active = 0.1;
                                points[i].circle.active = 0.3;
                            } else if (Math.abs(getDistance(target, points[i])) < 40000) {
                                points[i].active = 0.02;
                                points[i].circle.active = 0.1;
                            } else {
                                points[i].active = 0;
                                points[i].circle.active = 0;
                            }

                            drawLines(points[i]);
                            points[i].circle.draw();
                        }
                    }
                    requestAnimationFrame(animate);
                }

                function shiftPoint(p) {
                    gsap.to(p, {
                        x: p.originX - 50 + Math.random() * 100,
                        y: p.originY - 50 + Math.random() * 100,
                        duration: 1 + Math.random(),
                        ease: "circ.inOut",
                        onComplete: function () {
                            shiftPoint(p);
                        },
                    });
                }

                function drawLines(p) {
                    if (!p.active) return;
                    for (var i in p.closest) {
                        ctx.beginPath();
                        ctx.moveTo(p.x, p.y);
                        ctx.lineTo(p.closest[i].x, p.closest[i].y);
                        ctx.strokeStyle = "rgba(156,217,249," + p.active + ")";
                        ctx.stroke();
                    }
                }

                function Circle(pos, rad, color) {
                    var _this = this;

                    (function () {
                        _this.pos = pos || null;
                        _this.radius = rad || null;
                        _this.color = color || null;
                    })();

                    this.draw = function () {
                        if (!_this.active) return;
                        ctx.beginPath();
                        ctx.arc(_this.pos.x, _this.pos.y, _this.radius, 0, 2 * Math.PI, false);
                        ctx.fillStyle = "rgba(156,217,249," + _this.active + ")";
                        ctx.fill();
                    };
                }

                function getDistance(p1, p2) {
                    return Math.pow(p1.x - p2.x, 2) + Math.pow(p1.y - p2.y, 2);
                }
            })();
        },
    },
};
</script>

<style scoped>

.large-header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    overflow: hidden;
    background: linear-gradient(to top, #27273c 20%, #1e1e2a 80%);
}
.waveWrapper {
    overflow: hidden;
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 20%; /* Ajustez pour bien positionner les vagues */
    z-index: 0; /* Les vagues doivent être au-dessus du canvas */
}

.waveWrapperInner {
    position: absolute;
    width: 100%;
    overflow: hidden;
    height: 100%;
    bottom: 0;
    background-image: linear-gradient(to top, #86377b 20%, #27273c 80%);
}

.bgTop {
    z-index: 15;
    opacity: 0.5;
}

.bgMiddle {
    z-index: 10;
    opacity: 0.75;
}

.bgBottom {
    z-index: 5;
}
.waveWrapper {
    position: fixed; /* Les vagues sont fixes en bas de l'écran */
    bottom: 0; /* Toujours en bas */
    left: 0;
    width: 100%;
    height: 20%; /* Ajustez la hauteur si nécessaire */
    z-index: 0; /* Assurez-vous qu'il est derrière le contenu principal */
    overflow: hidden;
}

.waveWrapperInner {
    position: absolute;
    width: 100%;
    height: 100%;
    bottom: 0;
    background-image: linear-gradient(to top, #86377b 20%, #27273c 80%);
}


.wave {
    position: absolute;
    left: 0;
    width: 200%;
    height: 100%;
    background-repeat: repeat no-repeat;
    background-position: 0 bottom;
    transform-origin: center bottom;
}

.waveTop {
    background-size: 50% 100px;
    background-image: url("http://front-end-noobs.com/jecko/img/wave-top.png");
}

.waveMiddle {
    background-size: 50% 120px;
    background-image: url("http://front-end-noobs.com/jecko/img/wave-mid.png");
}

.waveBottom {
    background-size: 50% 100px;
    background-image: url("http://front-end-noobs.com/jecko/img/wave-bot.png");
}

.waveAnimation .waveTop {
    animation: move_wave 7s linear infinite;
}

.waveAnimation .waveMiddle {
    animation: move_wave 10s linear infinite;
}

.waveAnimation .waveBottom {
    animation: move_wave 15s linear infinite;
}

@keyframes move_wave {
    0% {
        transform: translateX(0) translateZ(0) scaleY(1);
    }
    50% {
        transform: translateX(-25%) translateZ(0) scaleY(0.35);
    }
    100% {
        transform: translateX(-50%) translateZ(0) scaleY(1);
    }
}




</style>
