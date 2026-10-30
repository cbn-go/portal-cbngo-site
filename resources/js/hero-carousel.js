// Carrossel de avisos do hero da home (Issues #23 e #39).
// O autoplay pausa com o mouse ou o foco dentro do carrossel, pelo botão de pausa
// e fica desligado para quem prefere menos movimento (prefers-reduced-motion).
export default function heroCarousel({ count, interval = 7000 }) {
    return {
        count,
        interval,
        active: 0,
        progress: 0,
        paused: false,
        hover: false,
        focus: false,
        reducedMotion: false,
        lastTick: null,
        frame: null,
        touchStartX: null,

        init() {
            this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            this.paused = this.reducedMotion;
            this.frame = requestAnimationFrame((now) => this.tick(now));
        },

        destroy() {
            cancelAnimationFrame(this.frame);
        },

        get running() {
            return !this.paused && !this.hover && !this.focus && !document.hidden;
        },

        tick(now) {
            if (this.running && this.lastTick !== null) {
                this.progress += (now - this.lastTick) / this.interval;

                if (this.progress >= 1) {
                    this.next();
                }
            }

            this.lastTick = now;
            this.frame = requestAnimationFrame((time) => this.tick(time));
        },

        progressFor(index) {
            if (index < this.active) {
                return 100;
            }

            if (index > this.active) {
                return 0;
            }

            return Math.min(this.progress, 1) * 100;
        },

        goTo(index) {
            this.active = (index + this.count) % this.count;
            this.progress = 0;
        },

        next() {
            this.goTo(this.active + 1);
        },

        prev() {
            this.goTo(this.active - 1);
        },

        togglePause() {
            this.paused = !this.paused;
        },

        swipeStart(event) {
            this.touchStartX = event.touches[0].clientX;
        },

        swipeEnd(event) {
            if (this.touchStartX === null) {
                return;
            }

            const deltaX = event.changedTouches[0].clientX - this.touchStartX;
            this.touchStartX = null;

            if (Math.abs(deltaX) > 40) {
                deltaX < 0 ? this.next() : this.prev();
            }
        },
    };
}
