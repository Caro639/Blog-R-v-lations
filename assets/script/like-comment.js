import axios from "axios";

export default class LikeComment {
    constructor(likeCommentElements) {
        this.likeCommentElements = likeCommentElements;

        if (this.likeCommentElements) {
            this.init();
        }
    }

    init() {
        this.likeCommentElements.map((element) => {
            element.addEventListener("click", this.onClick);
        });
    }

    onClick(event) {
        event.preventDefault();
        // console.log("👆 Clic sur le bouton like !");
        const url = this.href;

        axios.get(url).then((response) => {
            console.log("👍 Like action successful:", response, this);

            const nbLike = response.data.nbLikeComment;

            const span = this.querySelector("#nbLike-comment");

            this.dataset.nbLikeComment = nbLike;
            span.innerHTML = nbLike + " J'aime";

            const thumbsUpFilled = this.querySelector(".like-empty-comment");
            const thumbsUpUnfilled = this.querySelector(".like-filled-comment");

            thumbsUpFilled.classList.toggle("d-none");
            thumbsUpUnfilled.classList.toggle("d-none");
        });
    }
}
