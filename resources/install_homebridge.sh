#!/bin/bash
PROGRESS_FILE=/tmp/jeedom/homebridge/dependance
installVer='8' 	#NodeJS major version to be installed
minVer='8'	#min NodeJS major version to be accepted

touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "--0%"
BASEDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
DIRECTORY="/var/www"
if [ ! -d "$DIRECTORY" ]; then
  echo "Création du home www-data pour npm"
  sudo mkdir $DIRECTORY
fi
sudo chown -R www-data $DIRECTORY

echo "Lancement de l'installation/mise à jour des dépendances homebridge"
sudo killall homebridge &>/dev/null

if [ -f /etc/apt/sources.list.d/deb-multimedia.list* ]; then
  echo "Vérification si la source deb-multimedia existe (bug lors du apt-get update si c'est le cas)"
  echo "deb-multimedia existe !"
  if [ -f /etc/apt/sources.list.d/deb-multimedia.list.disabledByHomebridge ]; then
    echo "mais on l'a déjà désactivé..."
  else
    if [ -f /etc/apt/sources.list.d/deb-multimedia.list ]; then
      echo "Désactivation de la source deb-multimedia !"
      sudo mv /etc/apt/sources.list.d/deb-multimedia.list /etc/apt/sources.list.d/deb-multimedia.list.disabledByHomebridge
    else
      if [ -f /etc/apt/sources.list.d/deb-multimedia.list.disabled ]; then
        echo "mais il est déjà désactivé..."
      else
        echo "mais n'est ni 'disabled' ou 'disabledByHomebridge'... il sera normalement ignoré donc ca devrait passer..."
      fi
    fi
  fi
fi

if [ -f /etc/apt/sources.list.d/jeedom.list* ]; then
  echo "Vérification si la source repo.jeedom.com existe (bug lors de l'installation de node 6 si c'est le cas)"
  echo "repo.jeedom.com existe !"
  if [ -f /etc/apt/sources.list.d/jeedom.list.disabledByHomebridge ]; then
    echo "mais on l'a déjà désactivé..."
  else
    if [ -f /etc/apt/sources.list.d/jeedom.list ]; then
      echo "Désactivation de la source repo.jeedom.com !"
      sudo mv /etc/apt/sources.list.d/jeedom.list /etc/apt/sources.list.d/jeedom.list.disabledByHomebridge
    else
      if [ -f /etc/apt/sources.list.d/jeedom.list.disabled ]; then
        echo "mais il est déjà désactivé..."
      else
        echo "mais n'est ni 'disabled' ou 'disabledByHomebridge'... il sera normalement ignoré donc ca devrait passer..."
      fi
    fi
  fi
fi

echo 5 > ${PROGRESS_FILE}
echo "--5%"
sudo apt-get update
sudo apt-get install -y avahi-daemon avahi-discover avahi-utils libnss-mdns libavahi-compat-libdnssd-dev

echo 10 > ${PROGRESS_FILE}
echo "--10%"
type nodejs &>/dev/null
if [ $? -eq 0 ]; then actual=`nodejs -v`; fi
echo "Version actuelle : ${actual}"
arch=`arch`;

testVer=`php -r "echo version_compare('${actual}','v${minVer}','>=');"`
if [[ $testVer == "1" ]]
then
  echo "Ok, version suffisante";
  new=$actual
else
  echo 20 > ${PROGRESS_FILE}
  echo "--20%"
  echo "KO, version obsolète à upgrader";
  echo "Suppression du Nodejs existant et installation du paquet recommandé"
  #if npm exists
  type npm &>/dev/null
  if [ $? -eq 0 ]; then
    sudo npm rm -g homebridge-camera-ffmpeg --save
    sudo npm rm -g homebridge-jeedom --save
    sudo npm rm -g homebridge --save
    sudo npm rm -g request --save
    sudo npm rm -g node-gyp --save
    cd `npm root -g`;
    sudo npm rebuild
    npmPrefix=`npm prefix -g`
  else
    npmPrefix="/usr"
  fi
  sudo apt-get -y --purge autoremove nodejs npm
  
  echo 30 > ${PROGRESS_FILE}
  echo "--30%"
  if [[ $arch == "armv6l" ]]
  then
    echo "Raspberry 1, 2 ou zéro détecté, utilisation du paquet v${installVer} pour ${arch}"
    wget https://nodejs.org/download/release/latest-v${installVer}.x/node-*-linux-${arch}.tar.gz
    tar -xvf node-*-linux-${arch}.tar.gz
    cd node-*-linux-${arch}
    sudo cp -R * /usr/local/
    cd ..
    rm -fR node-*-linux-${arch}*
    #upgrade to recent npm
    sudo npm install -g npm
  else
    echo "Utilisation du dépot officiel"
    curl -sL https://deb.nodesource.com/setup_${installVer}.x | sudo -E bash -
    sudo apt-key update
    sudo apt-get install -y nodejs  
  fi
  
  npm config set prefix ${npmPrefix}

  new=`nodejs -v`;
  echo "Version actuelle : ${new}"
fi

echo 40 > ${PROGRESS_FILE}
echo "--40%"
# Remove old globals
sudo rm -f /usr/bin/homebridge &>/dev/null
sudo rm -f /usr/local/bin/homebridge &>/dev/null
sudo npm rm -g homebridge-camera-ffmpeg --save &>/dev/null
sudo npm rm -g homebridge-jeedom --save &>/dev/null
sudo npm rm -g homebridge --save &>/dev/null
sudo npm rm -g request --save &>/dev/null
sudo npm rm -g node-gyp --save &>/dev/null
cd `npm root -g`;
sudo npm rebuild &>/dev/null
cd ${BASEDIR};
#remove old local modules
sudo rm -rf node_modules

echo 50 > ${PROGRESS_FILE}
echo "--50%"
echo "Installation de Node-Gyp..."
#install deps from package.json
npm install
sudo chown -R www-data node_modules

echo 60 > ${PROGRESS_FILE}
echo "--60%"
echo "Installation de Homebridge..."
#need to be sudoed because of recompil
sudo npm install NebzHB/homebridge-stable#master
sudo chown -R www-data node_modules

echo 70 > ${PROGRESS_FILE}
echo "--70%"
echo "Installation de Homebridge-Jeedom..."
if [ -n $2 ]; then
	BRANCH=$2
	echo "Sur la branche ${BRANCH}"
fi
npm install NebzHB/homebridge-jeedom#${BRANCH}
sudo chown -R www-data node_modules

echo 75 > ${PROGRESS_FILE}
echo "--75%"
echo "Installation de Homebridge-Camera-FFMPEG..."
npm install NebzHB/homebridge-camera-ffmpeg#master
sudo chown -R www-data node_modules

echo 80 > ${PROGRESS_FILE}
echo "--80%"
hasPHP7GMP=`sudo dpkg -l | grep php7.0-gmp | wc -l`
hasPHP5GMP=`sudo dpkg -l | grep php5-gmp | wc -l`
if [[ "$hasPHP5GMP" == "0" ]] && [[ "$hasPHP7GMP" == "0" ]]; then
  echo "Installation de GMP (génération QRCode)"
  sudo apt-get -y install php7.0-gmp &>/dev/null
  if [ $? -ne 0 ]; then
    echo "pour php5"
    sudo apt-get -y install php5-gmp
  else
    echo "pour php7"
  fi

  sudo service nginx status &>/dev/null
  if [ $? = 0 ]; then
    echo "Reload nginx..."
    sudo service nginx reload
  fi
  sudo service apache2 status &>/dev/null
  if [ $? = 0 ]; then
    echo "Reload apache2..."
    sudo service apache2 reload
  fi
fi

echo 85 > ${PROGRESS_FILE}
echo "--85%"
# removing old node solution
if [ -e ${BASEDIR}/../node ]; then
  cd ${BASEDIR}/../node/
  npm cache clean
  sudo npm cache clean
  cd ${BASEDIR}/../
  sudo rm -Rf ${BASEDIR}/../node
fi
# cleaning of past tries
if [[ `file -bi /usr/bin/ffmpeg` == *"text/x-shellscript"* ]]; then 
  echo "Nettoyage de mon wrapper FFMPEG"; 
  sudo rm -f /usr/bin/ffmpeg
  echo "Réinstallez ffmpeg s'il était installé"; 
fi 


echo 90 > ${PROGRESS_FILE}
echo "--90%"
echo "Désactivation de avahi-daemon au démarrage...(il démarrera avec le daemon (on contourne le bug de la Smart du 1 jan 1970))"
sudo systemctl disable avahi-daemon &>/dev/null
sudo sed -i "/.*enable-dbus.*/c\enable-dbus=yes  #changed by homebridge" /etc/avahi/avahi-daemon.conf
sudo sed -i "/.*use-ipv6.*/c\use-ipv6=no  #changed by homebridge" /etc/avahi/avahi-daemon.conf
#sudo sed -i "/.*publish-aaaa-on-ipv4.*/c\publish-aaaa-on-ipv4=yes  #changed by homebridge" /etc/avahi/avahi-daemon.conf
#sudo sed -i "/.*publish-a-on-ipv6.*/c\publish-a-on-ipv6=no  #changed by homebridge" /etc/avahi/avahi-daemon.conf
if [ -n $1 ]; then
	UsedEth=$(ip addr | grep $1 | awk '{print $7}')
	sudo sed -i "/.*allow-interfaces.*/c\#allow-interfaces=$UsedEth  #changed by homebridge" /etc/avahi/avahi-daemon.conf
fi

echo 95 > ${PROGRESS_FILE}
echo "--95%"
if [ -f /etc/apt/sources.list.d/deb-multimedia.list.disabledByHomebridge ]; then
  echo "Réactivation de la source deb-multimedia qu'on avait désactivé !"
  sudo mv /etc/apt/sources.list.d/deb-multimedia.list.disabledByHomebridge /etc/apt/sources.list.d/deb-multimedia.list
fi
if [ -f /etc/apt/sources.list.d/jeedom.list.disabledByHomebridge ]; then
  echo "Réactivation de la source repo.jeedom.com qu'on avait désactivé !"
  sudo mv /etc/apt/sources.list.d/jeedom.list.disabledByHomebridge /etc/apt/sources.list.d/jeedom.list
fi

echo 100 > ${PROGRESS_FILE}
echo "--100%"
echo "Installation des dépendances Homebridge terminée, vérifiez qu'il n'y a pas d'erreur"
rm -f ${PROGRESS_FILE}
