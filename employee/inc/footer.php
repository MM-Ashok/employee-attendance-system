</div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="../assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="../assets/vendors/chart.js/chart.umd.js"></script>
    <script src="../assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="../assets/js/off-canvas.js"></script>
    <script src="../assets/js/misc.js"></script>
    <script src="../assets/js/settings.js"></script>
    <script src="../assets/js/todolist.js"></script>
    <script src="../assets/js/jquery.cookie.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="../assets/js/dashboard.js"></script>
    <!-- End custom js for this page -->
    <script>
        // Wait 5 seconds (5000 milliseconds) before fading out the notification
        setTimeout(function() {
            const notification = document.getElementById('notification');
            if (notification) {
                notification.style.transition = "opacity 1s ease"; // Fade-out effect
                notification.style.opacity = 0; // Start fading out

                // Completely remove the notification after the fade-out effect
                setTimeout(function() {
                    notification.style.display = 'none';
                }, 1000); // Match with the fade duration (1 second)
            }
        }, 5000); // 5-second delay before starting the fade-out


    </script>

  </body>
</html>