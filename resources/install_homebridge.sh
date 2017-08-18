#!/bin/bash
PROGRESS_FILE=/tmp/jeedom/homebridge/dependance
touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "--0%"
echo "Lancement de l'installation/mise à jour des dépendances homebridge"
sudo killall homebridge 2>/dev/null
sudo apt-get install -y avahi-daemon avahi-discover avahi-utils libnss-mdns libavahi-compat-libdnssd-dev
echo 10 > ${PROGRESS_FILE}
echo "--10%"
actual=`nodejs -v`;
echo "Version actuelle : ${actual}"
if [[ $actual == *"6."* ]]
then
  echo "Ok, version suffisante";
else
  echo "KO, version obsolète à upgrader";
  echo "Suppression du Nodejs existant et installation du paquet recommandé"
  sudo npm rm -g homebridge
  sudo npm rebuild
  sudo apt-get -y --purge autoremove nodejs npm
  arch=`arch`;
  echo 30 > ${PROGRESS_FILE}
  echo "--30%"
  
    echo "Utilisation du dépot officiel"
    curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash -
    sudo apt-get install -y nodejs
  
  new=`nodejs -v`;
  echo "Version actuelle : ${new}"
fi
echo 40 > ${PROGRESS_FILE}
echo "--40%"
sudo npm install -g node-gyp
sudo npm install -g request
echo 50 > ${PROGRESS_FILE}
echo "--50%"
nodePath=`npm root -g`
sudo rm -Rf ${nodePath}/homebridge-jeedom/.git
echo 60 > ${PROGRESS_FILE}
echo "--60%"
sudo npm install -g --unsafe-perm https://github.com/jeedom/homebridge.git#master
echo 70 > ${PROGRESS_FILE}
echo "--70%"
sudo npm install -g https://github.com/jeedom/homebridge-jeedom.git#beta
sudo npm install -g https://github.com/jeedom/homebridge-camera-ffmpeg.git#master
echo 80 > ${PROGRESS_FILE}
echo "--80%"
# copy the avconv ffmpeg wrapper
sudo cp -n ${nodePath}/homebridge-jeedom/ffmpeg-wrapper /usr/bin/ffmpeg
sudo chmod +x /usr/bin/ffmpeg
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
if [ -n $1 ]; then
	UsedEth=$(ifconfig | awk '/'$1'/ {print $1}' RS="\n\n")
	sudo sed -i "/.*allow-interfaces.*/c\#allow-interfaces=$UsedEth  #changed by homebridge" /etc/avahi/avahi-daemon.conf
fi
echo "Installation Homebridge OK"
echo 100 > ${PROGRESS_FILE}
echo "--100%"
rm ${PROGRESS_FILE}
