#!/bin/bash
PROGRESS_FILE=/tmp/jeedom/homebridge/dependance
touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "--0%"
BASEDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

echo "Lancement de l'installation/mise à jour des dépendances homebridge"
sudo killall homebridge 2>/dev/null


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
actual=`nodejs -v`;
echo "Version actuelle : ${actual}"
arch=`arch`;

#testVer=`php -r "echo version_compare('${actual}','v6','>=');"`
#if [[ $testVer == "1" ]]
if [[ $actual == *"4."* || $actual == *"5."*  || $actual == *"6."* || $actual == *"8."* || $actual == *"10."* ]]
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
  
  if [[ $arch == "armv6l" ]]
  then
    echo "Raspberry 1 détecté, utilisation du paquet pour armv6l"
    sudo rm /etc/apt/sources.list.d/nodesource.list
    wget http://node-arm.herokuapp.com/node_latest_armhf.deb
    sudo dpkg -i node_latest_armhf.deb
    sudo ln -s /usr/local/bin/node /usr/local/bin/nodejs
    rm node_latest_armhf.deb
    
    #echo "Raspberry zéro détecté, utilisation du paquet pour armv6l"
    #wget https://nodejs.org/dist/v5.12.0/node-v5.12.0-linux-armv6l.tar.gz
    #tar -xvf node-v5.12.0-linux-armv6l.tar.gz
    #cd node-v5.12.0-linux-armv6l
    #sudo cp -R * /usr/local/
    #cd ..
    #rm -fR node-v5.12.0-linux-armv6l/
    #rm -f node-v5.12.0-linux-armv6l.tar.gz
    #upgrade to recent npm
    #sudo npm install -g npm
  fi
  
  if [[ $arch == "aarch64" ]]
  then
    echo "Utilisation du dépot exotique car paquet officiel non existant en V5"
    wget http://dietpi.com/downloads/binaries/c2/nodejs_5-1_arm64.deb
    sudo dpkg -i nodejs_5-1_arm64.deb
    sudo ln -s /usr/local/bin/node /usr/local/bin/nodejs
    rm nodejs_5-1_arm64.deb
  fi
  
  if [[ $arch != "aarch64" && $arch != "armv6l" ]]
  then
    echo "Utilisation du dépot officiel"
    curl -sL https://deb.nodesource.com/setup_5.x | sudo -E bash -
    sudo apt-key update
    sudo apt-get install -y nodejs  
  fi
#  echo "Utilisation du dépot officiel"
#  curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash -
#  sudo apt-key update
#  sudo apt-get install -y nodejs  

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
#sudo npm install -g --unsafe-perm https://github.com/NebzHB/homebridge.git#master
sudo npm install -g --unsafe-perm homebridge
echo 70 > ${PROGRESS_FILE}
echo "--70%"
echo "Installation de Homebridge-Jeedom..."
if [ -n $2 ]; then
	BRANCH=$2
	echo "Sur la branche ${BRANCH}"
fi
sudo npm install -g --unsafe-perm https://github.com/NebzHB/homebridge-jeedom.git#${BRANCH}
echo "Installation de Homebridge-Camera-FFMPEG..."
sudo npm install -g --unsafe-perm https://github.com/NebzHB/homebridge-camera-ffmpeg.git#master
echo 80 > ${PROGRESS_FILE}
echo "--80%"

cd ${BASEDIR}/../node/
npm cache clean
sudo npm cache clean
sudo rm -rf node_modules
sudo npm install --unsafe-perm bases &> /dev/null
sudo npm install --unsafe-perm bignum &> /dev/null

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
