<script>
    (function () {
        if (window.__adminMathJaxLoaderInstalled) {
            if (window.typesetAdminMath) {
                setTimeout(window.typesetAdminMath, 50);
            }
            return;
        }

        window.__adminMathJaxLoaderInstalled = true;

        window.typesetAdminMath = function () {
            if (window.MathJax && window.MathJax.typesetPromise) {
                window.MathJax
                    .typesetPromise(document.querySelectorAll('.math-content'))
                    .catch(function (error) {
                        if (window.console && console.warn) {
                            console.warn('Admin MathJax typeset failed', error);
                        }
                    });
            }
        };

        function configureMathJax() {
            if (window.MathJax && window.MathJax.typesetPromise) {
                return;
            }

            window.MathJax = window.MathJax || {
                tex: {
                    inlineMath: [['\\(', '\\)']],
                    displayMath: [['\\[', '\\]']],
                    processEscapes: true
                },
                options: {
                    skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code']
                }
            };
        }

        function loadAdminMathJax() {
            configureMathJax();

            if (window.MathJax && window.MathJax.typesetPromise) {
                setTimeout(window.typesetAdminMath, 50);
                return;
            }

            if (document.getElementById('admin-mathjax-script')) {
                return;
            }

            var script = document.createElement('script');
            script.id = 'admin-mathjax-script';
            script.async = true;
            script.src = 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js';
            script.onload = function () {
                setTimeout(window.typesetAdminMath, 50);
            };
            document.head.appendChild(script);
        }

        document.addEventListener('DOMContentLoaded', loadAdminMathJax);
        document.addEventListener('livewire:navigated', function () {
            loadAdminMathJax();
            setTimeout(window.typesetAdminMath, 80);
        });

        document.addEventListener('livewire:init', function () {
            if (window.Livewire && Livewire.hook && ! window.__adminMathJaxHooked) {
                window.__adminMathJaxHooked = true;
                Livewire.hook('morph.updated', function () {
                    setTimeout(window.typesetAdminMath, 50);
                });
            }
        });

        loadAdminMathJax();
    })();
</script>
