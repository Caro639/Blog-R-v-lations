/**
 * Système de like AJAX
 * Gère les interactions de like/unlike sans rechargement de page
 */
document.addEventListener("DOMContentLoaded", function () {
    console.log("🔄 Système de likes initialisé");

    // Sélectionner tous les boutons de like
    const likeButtons = document.querySelectorAll('[id^="like-btn-"]');
    console.log(`👍 ${likeButtons.length} bouton(s) de like trouvé(s)`);

    likeButtons.forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            console.log("👆 Clic sur le bouton like !");

            const postId = this.getAttribute("data-post-id");
            const likeEmpty = this.querySelector(".like-empty");
            const likeFilled = this.querySelector(".like-filled");
            const likeCount = document.getElementById(`like-count-${postId}`);

            console.log(`📝 Post ID: ${postId}`);

            // Désactiver le bouton pendant la requête
            this.disabled = true;

            // Envoyer la requête AJAX
            fetch(`/like/${postId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        // Mettre à jour l'affichage des SVG
                        if (data.liked) {
                            // L'utilisateur a liké : afficher SVG rempli, masquer SVG vide
                            likeEmpty.classList.add("d-none");
                            likeFilled.classList.remove("d-none");
                        } else {
                            // L'utilisateur a unliké : afficher SVG vide, masquer SVG rempli
                            likeEmpty.classList.remove("d-none");
                            likeFilled.classList.add("d-none");
                        }

                        // Mettre à jour le compteur
                        likeCount.textContent = data.likeCount;

                        // Animation subtile pour feedback visuel
                        this.style.transform = "scale(0.95)";
                        setTimeout(() => {
                            this.style.transform = "scale(1)";
                        }, 150);
                    } else {
                        console.error(
                            "Erreur lors de la mise à jour du like:",
                            data.message
                        );
                    }
                })
                .catch((error) => {
                    console.error("Erreur réseau:", error);
                })
                .finally(() => {
                    // Réactiver le bouton
                    this.disabled = false;
                });
        });
    });
});
