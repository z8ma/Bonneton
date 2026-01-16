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

</body>

</html>
