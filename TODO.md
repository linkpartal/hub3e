#TODO
- [ ] Mettre une longueur maximum aux inputs correspandant à leur longueur max dans la base de données.
- [ ] Vérifier les imports de bibliothéque dans chaque page.
- [ ] Créer un bundle indépendant pour la gestion des tiers et des établissements.
- [ ] Créer un bundle indépendant pour la gestion des missions.
- [ ] Analyser le code et corriger les warnings.
- [ ] Gestion des erreurs.
- [ ] Montrer une alerte onunload() des pages d'édition sans confirmer le modification(ModifierEtablissement, ModierUser, ...)
        (<body onunload="function()">, function(){if(confirm('Voulez-vous Sauvegardez les modifications?'){}else{}})