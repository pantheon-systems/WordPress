#!/usr/bin/env bash
set -ex

sudo apt-get install python-software-properties
sudo apt-add-repository -y ppa:chris-lea/node.js
sudo apt-get update
sudo apt-get install nodejs curl 
sudo apt-get install php5-tidy
sudo apt-get install subversion

# sass depenencies 
gem update --system
gem install sass
gem install scss-lint

# sudo ln -s /usr/bin/nodejs /usr/bin/node 
# > ln: failed to create symbolic link `/usr/bin/node': File exists

cd .standards
sudo npm install -g grunt grunt-cli
sudo npm install

# grunt --gruntfile Gruntfile.js
cd ../
