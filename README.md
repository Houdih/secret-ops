# Créer le projet (squelette minimal)
    composer create-project symfony/skeleton secret-ops

# Ajouter les composants web utiles (HTTP, serializer, validator…)
    symfony/framework-bundle symfony/orm-pack symfony/serializer-pack symfony/validator symfony/asset
    --dev symfony/maker-bundle

# Installer API Platform (core)
    api-platform/core

# En-têtes CORS pour requêter l’API
    nelmio/cors-bundle

# Messenger pour les événements : MissionStarted, AgentDied...
    symfony/messenger


