#install-git.sh
#!/bin/bash

# Nous devons installer des dépendances uniquement pour Docker
 [[ ! -e /.dockerenv ]] && exit 0
apt-get update -qq \
&& apt-get install -qq git \
  # Configurer les clés de déploiement SSH
 && 'which ssh-agent || ( apt-get install -qq openssh-client )' \
&& eval $(ssh-agent -s) \
&& ssh-add <(echo "$SSH_PRIVATE_KEY") \
&& mkdir -p ~/.ssh \
&& '[[ -f /.dockerenv ]] && echo -e "Hôte *\n\tStrictHostKeyChecking no\n\n" > ~/.ssh/config'