            </main>
        </div>
    </div>
    
    <script>
        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const adminSidebar = document.getElementById('adminSidebar');
        
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function() {
                adminSidebar.classList.toggle('active');
            });
        }
        
        // Fermer le menu au clic en dehors (mobile)
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                if (adminSidebar && mobileMenuToggle) {
                    if (!adminSidebar.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                        adminSidebar.classList.remove('active');
                    }
                }
            }
        });
        
        // Confirmation avant suppression
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>