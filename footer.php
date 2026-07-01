    <!-- back to top part start -->
    <button id="back-to-top" title="Back to Top">
      <i class="fas fa-arrow-up"></i>
    </button>
    <!-- back to top part end -->

    <!-- footer part start -->
    <footer class="footer-area py-4">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 text-center text-md-start">
            <p class="mb-0">© Praveen Kumar K &nbsp;2026 &nbsp;·&nbsp; UX/UI Architect &amp; AI Agentic Design Strategist</p>
          </div>
          <div class="col-md-6 mt-3 mt-md-0">
            <ul class="footer-menu d-flex flex-wrap justify-content-center justify-content-md-end list-unstyled mb-0" style="gap: 14px 0;">
              <?php
              // Reuse header menu if available; keep footer fully linked.
              if (isset($main_menu) && is_array($main_menu) && isset($user_role)) {
                foreach ($main_menu as $item) {
                  if (!in_array($user_role, $item['roles'] ?? [], true)) continue;
                  // Hide Admin in footer for guests (already filtered) and avoid clutter on mobile by skipping Home.
                  if (($item['url'] ?? '') === 'index') continue;
                  echo '<li class="ms-4"><a href="' . htmlspecialchars($item['url'], ENT_QUOTES) . '" class="text-secondary text-decoration-none small">' . htmlspecialchars($item['title'], ENT_QUOTES) . '</a></li>';
                }
              } else {
                // Safe fallback if footer ever renders without header.php
                echo '<li class="ms-4"><a href="about" class="text-secondary text-decoration-none small">About</a></li>';
                echo '<li class="ms-4"><a href="services" class="text-secondary text-decoration-none small">Services</a></li>';
                echo '<li class="ms-4"><a href="skills" class="text-secondary text-decoration-none small">Skills</a></li>';
                echo '<li class="ms-4"><a href="portfolio" class="text-secondary text-decoration-none small">Works</a></li>';
                echo '<li class="ms-4"><a href="blog" class="text-secondary text-decoration-none small">Blog</a></li>';
                echo '<li class="ms-4"><a href="contact" class="text-secondary text-decoration-none small">Contact</a></li>';
                echo '<li class="ms-4"><a href="login" class="text-secondary text-decoration-none small">Login</a></li>';
              }
              ?>
              <li class="ms-4"><a href="privacy" class="text-secondary text-decoration-none small">Privacy</a></li>
              <li class="ms-4"><a href="terms" class="text-secondary text-decoration-none small">Terms</a></li>
            </ul>
          </div>
        </div>
      </div>
    </footer>
    <!-- footer part end -->
  </div>

  <!-- JS here -->
  <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/jquery.magnific-popup.min.js"></script>
  <script src="assets/js/ajax-form.js"></script>
  <script src="assets/js/clipboard.min.js"></script>
  <script src="assets/js/slick.min.js"></script>
  <script src="assets/js/script.js"></script>
  <script src="assets/js/animations.js"></script>
  <script src="js/modern.js"></script>

</body>
</html>