$(document).ready(function () {
    if (!(document.querySelector('.js-trick-list') === null)) {       
    var tricklist = document.querySelector('.js-trick-list');
    var nbtrickdisplayed = parseInt(tricklist.dataset.nbDisplayed);
    }

    if (!(document.querySelector('.js-comment-list') === null)) {
        var commentlist = document.querySelector('.js-comment-list');
        var nbcommentdisplayed = parseInt(commentlist.dataset.nbDisplayed);
    }

    $("#displayMoreTricks").click(function () {
        var tricks = document.getElementsByClassName('trick');
        nbtrickdisplayed = nbtrickdisplayed + nbtrickdisplayed;
        k = nbtrickdisplayed;
        if (nbtrickdisplayed >= tricks.length) {
            $("#displayMoreTricks").addClass("invisible");
            k = tricks.length;
        }
        for (var i = 0; i < k; i++) {	
            if (tricks[i].classList.contains("invisible")) {
                tricks[i].classList.remove("invisible");
            }
        }
        if ($("#pullUp").hasClass("invisible")) {
                $("#pullUp").removeClass("invisible");
        }
    });

    $("#displayMoreComments").click(function () {
        var comments = document.getElementsByClassName('comment');
        nbcommentdisplayed = nbcommentdisplayed + nbcommentdisplayed;
        var j = nbcommentdisplayed;
        if (nbcommentdisplayed >= comments.length) {
            $("#displayMoreComments").addClass("invisible");
            j = comments.length;
        }
        for (var i = 0; i < j; i++) {	
            if (comments[i].classList.contains("invisible")) {
                comments[i].classList.remove("invisible");
            }
        }
    });

    $("#displayMedia").click(function () {
        $("#mediaContent").removeClass("d-none");
        $("#mediaContent").addClass("d-flex");
        $("#displayMedia").addClass("d-none");
    });

    // On récupère la balise <div> en question qui contient l'attribut qui nous intéresse.
    var $picturecontainer = $('div#gb_tricksbundle_trick_pictures_list');

    // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
    var pictureindex = $picturecontainer.find(':input').length;

    // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
    $('#add_picture').click(function (e) {
        addPicture($picturecontainer);

        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        return false;
    });
    console.log(pictureindex);
    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un (cas d'une nouvelle annonce par exemple).
    if (pictureindex === 0) {
        addPicture($picturecontainer);
    } else {
        // S'il existe déjà des images, on ajoute un lien de suppression pour chacune d'entre elles
        $picturecontainer.children('div').each(function () {
            addPictureDeleteLink($(this));
        });
    }
    // La fonction qui ajoute un formulaire CategoryType
    function addPicture($picturecontainer) {
        // Dans le contenu de l'attribut « data-prototype », on remplace :
        // - le texte "__name__label__" qu'il contient par le label du champ
        // - le texte "__name__" qu'il contient par le numéro du champ
        var template = $picturecontainer.attr('data-prototype')
                .replace(/__name__label__/g, 'Picture n°' + (pictureindex + 1))
                .replace(/__name__/g, pictureindex)
                ;

        // On crée un objet jquery qui contient ce template
        var $prototype = $(template);

        // On ajoute au prototype un lien pour pouvoir supprimer la catégorie
        addPictureDeleteLink($prototype);

        // On ajoute le prototype modifié à la fin de la balise <div>
        $picturecontainer.append($prototype);

        // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
        pictureindex++;
    }


    // La fonction qui ajoute un lien de suppression d'une catégorie
    function addPictureDeleteLink($prototype) {
        // Création du lien
        var $deleteLink = $('\
        <div class="row">\n\
        <a href="#" class="btn btn-danger btn-block">\n\
        <button type="button" class="btn btn-danger btn-block">\n\
        <span class="fas fa-trash-alt">\n\
        </span>\n\
        </button></a>\n\
        </div>');

        // Ajout du lien
        $prototype.append($deleteLink);

        // Ajout du listener sur le clic du lien pour effectivement supprimer la catégorie
        $deleteLink.click(function (e) {
            $prototype.remove();

            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
            return false;
        });
    }

    // On récupère la balise <div> en question qui contient l'attribut « data-prototype » qui nous intéresse.
    var $videocontainer = $('div#gb_tricksbundle_trick_videos_list');

    // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
    var videoindex = $videocontainer.find(':input').length;

    // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
    $('#add_video').click(function (e) {
        addVideo($videocontainer);

        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        return false;
    });

    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un (cas d'une nouvelle annonce par exemple).
    if (videoindex == 0) {
        addVideo($videocontainer);
    } else {
        // S'il existe déjà des catégories, on ajoute un lien de suppression pour chacune d'entre elles
        $videocontainer.children('div').each(function () {
            addVideoDeleteLink($(this));
        });
    }

    // La fonction qui ajoute un formulaire CategoryType
    function addVideo($videocontainer) {
        // Dans le contenu de l'attribut « data-prototype », on remplace :
        // - le texte "__name__label__" qu'il contient par le label du champ
        // - le texte "__name__" qu'il contient par le numéro du champ
        var template = $videocontainer.attr('data-prototype')
                .replace(/__name__label__/g, 'Video n°' + (videoindex + 1))
                .replace(/__name__/g, videoindex)
                ;

        // On crée un objet jquery qui contient ce template
        var $prototype = $(template);

        // On ajoute au prototype un lien pour pouvoir supprimer la catégorie
        addVideoDeleteLink($prototype);

        // On ajoute le prototype modifié à la fin de la balise <div>
        $videocontainer.append($prototype);

        // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
        videoindex++;
    }

    // La fonction qui ajoute un lien de suppression d'une catégorie
    function addVideoDeleteLink($prototype) {
        // Création du lien
        var $deleteLink = $('<div class="col-sm-2"><a href="#" class="btn btn-danger">Supprimer</a></div>');

        // Ajout du lien
        $prototype.append($deleteLink);

        // Ajout du listener sur le clic du lien pour effectivement supprimer la catégorie
        $deleteLink.click(function (e) {
            $prototype.remove();

            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
            return false;
        });
    }
});
