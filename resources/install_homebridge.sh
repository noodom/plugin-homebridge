#!/bin/bash
PROGRESS_FILE=/tmp/jeedom/homebridge/dependance
touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "--0%"
echo "Lancement de l'installation/mise à jour des dépendances homebridge"
sudo killall homebridge 2>/dev/null

echo "Vérification si la source deb-multimedia existe (bug lors du apt-get update si c'est le cas)"
if [ -f /etc/apt/sources.list.d/deb-multimedia.list* ]; then
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

echo "Vérification si la source repo.jeedom.com existe (bug lors de l'installation de node 6 si c'est le cas)"
if [ -f /etc/apt/sources.list.d/jeedom.list* ]; then
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

sudo apt-get install -y avahi-daemon avahi-discover avahi-utils libnss-mdns libavahi-compat-libdnssd-dev
echo 10 > ${PROGRESS_FILE}
echo "--10%"
actual=`nodejs -v`;
echo "Version actuelle : ${actual}"

if [[ $actual == *"4."* || $actual == *"5."* ]]
then
  echo "Ok, version suffisante";
else
  echo 20 > ${PROGRESS_FILE}
  echo "--20%"
  echo "KO, version obsolète à upgrader";
  echo "Suppression du Nodejs existant et installation du paquet recommandé"
  sudo npm rm -g homebridge-camera-ffmpeg --save
  sudo npm rm -g homebridge-jeedom --save
  sudo npm rm -g homebridge --save
  sudo npm rm -g request --save
  sudo npm rm -g node-gyp --save
  cd `npm root -g`;
  sudo npm rebuild
  sudo rm -f /usr/bin/homebridge >/dev/null 2>&1
  sudo rm -f /usr/local/bin/homebridge >/dev/null 2>&1
  
  sudo apt-get -y --purge autoremove nodejs npm
  echo 30 > ${PROGRESS_FILE}
  echo "--30%"
  echo "Utilisation du dépot officiel"
  curl -sL https://deb.nodesource.com/setup_5.x | sudo -E bash -
  sudo apt-key update
  sudo apt-get install -y nodejs
  
  new=`nodejs -v`;
  echo "Version actuelle : ${new}"
fi

echo 40 > ${PROGRESS_FILE}
echo "--40%"
echo "Installation de node-gyp..."
sudo npm install -g node-gyp
echo "Installation de request..."
sudo npm install -g request
echo 50 > ${PROGRESS_FILE}
echo "--50%"
nodePath=`npm root -g`
sudo rm -Rf ${nodePath}/homebridge-jeedom/.git
echo 60 > ${PROGRESS_FILE}
echo "--60%"
echo "Installation de Homebridge..."
sudo npm install -g --unsafe-perm https://github.com/jeedom/homebridge.git#master
echo 70 > ${PROGRESS_FILE}
echo "--70%"
echo "Installation de Homebridge-Jeedom..."
sudo npm install -g --unsafe-perm https://github.com/jeedom/homebridge-jeedom.git#beta
echo "Installation de Homebridge-Camera-FFMPEG..."
sudo npm install -g --unsafe-perm https://github.com/jeedom/homebridge-camera-ffmpeg.git#master
echo 80 > ${PROGRESS_FILE}
echo "--80%"

# do not break i don't know what
#if [[ `file -bi /usr/bin/ffmpeg` == *"application/x-executable"* ]]; then 
#  echo "FFMPEG existe"; 
#else 
#  echo "FFMPEG n'existe pas, on copie le wrapper pour avconv"; 
#  sudo cp -f ${nodePath}/homebridge-jeedom/ffmpeg-wrapper /usr/bin/ffmpeg
#  sudo chmod +x /usr/bin/ffmpeg
#fi
if [[ `file -bi /usr/bin/ffmpeg` == *"text/x-shellscript"* ]]; then 
  echo "Nettoyage de mon wrapper FFMPEG"; 
  sudo rm -f /usr/bin/ffmpeg
  echo "Réinstallez ffmpeg s'il était installé"; 
fi 
pwd
sudo chmod +x ../../plugins/homebridge/resources/ffmpeg-wrapper

echo 90 > ${PROGRESS_FILE}
echo "--90%"
#sudo systemctl is-enabled avahi-daemon >/dev/null
#if [ $? -ne 0 ]; then
#	echo "avahi-daemon non activé au démarrage, activation..."
#	sudo systemctl enable avahi-daemon
	echo "Désactivation de avahi-daemon au démarrage...(il démarrera avec le daemon (on contourne le bug de la Smart du 1 jan 1970))"
	sudo systemctl disable avahi-daemon >/dev/null 2>&1
#fi
sudo sed -i "/.*enable-dbus.*/c\enable-dbus=yes  #changed by homebridge" /etc/avahi/avahi-daemon.conf
sudo sed -i "/.*use-ipv6.*/c\use-ipv6=no  #changed by homebridge" /etc/avahi/avahi-daemon.conf
#sudo sed -i "/.*publish-aaaa-on-ipv4.*/c\publish-aaaa-on-ipv4=yes  #changed by homebridge" /etc/avahi/avahi-daemon.conf
#sudo sed -i "/.*publish-a-on-ipv6.*/c\publish-a-on-ipv6=no  #changed by homebridge" /etc/avahi/avahi-daemon.conf
if [ -n $1 ]; then
	UsedEth=$(ip addr | grep $1 | awk '{print $7}')
	sudo sed -i "/.*allow-interfaces.*/c\#allow-interfaces=$UsedEth  #changed by homebridge" /etc/avahi/avahi-daemon.conf
fi
if [ -f /etc/apt/sources.list.d/deb-multimedia.list.disabledByHomebridge ]; then
  echo "Réactivation de la source deb-multimedia qu'on avait désactivé !"
  sudo mv /etc/apt/sources.list.d/deb-multimedia.list.disabledByHomebridge /etc/apt/sources.list.d/deb-multimedia.list
fi
if [ -f /etc/apt/sources.list.d/jeedom.list.disabledByHomebridge ]; then
  echo "Réactivation de la source repo.jeedom.com qu'on avait désactivé !"
  sudo mv /etc/apt/sources.list.d/jeedom.list.disabledByHomebridge /etc/apt/sources.list.d/jeedom.list
fi
echo "Installation Homebridge OK"
echo 100 > ${PROGRESS_FILE}
echo "--100%"
rm ${PROGRESS_FILE}
