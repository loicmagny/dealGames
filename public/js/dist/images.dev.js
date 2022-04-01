"use strict";

window.onload = function () {
  // Gestion des boutons "Supprimer"
  var links = document.querySelectorAll('[data-delete]'); // On boucle sur links

  var _iteratorNormalCompletion = true;
  var _didIteratorError = false;
  var _iteratorError = undefined;

  try {
    for (var _iterator = links[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
      link = _step.value;
      // On écoute le clic
      link.addEventListener('click', function (e) {
        var _this = this;

        // On empêche la navigation
        e.preventDefault(); // On demande confirmation

        if (confirm('Voulez-vous supprimer cette image ?')) {
          // On envoie une requête Ajax vers le href du lien avec la méthode DELETE
          fetch(this.getAttribute('href'), {
            method: 'DELETE',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              _token: this.dataset.token
            })
          }).then( // On récupère la réponse en JSON
          function (response) {
            return response.json();
          }).then(function (data) {
            if (data.success) _this.parentElement.remove();else alert(data.error);
          })["catch"](function (e) {
            return alert(e);
          });
        }
      });
    }
  } catch (err) {
    _didIteratorError = true;
    _iteratorError = err;
  } finally {
    try {
      if (!_iteratorNormalCompletion && _iterator["return"] != null) {
        _iterator["return"]();
      }
    } finally {
      if (_didIteratorError) {
        throw _iteratorError;
      }
    }
  }
};