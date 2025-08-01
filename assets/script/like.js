import axios from "axios";

export default class Like {
    constructor(likeElements) {
        this.likeElements = likeElements;

        if (this.likeElements) {
            this.init();
        }
    }

    init() {
        this.likeElements.map((element) => {
            element.addEventListener("click", this.onClick);
        });
    }

    onClick(event) {
        event.preventDefault();
        // console.log("👆 Clic sur le bouton like !");
        const url = this.href;

        axios.get(url).then((response) => {
            console.log("👍 Like action successful:", response, this);

            const nb = response.data.nbLike;

            const span = this.querySelector("#nb-like");

            this.dataset.nb = nb;
            span.innerHTML = nb + " J'aime";

            const thumbsUpFilled = this.querySelector(".like-empty");
            const thumbsUpUnfilled = this.querySelector(".like-filled");

            thumbsUpFilled.classList.toggle("d-none");
            thumbsUpUnfilled.classList.toggle("d-none");
        });
    }
}
