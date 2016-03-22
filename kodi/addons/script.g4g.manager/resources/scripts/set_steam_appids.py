#!/usr/bin/env python
#encoding: UTF-8

# Copyright (C) 2016 André Gregor-Herrmann
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

import getopt
import sys
import os
import re
import pprint
import json
import hashlib
from steamweb import SteamWebBrowser
from os.path import expanduser

HOME_DIR = expanduser("~")
FOLDER_G4G = os.path.join(HOME_DIR, '.g4g')
FOLDER_G4G_STEAM = os.path.join(FOLDER_G4G, 'steam')
FOLDER_G4G_STEAM_CACHE = os.path.join(FOLDER_G4G_STEAM, 'cache')

# get options set
opts, args = getopt.getopt(sys.argv[1:], 'u:p', ['user=', 'password='])
pprint.pprint(opts)
steam_user = ""

for opt, arg in opts:
    if opt in ('-u', '--user'):
        steam_user = arg
    elif opt in ('-p', '--password'):
        steam_password = arg

if steam_user != "":
    swb = SteamWebBrowser(steam_user, steam_password)
    if not swb.logged_in():
        swb.login()
    if swb.logged_in():
        confirm = raw_input("go on?:")
        page = 'http://steamcommunity.com/id/' + steam_user + '/games/?tab=all'
        print page
        r = swb.get(page)

        site_content = r.text
        # print site_content        
        pattern = '\[{([^\]]+)\]'
        matches = re.findall(pattern,site_content, flags=re.DOTALL)
        result  = matches[0]

        games_json = "[{" + result + "]"
        gamedata = json.loads(games_json)

        #pp = pprint.PrettyPrinter(indent=4)
        #pp.pprint(gamedata)
        
        #create hash of user so we always will have the right user library
        m = hashlib.md5()
        m.update(steam_user)
        steam_user_hash = m.hexdigest()
        
        appid_file = os.path.join(FOLDER_G4G_STEAM_CACHE, steam_user_hash + '.txt')
        fh = open(appid_file, 'w')
        for game in gamedata:
            fh.write(str(game['appid']) + "\n")
        fh.close()
else:
    print "No user given"