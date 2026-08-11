/**/
document.querySelectorAll(".btn-like").forEach((button) => {
    console.log("like.js chargé");
    button.addEventListener("click", async function () {
        /*récuperer l'id de l'article */
        const articleId = this.dataset.id;
        /*envoie de la requête avec la route construite avec l'id, en post */
        const response = await fetch(`/article/${articleId}/like`, {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
        });

        /*repnse json de la méthode like , liked = true ou false*/
        const data = await response.json();

        /*si liked = true, on ajoute la class "liked" au coeur */
        const coeur = this.querySelector(".coeur");

        if (data.liked) {
            coeur.classList.add("liked");
        } else {
            coeur.classList.remove("liked");
        }
        /*trouver le <span class="likes"> correspondant au bouton cœur sur lequel on vient de cliquer en remonte dans le HTML jusqu'au parent le plus proche ayant : class="commentaires-likes". */
        const compteur = this.closest(".commentaires-likes").querySelector(
            ".likes",
        );

        compteur.textContent = data.nombreLikes;
    });
});

/* role de la fonction : au scroll de la page, si l'utilisateur a scrolle plus de 20px, on ajoute la class "scrolled" au header */

window.addEventListener("scroll", function () {
    const header = document.querySelector("header");

    if (window.scrollY > 20) {
        header.classList.add("scrolled");
    } else {
        header.classList.remove("scrolled");
    }
});

/*supprimer texte de la barre de recherche*/
function effacerRecherche() {
    console.log("effacerRecherche");
    document.getElementById("search").value = "";
}
