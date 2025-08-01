import "bootstrap/dist/js/bootstrap.js";
// import "./script/like-system.js";
import Like from "./script/like.js";
import LikeComment from "./script/like-comment.js";

document.addEventListener("DOMContentLoaded", () => {
    console.log("📦 Assets chargés et initialisés");

    //Like system
    const likeElements = [].slice.call(
        document.querySelectorAll('a[data-action="like"]')
    );
    if (likeElements) {
        new Like(likeElements);
        console.log(
            // "👍 Système de likes initialisé pour les éléments:",
            likeElements
        );
    }

    const likeCommentElements = [].slice.call(
        document.querySelectorAll('a[data-action="like-comment"]')
    );
    if (likeCommentElements) {
        new LikeComment(likeCommentElements);
        console.log(
            "👍 Système de likes pour les commentaires initialisé pour les éléments:",
            likeCommentElements
        );
    }
});
