</main>
<footer>
    <div class="footer-content">
        <p>© 2024 Bonneton. Tous droits réservés.</p>
        <p>Contact : <a href="mailto:contact@bonneton.com">contact@bonneton.com</a></p>
        <p>Téléphone : +33 1 23 45 67 89</p>
    </div>
</footer>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var selectors = [
            ".reveal",
            ".haut",
            ".bas",
            ".article",
            ".categories",
            ".image-container",
            "#content",
            ".container"
        ];
        var elements = document.querySelectorAll(selectors.join(", "));
        if (!elements.length) {
            return;
        }
        elements.forEach(function (el) {
            el.classList.add("reveal");
        });
        if (!("IntersectionObserver" in window)) {
            elements.forEach(function (el) {
                el.classList.add("reveal-visible");
            });
            return;
        }
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add("reveal-visible");
                } else {
                    entry.target.classList.remove("reveal-visible");
                }
            });
        }, { threshold: 0.15 });
        elements.forEach(function (el) {
            observer.observe(el);
        });
    });
</script>
<script>
    (function () {
        var target = window.scrollY || window.pageYOffset;
        var current = target;
        var ticking = false;
        var ease = 0.12;
        var maxDelta = 120;
        var isTouch = false;

        window.addEventListener("touchstart", function () {
            isTouch = true;
        }, { passive: true });

        function raf() {
            var diff = target - current;
            current += diff * ease;
            if (Math.abs(diff) < 0.5) {
                current = target;
            }
            window.scrollTo(0, current);
            if (Math.abs(diff) > 0.5) {
                requestAnimationFrame(raf);
            } else {
                ticking = false;
            }
        }

        window.addEventListener("wheel", function (e) {
            if (isTouch) {
                return;
            }
            e.preventDefault();
            var delta = Math.max(-maxDelta, Math.min(maxDelta, e.deltaY));
            target += delta;
            target = Math.max(0, Math.min(target, document.documentElement.scrollHeight - window.innerHeight));
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(raf);
            }
        }, { passive: false });
    })();
</script>

</body>

</html>
